<?php

namespace App\Http\Requests\Document;

use App\Models\CampoType;
use App\Models\DocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('documents.update') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $documentId = $this->route('document')->id;
        $canUploadFile = $this->user()?->can('documents.upload') ?? false;

        return [
            'n_documento' => [
                'required',
                'string',
                'max:255',
                Rule::unique('documents', 'n_documento')->ignore($documentId)
            ],
            'asunto' => 'required|string|max:255',
            'root' => $canUploadFile
                ? 'nullable|file|mimes:pdf|max:' . (15 * 1024)
                : 'prohibited',
            'folios' => 'nullable|string|max:255',
            'fecha' => 'required|date',
            'campos' => 'sometimes|array',
            'campos.*.id' => 'required|integer|exists:campo_types,id',
            'campos.*.dato' => 'nullable',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $fields = $this->input('campos', []);
            if (!is_array($fields) || empty($fields)) {
                return;
            }

            $documentTypeId = (int) ($this->route('document')?->document_type_id ?? 0);
            if ($documentTypeId <= 0) {
                return;
            }

            $allowedCampoIds = DocumentType::query()
                ->whereKey($documentTypeId)
                ->first()?->campoTypes()
                ->pluck('campo_types.id')
                ->map(fn($id) => (int) $id)
                ->all() ?? [];

            $allowedLookup = array_fill_keys($allowedCampoIds, true);
            $requiredCampoIds = CampoType::whereIn('id', $allowedCampoIds)
                ->where('is_nullable', false)
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->all();
            $providedCampoIds = collect($fields)
                ->pluck('id')
                ->filter()
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

            foreach ($requiredCampoIds as $requiredCampoId) {
                if (!in_array($requiredCampoId, $providedCampoIds, true)) {
                    $requiredCampo = CampoType::find($requiredCampoId);
                    $requiredName = $requiredCampo?->name ?? 'desconocido';
                    $validator->errors()->add('campos', "Falta enviar el campo obligatorio {$requiredName}.");
                }
            }

            $campoTypeIds = collect($fields)
                ->pluck('id')
                ->filter()
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

            $campoTypes = CampoType::whereIn('id', $campoTypeIds)->get()->keyBy('id');

            foreach ($fields as $index => $field) {
                $campoTypeId = (int) ($field['id'] ?? 0);
                if ($campoTypeId <= 0) {
                    continue;
                }

                if (!isset($allowedLookup[$campoTypeId])) {
                    $validator->errors()->add("campos.$index.id", 'El campo no pertenece al tipo de documento seleccionado.');
                    continue;
                }

                $campoType = $campoTypes->get($campoTypeId);
                if (!$campoType) {
                    continue;
                }

                $value = $field['dato'] ?? null;
                $isEmpty = $value === null || $value === '';

                if (!$campoType->is_nullable && $isEmpty) {
                    $validator->errors()->add("campos.$index.dato", "El campo {$campoType->name} es obligatorio.");
                    continue;
                }

                if ($isEmpty) {
                    continue;
                }

                $this->validateTypedField($validator, $index, $campoType, $value);
            }
        });
    }

    private function validateTypedField($validator, int $index, CampoType $campoType, $value): void
    {
        $dataType = $campoType->data_type ?? 'string';

        if (in_array($dataType, ['string', 'text', 'char'], true)) {
            if (is_array($value)) {
                $validator->errors()->add("campos.$index.dato", "El campo {$campoType->name} debe ser texto.");
                return;
            }

            $stringValue = (string) $value;
            $maxLength = $dataType === 'char' ? ($campoType->length ?: 1) : $campoType->length;
            if ($maxLength && mb_strlen($stringValue) > $maxLength) {
                $validator->errors()->add("campos.$index.dato", "El campo {$campoType->name} no debe exceder {$maxLength} caracteres.");
            }
            return;
        }

        if ($dataType === 'boolean') {
            if (!$this->isValidBooleanValue($value)) {
                $validator->errors()->add("campos.$index.dato", "El campo {$campoType->name} debe ser booleano.");
            }
            return;
        }

        if ($dataType === 'int') {
            if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                $validator->errors()->add("campos.$index.dato", "El campo {$campoType->name} debe ser un numero entero.");
                return;
            }
            $this->validateNumericConstraints($validator, $index, $campoType, (float) $value, (string) $value);
            return;
        }

        if (in_array($dataType, ['float', 'double'], true)) {
            if (!is_numeric($value)) {
                $validator->errors()->add("campos.$index.dato", "El campo {$campoType->name} debe ser numerico.");
                return;
            }
            $this->validateNumericConstraints($validator, $index, $campoType, (float) $value, (string) $value);
            return;
        }

        if ($dataType === 'enum') {
            $options = collect($campoType->enum_values ?? [])
                ->map(fn($option) => trim((string) $option))
                ->filter(fn($option) => $option !== '')
                ->values()
                ->all();

            if (empty($options) || !in_array((string) $value, $options, true)) {
                $validator->errors()->add("campos.$index.dato", "El campo {$campoType->name} tiene un valor no permitido.");
            }
        }
    }

    private function validateNumericConstraints($validator, int $index, CampoType $campoType, float $numericValue, string $rawValue): void
    {
        if (!$campoType->allow_negative && $numericValue < 0) {
            $validator->errors()->add("campos.$index.dato", "El campo {$campoType->name} no permite valores negativos.");
        }

        if (!$campoType->allow_zero && abs($numericValue) < PHP_FLOAT_EPSILON) {
            $validator->errors()->add("campos.$index.dato", "El campo {$campoType->name} no permite el valor 0.");
        }

        if ($campoType->length) {
            $digits = preg_replace('/[^0-9]/', '', $rawValue);
            if (strlen($digits) > $campoType->length) {
                $validator->errors()->add("campos.$index.dato", "El campo {$campoType->name} no debe exceder {$campoType->length} digitos.");
            }
        }
    }

    private function isValidBooleanValue($value): bool
    {
        if (is_bool($value)) {
            return true;
        }

        if (is_int($value)) {
            return in_array($value, [0, 1], true);
        }

        if (is_string($value)) {
            return in_array(strtolower(trim($value)), ['0', '1', 'true', 'false', 'si', 'no', 'yes', 'on', 'off'], true);
        }

        return false;
    }
}

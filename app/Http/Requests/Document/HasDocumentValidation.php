<?php

namespace App\Http\Requests\Document;

use App\Models\CampoType;
use App\Models\DocumentType;

/**
 * Trait HasDocumentValidation
 * 
 * Este trait proporciona una lógica de validación dinámica para los metadatos (campos) 
 * asociados a un documento. Se utiliza para asegurar que los campos enviados por el 
 * frontend correspondan a las definiciones del Tipo de Documento seleccionado.
 */
trait HasDocumentValidation
{
    /**
     * Configura el validador de Laravel añadiendo una validación personalizada "after".
     * Se ejecuta después de las reglas básicas definidas en el FormRequest.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $fields = $this->input('campos', []);
            
            // Si no hay campos que validar, terminamos.
            if (!is_array($fields) || empty($fields)) {
                return;
            }

            // Obtenemos el ID del tipo de documento para saber qué campos son permitidos.
            $documentTypeId = $this->getDocumentTypeId();
            if ($documentTypeId <= 0) {
                return;
            }

            // 1. OBTENER CONFIGURACIÓN PERMITIDA
            // Extraemos los IDs de los campos que legalmente pertenecen a este tipo de documento.
            $allowedCampoIds = DocumentType::query()
                ->whereKey($documentTypeId)
                ->first()?->campoTypes()
                ->pluck('campo_types.id')
                ->map(fn($id) => (int) $id)
                ->all() ?? [];

            $allowedLookup = array_fill_keys($allowedCampoIds, true);

            // 2. VALIDACIÓN DE CAMPOS OBLIGATORIOS
            // Identificamos cuáles de los campos permitidos están marcados como NO nulos (obligatorios).
            $requiredCampoIds = CampoType::whereIn('id', $allowedCampoIds)
                ->where('is_nullable', false)
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->all();

            // IDs de los campos que el usuario realmente envió.
            $providedCampoIds = collect($fields)
                ->pluck('id')
                ->filter()
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

            // Comprobamos que todos los campos requeridos estén presentes en el envío.
            foreach ($requiredCampoIds as $requiredCampoId) {
                if (!in_array($requiredCampoId, $providedCampoIds, true)) {
                    $requiredCampo = CampoType::find($requiredCampoId);
                    $requiredName = $requiredCampo?->name ?? 'desconocido';
                    $validator->errors()->add('campos', "Falta enviar el campo obligatorio {$requiredName}.");
                }
            }

            // 3. VALIDACIÓN DE CONTENIDO Y TIPOS
            // Cargamos las definiciones de tipos para los campos enviados para validar sus reglas (longitud, regex, etc).
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

                // Seguridad: Validar que el campo enviado pertenece al tipo de documento (evita inyección de campos ajenos).
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

                // Validar nulidad si el campo es obligatorio.
                if (!$campoType->is_nullable && $isEmpty) {
                    $validator->errors()->add("campos.$index.dato", "El campo {$campoType->name} es obligatorio.");
                    continue;
                }

                if ($isEmpty) {
                    continue;
                }

                // Delegar la validación específica según el tipo de dato (string, int, enum, etc).
                $this->validateTypedField($validator, $index, $campoType, $value);
            }
        });
    }

    /**
     * Valida el valor del campo basándose en su 'data_type' definido en la base de datos.
     */
    private function validateTypedField($validator, int $index, CampoType $campoType, $value): void
    {
        $dataType = $campoType->data_type ?? 'string';

        // Manejo de cadenas de texto
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

        // Manejo de booleanos (acepta true/false, 1/0, si/no)
        if ($dataType === 'boolean') {
            if (!$this->isValidBooleanValue($value)) {
                $validator->errors()->add("campos.$index.dato", "El campo {$campoType->name} debe ser booleano.");
            }
            return;
        }

        // Manejo de números enteros
        if ($dataType === 'int') {
            if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                $validator->errors()->add("campos.$index.dato", "El campo {$campoType->name} debe ser un numero entero.");
                return;
            }
            $this->validateNumericConstraints($validator, $index, $campoType, (float) $value, (string) $value);
            return;
        }

        // Manejo de números decimales
        if (in_array($dataType, ['float', 'double'], true)) {
            if (!is_numeric($value)) {
                $validator->errors()->add("campos.$index.dato", "El campo {$campoType->name} debe ser numerico.");
                return;
            }
            $this->validateNumericConstraints($validator, $index, $campoType, (float) $value, (string) $value);
            return;
        }

        // Manejo de listas desplegables (Enums)
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

    /**
     * Valida restricciones numéricas avanzadas como negativos, ceros y cantidad de dígitos.
     */
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

    /**
     * Comprueba si un valor es una representación válida de booleano.
     */
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

    /**
     * Obliga a la clase que use este trait a implementar la lógica para obtener el ID 
     * del tipo de documento, necesario para la validación cruzada.
     */
    abstract protected function getDocumentTypeId(): int;
}

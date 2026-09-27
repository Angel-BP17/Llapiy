<?php

namespace App\Http\Requests\Inbox;

use App\Support\PhpIniHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateStorageInboxRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $maxKb = PhpIniHelper::getMaxUploadFileSizeInKilobytes();

        return [
            'n_box' => 'required|integer|exists:boxes,id',
            'n_andamio' => 'required|integer|exists:andamios,id',
            'n_section' => 'required|integer|exists:sections,id',
            'root' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:'.$maxKb,
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $maxFormatted = PhpIniHelper::getMaxUploadFileSizeFormatted();

        return [
            'n_box.required' => 'La caja es obligatoria.',
            'n_box.exists' => 'La caja seleccionada no es válida.',
            'n_andamio.required' => 'El andamio es obligatorio.',
            'n_andamio.exists' => 'El andamio seleccionado no es válido.',
            'n_section.required' => 'La sección es obligatoria.',
            'n_section.exists' => 'La sección seleccionada no es válida.',
            'root.file' => 'El archivo adjunto no es válido o superó el tamaño máximo permitido por el entorno ('.$maxFormatted.').',
            'root.uploaded' => 'El archivo adjunto supera el tamaño máximo permitido por el entorno ('.$maxFormatted.').',
            'root.max' => 'El archivo adjunto supera el tamaño máximo permitido por el entorno ('.$maxFormatted.').',
            'root.mimes' => 'El archivo adjunto debe ser de formato PDF.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $file = $this->file('root');
            if ($file && ! $file->isValid()) {
                if ($file->getError() === UPLOAD_ERR_INI_SIZE || $file->getError() === UPLOAD_ERR_FORM_SIZE) {
                    $validator->errors()->add(
                        'root',
                        'El archivo adjunto supera el tamaño máximo permitido por el entorno ('.PhpIniHelper::getMaxUploadFileSizeFormatted().').'
                    );
                }
            }
        });
    }
}

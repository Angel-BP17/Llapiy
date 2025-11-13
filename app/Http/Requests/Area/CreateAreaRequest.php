<?php

namespace App\Http\Requests\Area;

use Illuminate\Foundation\Http\FormRequest;

class CreateAreaRequest extends FormRequest
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
        return [
            'descripcion' => 'required|string|max:255|unique:areas,descripcion',
            'abreviacion' => 'nullable|string|max:255',
            'grupos' => 'sometimes|array',
            'grupos.*.descripcion' => 'required_with:grupos|string|max:255',
            'grupos.*.abreviacion' => 'nullable|string|max:255',
            'grupos.*.subgrupos' => 'sometimes|array',
            'grupos.*.subgrupos.*.descripcion' => 'required_with:grupos.*.subgrupos|string|max:255'
        ];
    }
}

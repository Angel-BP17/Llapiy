<?php

namespace App\Services\DocumentTypes;

use App\Models\CampoType;
use Illuminate\Http\Request;

class CampoService
{
    public function getIndexData(Request $request): array
    {
        $search = $request->input('search');
        $campos = CampoType::when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', "%$search%")
                ->orWhere('data_type', 'LIKE', "%$search%");
        })
            ->withCount('documentTypes')
            ->paginate(10);

        return compact('campos');
    }

    public function create(array $data): void
    {
        CampoType::create($this->normalizePayload($data));
    }

    public function update(CampoType $campo, array $data): void
    {
        $campo->update($this->normalizePayload($data, $campo));
    }

    public function delete(CampoType $campo): void
    {
        $campo->delete();
    }

    private function normalizePayload(array $data, ?CampoType $campo = null): array
    {
        $dataType = $data['data_type'] ?? $campo?->data_type ?? 'string';
        $isNumericType = in_array($dataType, ['int', 'float', 'double'], true);
        $isEnumType = $dataType === 'enum';

        $enumValues = null;
        if ($isEnumType) {
            $enumValues = collect(preg_split('/[\r\n,]+/', (string) ($data['enum_values'] ?? '')))
                ->map(fn($value) => trim((string) $value))
                ->filter(fn($value) => $value !== '')
                ->unique()
                ->values()
                ->all();
            if (empty($enumValues)) {
                $enumValues = $campo?->enum_values;
            }
        }

        return [
            'name' => $data['name'],
            'data_type' => $dataType,
            'is_nullable' => (bool) ($data['is_nullable'] ?? $campo?->is_nullable ?? true),
            'length' => !empty($data['length']) ? (int) $data['length'] : null,
            'allow_negative' => $isNumericType ? (bool) ($data['allow_negative'] ?? false) : false,
            'allow_zero' => $isNumericType ? (bool) ($data['allow_zero'] ?? true) : true,
            'enum_values' => $isEnumType ? $enumValues : null,
        ];
    }
}

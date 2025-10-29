<?php

namespace App\Service;

use App\Models\SampleType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SampleTypeService
{
    public function validateSampleTypeData(array $data, $sampleTypeId = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    public function createSampleType(array $data)
    {
        $validated = $this->validateSampleTypeData($data);

        return SampleType::create($validated);
    }

    public function updateSampleType(SampleType $sampleType, array $data)
    {
        $validated = $this->validateSampleTypeData($data, $sampleType->id);
        $sampleType->update($validated);

        return $sampleType;
    }

    public function deleteSampleType(SampleType $sampleType)
    {
        $sampleType->delete();
    }

    public function getFilteredSampleTypes(array $filters = [])
    {
        $query = SampleType::query();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('name')->paginate(10);
    }
}

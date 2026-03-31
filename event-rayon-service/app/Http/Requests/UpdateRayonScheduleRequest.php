<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRayonScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rayon_id'   => 'sometimes|required|integer|exists:rayons,id',
            'title'      => 'sometimes|required|string|max:255',
            'description'=> 'nullable|string',
            'location'   => 'sometimes|required|string|max:255',
            'event_date' => 'sometimes|required|date',
            'start_time' => 'sometimes|required|date_format:H:i',
            'end_time'   => 'sometimes|required|date_format:H:i|after:start_time',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => 'Validation failed',
            'errors'  => $validator->errors()
        ], 422));
    }
}
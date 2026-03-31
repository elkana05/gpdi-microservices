<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRayonScheduleRequest extends FormRequest
{
    /**
     * Otorisasi akan kita tangani di Controller melalui pengecekan Role JWT.
     * Jadi di sini kita return true.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi input.
     */
    public function rules(): array
    {
        return [
            'rayon_id'   => 'required|integer|exists:rayons,id',
            'title'      => 'required|string|max:255',
            'description'=> 'nullable|string',
            'location'   => 'required|string|max:255',
            'event_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
        ];
    }

    /**
     * Override response error bawaan Laravel agar 100% 
     * mematuhi aturan Blueprint poin 2.3 (Validation Error).
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => 'Validation failed',
            'errors'  => $validator->errors()
        ], 422));
    }
}
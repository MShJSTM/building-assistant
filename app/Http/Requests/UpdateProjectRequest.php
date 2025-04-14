<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
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
            'name' => 'string|max:255',
            'project_type' => 'string',
            'status' => 'string',
            'address' => 'string',
            'postal_code' => 'string|max:255',
            'land_area' => 'numeric',
            'building_area' => 'numeric',
            'structure_type' => 'string|max:255',
            'start_date' => 'date',
            'end_date' => 'date|after_or_equal:start_date',
            'permit_start_date' => 'date',
            'permit_end_date' => 'date|after_or_equal:permit_start_date',
            'images' => 'array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif',
        ];
    }
}

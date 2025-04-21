<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:projects,slug',
            'status' => 'required|string|max:255',
            'project_type' => 'required|string|max:255',
            'address' => 'required',
            'postal_code' => 'required|string|max:255',
            'land_area' => 'required|numeric',
            'building_area' => 'required|numeric',
            'structure_type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'permit_start_date' => 'required|date',
            'permit_end_date' => 'required|date|after_or_equal:permit_start_date',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif',
        ];
    }
}

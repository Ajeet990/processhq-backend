<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class CreateModuleRequest extends FormRequest
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
            'description' => 'required|string|max:255',
            'status' => 'required|in:0,1',
            'slug' => 'required|string|max:255|unique:modules,slug',
            'icon' => 'nullable|string|max:255',
            'parent_id' => 'nullable|integer|exists:modules,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Module name is required',
            'name.string' => 'Module name must be a string',
            'description.required' => 'Description is required',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be either 0 or 1',
            'slug.required' => 'Slug is required',
            'slug.unique' => 'This slug already exists. Plese choose another slug',
            'slug.string' => 'Slug must be a string',
            'icon.string' => 'Icon must be a string',
            'parent_id.exists' => 'Parent ID must exist in the modules table',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors()
        ], 422));
    }
}

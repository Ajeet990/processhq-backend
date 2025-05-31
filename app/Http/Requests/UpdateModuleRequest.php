<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class UpdateModuleRequest extends FormRequest
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
        $moduleId = $this->route('id'); // Assuming your route parameter is named 'id'

        return [
            // 'id' => [
            //     'required',
            //     'integer',
            //     'exists:modules,id'
            // ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('modules', 'name')->ignore($moduleId)
            ],
            'description' => 'required|string|max:255',
            'status' => 'required|string|in:0,1',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('modules', 'slug')->ignore($moduleId)
            ],
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

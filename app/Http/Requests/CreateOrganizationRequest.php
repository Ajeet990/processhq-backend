<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class CreateOrganizationRequest extends FormRequest
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
            'username' => 'required|string|max:255|unique:organizations,username',
            'email' => 'required|email|max:255|unique:organizations,email',
            'password' => 'required|string',
            'address' => 'required|string|max:255',
            'phone' => 'required|digits:10',
            'url' => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Organization name is required',
            'username.required' => 'Username is required',
            'email.required' => 'Email is required',
            'password.required' => 'password is required',
            'password.string' => 'password must be string',
            'address.required' => 'Address is required',
            'phone.required' => 'Phone number is required',
            'url.required' => 'URL is required',
            'status.required' => 'Status is required',
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

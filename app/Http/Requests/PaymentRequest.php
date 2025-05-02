<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'course_id' => 'required|exists:courses,course_id',
            'payment_method' => 'required|string|in:paymob,vodafone_cash,cash',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:2',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'course_id.required' => 'The course is required.',
            'course_id.exists' => 'The selected course is invalid.',
            'payment_method.required' => 'The payment method is required.',
            'payment_method.in' => 'The selected payment method is invalid.',
            'first_name.required' => 'The first name is required.',
            'last_name.required' => 'The last name is required.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'phone_number.required' => 'The phone number is required.',
            'street.required' => 'The street address is required.',
            'city.required' => 'The city is required.',
            'country.required' => 'The country is required.',
            'state.required' => 'The state/province is required.',
            'postal_code.required' => 'The postal code is required.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // If the user is logged in, pre-fill some fields
        if (auth()->check()) {
            $user = auth()->user();
            
            $this->merge([
                'first_name' => $this->first_name ?? $user->first_name ?? '',
                'last_name' => $this->last_name ?? $user->last_name ?? '',
                'email' => $this->email ?? $user->email ?? '',
                'phone_number' => $this->phone_number ?? $user->phone ?? '',
            ]);
        }
    }
}

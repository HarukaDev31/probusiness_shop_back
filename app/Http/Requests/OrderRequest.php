<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderRequest extends FormRequest
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
            'email' => 'required|email',
            'fullName' => 'required|string|max:255',
            'dni' => 'sometimes|nullable|string|max:255',
            'documentType' => 'required|string|in:factura,boleta',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'ruc' => 'sometimes|nullable|string|max:255',
            'businessName' => 'required_if:documentType,factura|string|max:255',
            'city' => 'required|string|max:255',
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'El campo email es obligatorio',
            'fullName.required' => 'El campo nombre completo es obligatorio',
            'dni.required' => 'El campo dni es obligatorio',
            'documentType.required' => 'El campo tipo de documento es obligatorio',
            'phone.required' => 'El campo telefono es obligatorio',
            'address.required' => 'El campo direccion es obligatorio',
            'ruc.required' => 'El campo ruc es obligatorio',
            'businessName.required_if' => 'El campo nombre de empresa es obligatorio cuando el tipo de documento es factura',
            'city.required' => 'El campo ciudad es obligatorio',
            'items.required' => 'El campo items es obligatorio',
            'items.*.id.required' => 'El campo id del item es obligatorio',
            'items.*.quantity.required' => 'El campo cantidad del item es obligatorio',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['error' => 'Validation failed', 'errors' => $validator->errors()], 422)
        );
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'dni' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|max:255',
            'whatsapp' => 'sometimes|string|max:20',
            'edad' => 'sometimes|integer|min:1|max:120',
            'sexo' => 'sometimes|in:Masculino,Femenino',
            'departamento_id' => 'sometimes|integer|exists:departamento,Id_Departamento',
            'provincia_id' => 'sometimes|integer|exists:provincia,Id_Provincia',
            'distrito_id' => 'sometimes|integer|exists:distrito,Id_Distrito',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.string' => 'El nombre debe ser una cadena de texto',
            'name.max' => 'El nombre no puede exceder 255 caracteres',
            'dni.string' => 'El DNI debe ser una cadena de texto',
            'dni.max' => 'El DNI no puede exceder 20 caracteres',
            'email.email' => 'El email debe tener un formato válido',
            'email.unique' => 'El email ya está en uso',
            'whatsapp.string' => 'El WhatsApp debe ser una cadena de texto',
            'whatsapp.max' => 'El WhatsApp no puede exceder 20 caracteres',
            'edad.integer' => 'La edad debe ser un número entero',
            'edad.min' => 'La edad mínima es 1',
            'edad.max' => 'La edad máxima es 120',
            'sexo.in' => 'El sexo debe ser Masculino o Femenino',
            'departamento_id.exists' => 'El departamento seleccionado no existe',
            'provincia_id.exists' => 'La provincia seleccionada no existe',
            'distrito_id.exists' => 'El distrito seleccionado no existe',
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
            response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422)
        );
    }
} 
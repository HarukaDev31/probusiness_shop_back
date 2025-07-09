<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class NewOrderRequest extends FormRequest
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
            'customer' => 'required|array',
            'customer.fullName' => 'required|string|max:255',
            'customer.dni' => 'required|string|max:20',
            'customer.email' => 'required|email|max:255',
            'customer.phone' => 'required|string|max:20',
            'customer.address' => 'required|array',
            'customer.address.province' => 'required|string|max:255',
            'customer.address.city' => 'required|string|max:255',
            'customer.address.district' => 'required|string|max:255',
            
            'order' => 'required|array',
            'order.items' => 'required|array|min:1',
            'order.items.*.productId' => 'required|integer|exists:catalogo_producto,id',
            'order.items.*.name' => 'required|string|max:255',
            'order.items.*.price' => 'required|numeric|min:0',
            'order.items.*.quantity' => 'required|integer|min:1',
            'order.items.*.total' => 'required|numeric|min:0',
            'order.items.*.image' => 'nullable|url|max:500',
            'order.total' => 'required|numeric|min:0',
            'order.orderNumber' => 'required|string|max:20',
            'order.orderDate' => 'required|date',
            'order.status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
            
            'metadata' => 'nullable|array',
            'metadata.source' => 'nullable|string|max:50',
            'metadata.userAgent' => 'nullable|string|max:500',
            'metadata.timestamp' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'customer.required' => 'La información del cliente es obligatoria',
            'customer.fullName.required' => 'El nombre completo del cliente es obligatorio',
            'customer.dni.required' => 'El DNI del cliente es obligatorio',
            'customer.email.required' => 'El email del cliente es obligatorio',
            'customer.email.email' => 'El email del cliente debe tener un formato válido',
            'customer.phone.required' => 'El teléfono del cliente es obligatorio',
            'customer.address.required' => 'La dirección del cliente es obligatoria',
            'customer.address.province.required' => 'La provincia es obligatoria',
            'customer.address.city.required' => 'La ciudad es obligatoria',
            'customer.address.district.required' => 'El distrito es obligatorio',
            
            'order.required' => 'La información de la orden es obligatoria',
            'order.items.required' => 'Los items de la orden son obligatorios',
            'order.items.min' => 'La orden debe tener al menos un item',
            'order.items.*.productId.required' => 'El ID del producto es obligatorio',
            'order.items.*.productId.exists' => 'El producto especificado no existe',
            'order.items.*.name.required' => 'El nombre del producto es obligatorio',
            'order.items.*.price.required' => 'El precio del producto es obligatorio',
            'order.items.*.price.numeric' => 'El precio debe ser un número',
            'order.items.*.price.min' => 'El precio debe ser mayor a 0',
            'order.items.*.quantity.required' => 'La cantidad es obligatoria',
            'order.items.*.quantity.integer' => 'La cantidad debe ser un número entero',
            'order.items.*.quantity.min' => 'La cantidad debe ser mayor a 0',
            'order.items.*.total.required' => 'El total del item es obligatorio',
            'order.items.*.total.numeric' => 'El total debe ser un número',
            'order.items.*.total.min' => 'El total debe ser mayor a 0',
            'order.items.*.image.url' => 'La imagen debe ser una URL válida',
            'order.total.required' => 'El total de la orden es obligatorio',
            'order.total.numeric' => 'El total debe ser un número',
            'order.total.min' => 'El total debe ser mayor a 0',
            'order.orderNumber.required' => 'El número de orden es obligatorio',
            'order.orderDate.required' => 'La fecha de orden es obligatoria',
            'order.orderDate.date' => 'La fecha de orden debe ser una fecha válida',
            'order.status.required' => 'El estado de la orden es obligatorio',
            'order.status.in' => 'El estado de la orden debe ser válido',
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
                'error' => 'Validation failed', 
                'errors' => $validator->errors()
            ], 422)
        );
    }
} 
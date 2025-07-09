<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class ProductRequest extends FormRequest
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
            // === INFORMACIÓN BÁSICA DEL PRODUCTO ===
            'img' => 'required|url|max:500',
            'description' => 'required|string|max:1000',
            'price' => 'required|string|max:100',
            'company' => 'required|string|max:255',
            'product_url' => 'required|url|max:500',
            'min_order' => 'required|string|max:100',
            
            // === URL DEL DETALLE DE ALIBABA ===
            'alibaba_detail_url' => 'required|url|max:500',
            
            // === DESCRIPCIÓN DETALLADA ===
            'detailed_description_text' => 'nullable|string',
            'detailed_description_html' => 'nullable|string',
            
            // === CONTENIDO DEL IFRAME ===
            'iframe_content_text' => 'nullable|string',
            'iframe_content_html' => 'nullable|string',
            'iframe_content_images' => 'nullable|array|max:15',
            'iframe_content_images.*' => 'url|max:500',
            
            // === PRECIOS (ESTRUCTURA DE ESCALERA) ===
            'prices' => 'required|array|min:1',
            'prices.*.quantity' => 'required|string|max:100',
            'prices.*.price' => 'required|string|max:100',
            
            // === ATRIBUTOS DEL PRODUCTO ===
            'attributes' => 'required|array',
            'attributes.*' => 'string|max:255',
            
            // === INFORMACIÓN DE EMBALAJE ===
            'packaging_info' => 'required|array',
            'packaging_info.*' => 'string|max:255',
            
            // === IMÁGENES (MÁXIMO 15) ===
            'images' => 'required|array|min:1|max:15',
            'images.*' => 'required|url|max:500',
            
            // === INFORMACIÓN DEL PRODUCTO ORIGINAL ===
            'original_product_id' => 'nullable|integer',
            
            // === INFORMACIÓN DEL PROVEEDOR ===
            'supplier_name' => 'required|string|max:255',
            
            // === CAMPOS ADICIONALES ===
            'delivery_lead_times' => 'nullable|array',
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
            // === INFORMACIÓN BÁSICA DEL PRODUCTO ===
            'img.required' => 'La imagen principal es requerida',
            'img.url' => 'La imagen principal debe ser una URL válida',
            'description.required' => 'La descripción del producto es requerida',
            'price.required' => 'El precio es requerido',
            'company.required' => 'El nombre de la empresa es requerido',
            'product_url.required' => 'La URL del producto es requerida',
            'product_url.url' => 'La URL del producto debe ser válida',
            'min_order.required' => 'El pedido mínimo es requerido',
            
            // === URL DEL DETALLE DE ALIBABA ===
            'alibaba_detail_url.required' => 'La URL del detalle de Alibaba es requerida',
            'alibaba_detail_url.url' => 'La URL del detalle de Alibaba debe ser válida',
            
            // === PRECIOS ===
            'prices.required' => 'Los precios son requeridos',
            'prices.array' => 'Los precios deben ser un array',
            'prices.min' => 'Debe haber al menos un precio',
            'prices.*.quantity.required' => 'La cantidad es requerida para cada precio',
            'prices.*.price.required' => 'El precio es requerido para cada entrada',
            
            // === ATRIBUTOS ===
            'attributes.required' => 'Los atributos del producto son requeridos',
            'attributes.array' => 'Los atributos deben ser un array',
            
            // === INFORMACIÓN DE EMBALAJE ===
            'packaging_info.required' => 'La información de embalaje es requerida',
            'packaging_info.array' => 'La información de embalaje debe ser un array',
            
            // === IMÁGENES ===
            'images.required' => 'Las imágenes son requeridas',
            'images.array' => 'Las imágenes deben ser un array',
            'images.min' => 'Debe haber al menos una imagen',
            'images.max' => 'No puede haber más de 15 imágenes',
            'images.*.required' => 'Cada imagen es requerida',
            'images.*.url' => 'Cada imagen debe ser una URL válida',
            
            // === INFORMACIÓN DEL PROVEEDOR ===
            'supplier_name.required' => 'El nombre del proveedor es requerido',
            
            // === CAMPOS ADICIONALES ===
            'category_id.required' => 'La categoría es requerida',
            'category_id.exists' => 'La categoría seleccionada no existe',
            
            // === CONTENIDO DEL IFRAME ===
            'iframe_content_images.max' => 'No puede haber más de 15 imágenes en el iframe',
            'iframe_content_images.*.url' => 'Cada imagen del iframe debe ser una URL válida',
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
    public function failedValidation(Validator $validator)
    {
        Log::error('Error de validación: ' . json_encode($validator->errors()));
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Error de validación',
            'errors' => $validator->errors()
        ], 422));
    }
}

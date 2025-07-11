<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function me(Request $request)
    {
        try {
            // Obtener el token del header
            $token = $request->header('Authorization');
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token de autorización requerido'
                ], 401);
            }

            // Remover 'Bearer ' del token si está presente
            $token = str_replace('Bearer ', '', $token);

            // Obtener el usuario por el token
            $user = DB::table('users')
                ->where('api_token', $token)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token inválido'
                ], 401);
            }

            // Obtener datos de ubicación
            $departamento = null;
            $provincia = null;
            $distrito = null;

            if ($user->departamento_id) {
                $departamento = DB::table('departamento')
                    ->where('Id_Departamento', $user->departamento_id)
                    ->value('No_Departamento');
            }

            if ($user->provincia_id) {
                $provincia = DB::table('provincia')
                    ->where('Id_Provincia', $user->provincia_id)
                    ->value('No_Provincia');
            }

            if ($user->distrito_id) {
                $distrito = DB::table('distrito')
                    ->where('Id_Distrito', $user->distrito_id)
                    ->value('No_Distrito');
            }

            // Calcular edad
            $edad = $user->edad;

            return response()->json([
                'success' => true,
                'data' => [
                    'nombre' => $user->name,
                    'dni' => $user->dni,
                    'email' => $user->email,
                    'whatsapp' => $user->whatsapp,
                    'edad' => $edad,
                    'sexo' => $user->sexo,
                    'departamento' => $user->departamento_id        ,
                    'provincia' => $user->provincia_id,
                    'distrito' => $user->distrito_id
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error obteniendo datos del usuario', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    public function update(UpdateUserRequest $request)
    {
        try {
            // Obtener el token del header
            $token = $request->header('Authorization');
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token de autorización requerido'
                ], 401);
            }

            // Remover 'Bearer ' del token si está presente
            $token = str_replace('Bearer ', '', $token);

            // Obtener el usuario por el token
            $user = DB::table('users')
                ->where('api_token', $token)
                ->first();
                Log::info(json_encode($user));

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token inválido'
                ], 401);
            }

            // Obtener solo los campos que se enviaron en la request
            $updateData = $request->only([
                'name', 'dni', 'email', 'whatsapp', 
                'edad', 'sexo', 
                'departamento_id', 'provincia_id', 'distrito_id'
            ]);
            
            // Validar unicidad del email si se está actualizando
            if (isset($updateData['email'])) {
                $existingUser = DB::table('users')
                    ->where('email', $updateData['email'])
                    ->where('id', '!=', $user->id)
                    ->first();
                
                if ($existingUser) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error de validación',
                        'errors' => ['email' => ['El email ya está en uso']]
                    ], 422);
                }
            }
            
            // Agregar timestamp de actualización
            $updateData['updated_at'] = now();
            Log::info(json_encode($user));
            // Actualizar usuario
            DB::table('users')
                ->where('id', $user->id)
                ->update($updateData);

            Log::info('Usuario actualizado exitosamente', [
                'user_id' => $user->id,
                'updated_fields' => array_keys($updateData)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Datos del usuario actualizados correctamente'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error actualizando datos del usuario', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }
} 
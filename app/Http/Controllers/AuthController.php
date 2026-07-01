<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validar los datos que vienen de React
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Buscar al usuario
        $user = User::where('email', $request->email)->first();

        // Verificar la contraseña
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Generar token de Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                // Roles y permisos para que el Front pueda ocultar/mostrar botones
                'roles' => $user->getRoleNames(), 
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ]
        ]);
    }

    // Método opcional para cerrar sesión y destruir el token
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}

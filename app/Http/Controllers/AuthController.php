<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Helpers\PermissionHelper;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        // Verificar si existe y si la contraseña coincide
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Las credenciales ingresadas son incorrectas.'
            ], 401);
        }

        // Verificar si el usuario debe cambiar su contraseña
        if ($user->change_pass_next_login) {
            // Genera un token temporal con ability limitado a solo cambiar contraseña
            $tempToken = $user->createToken('password-change', ['password-change'])->plainTextToken;

            return response()->json([
                'requires_password_change' => true,
                'temp_token' => $tempToken,
                'token_type' => 'Bearer',
                'message' => 'Debe cambiar su contraseña antes de continuar.',
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                ],
            ]);
        }

        // Verificar que el usuario esté activo
        if (! $user->status) {
            return response()->json([
                'message' => 'Su cuenta ha sido desactivada. Contacte al administrador.'
            ], 403);
        }

        // Genera token con Sanctum e inyecta roles/permisos
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->employee
                    ? trim($user->employee->first_name . ' ' . $user->employee->last_name)
                    : $user->username,
                'username' => $user->username,
                'email' => $user->employee?->email,
                'departmentId' => $user->employee?->department->id ?? null,
                'roles' => $user->getRoleNames(),
                'roleName' => $user->employee?->roleSnapshot?->role_name ?? null,
                // Usuario de emergencia recibe permisos FIJOS sobre empleados.
                // Resto de usuarios: permisos del role_snapshot.
                'permissions' => $user->username === env('EMERGENCY_USER')
                    ? PermissionHelper::emergencyEmployeePermissions()
                    : ($user->employee?->roleSnapshot?->permissions ?? []),
                'departments' => $user->employee?->roleSnapshot?->departments ?? [],
            ]
        ]);
    }

    /**
     * Cuando change_pass_next_login = true.
     * Cambiar contraseña
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        // Verificar que la nueva contraseña sea diferente a la actual
        if (Hash::check($request->new_password, $user->password)) {
            return response()->json([
                'message' => 'La nueva contraseña no puede ser igual a la actual.'
            ], 422);
        }

        // Actualizar contraseña y desmarcar el flag
        $user->password = $request->new_password;
        $user->change_pass_next_login = false;
        $user->save();

        // Revocar todos los tokens temporales de password-change
        $user->tokens()->where('name', 'password-change')->delete();

        // Generar un token de acceso normal
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'message' => 'Contraseña actualizada correctamente.',
            'user' => [
                'id' => $user->id,
                'name' => $user->employee
                    ? trim($user->employee->first_name . ' ' . $user->employee->last_name)
                    : $user->username,
                'username' => $user->username,
                'email' => $user->employee?->email,
                'departmentId' => $user->employee?->department->id ?? null,
                'roles' => $user->getRoleNames(),
                'roleName' => $user->employee->roleSnapshot->role_name,
                'permissions' => $user->employee->roleSnapshot->permissions ?? [],
                'departments' => $user->employee->roleSnapshot->departments ?? [],
            ]
        ]);
    }

    // Cerrar sesión y destruir el token
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status'  => 'success',
            'message' => 'Sesión cerrada correctamente'
        ], 200);
    }
}

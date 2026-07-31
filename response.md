Formato de respuesta backend en cada endpoint:

    {
        "status":    "ok | fail",
        "code":      "created_role",
        "message":   "Rol creado exitosamente",
        "data":      { ... },       // opcional
        "timestamp": "2026-07-23T..."
    }

Usar helper ApiResponseHelper, ejemplo:

    return ApiResponseHelper::createResponse(
        'ok',
        'created_role',
        'Rol creado exitosamente'
    );
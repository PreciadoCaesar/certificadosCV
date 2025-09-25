<?php
// auth/Middleware.php
namespace Auth;

class Middleware {
    public static function checkAuth(string $requiredRole): void {
        $user = Auth::user();
        if (empty($user['id']) || $user['role'] !== $requiredRole) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode([ 'error' => 'Acceso no autorizado' ]);
            exit;
        }
    }

    /** Permite cualquiera de los roles pasados en el array */
    public static function checkAny(array $roles): void {
        $user = Auth::user();
        if (empty($user['id']) || !in_array($user['role'], $roles, true)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode([ 'error' => 'Acceso no autorizado' ]);
            exit;
        }
    }
}
?>
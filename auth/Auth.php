<?php
// auth/Auth.php
namespace Auth;

class Auth {
    public static function user(): array {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return [
            'id'   => $_SESSION['ID_administrador'] ?? ($_SESSION['ID_gerente'] ?? null),
            'role' => $_SESSION['rol'] ?? null,
        ];
    }

    public static function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: ' . \Conectar::ruta());
        exit;
    }
}

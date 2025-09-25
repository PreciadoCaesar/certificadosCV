<?php
require_once __DIR__ . '/../../config/conexion.php';

session_unset();
session_destroy();

header('Location: ' . BASE_URL);
exit;

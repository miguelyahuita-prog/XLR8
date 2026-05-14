<?php
declare(strict_types=1);

const DB_HOST = 'localhost';
const DB_NAME = 'flores_alesli';
const DB_USER = 'root';
const DB_PASS = '';

function conectarBaseDatos(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        die('No se pudo conectar con MySQL. Revisa que XAMPP este activo y que exista la base "' . DB_NAME . '".');
    }

    return $pdo;
}

function e(?string $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
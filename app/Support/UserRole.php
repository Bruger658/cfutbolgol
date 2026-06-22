<?php

namespace App\Support;

final class UserRole
{
    public const ADMIN = 'admin';
    public const EDITOR = 'editor';
    public const TESORERO = 'tesorero';
    public const COORDINADOR = 'coordinador';
    public const SOCIO = 'socio';

    public const LABELS = [
        self::ADMIN => 'Administrador',
        self::EDITOR => 'Editor de contenido',
        self::TESORERO => 'Tesorero',
        self::COORDINADOR => 'Coordinador deportivo',
        self::SOCIO => 'Socio',
    ];

    public const PERMISSIONS = [
        'access-dashboard' => [self::ADMIN, self::EDITOR, self::TESORERO, self::COORDINADOR],
        'manage-content' => [self::ADMIN, self::EDITOR],
        'manage-store' => [self::ADMIN, self::TESORERO],
        'manage-members' => [self::ADMIN, self::COORDINADOR, self::TESORERO],
        'manage-fees' => [self::ADMIN, self::TESORERO],
        'manage-enrollments' => [self::ADMIN, self::COORDINADOR],
        'manage-staff' => [self::ADMIN, self::COORDINADOR],
    ];

    public static function values(): array
    {
        return array_keys(self::LABELS);
    }
}
<?php

declare(strict_types=1);

namespace App\Enums;

enum Permission: string
{
    case UsersView = 'users.view';
    case UsersCreate = 'users.create';
    case UsersUpdate = 'users.update';
    case UsersDelete = 'users.delete';

    case ReportsView = 'reports.view';
    case ReportsCreate = 'reports.create';
    case ReportsUpdate = 'reports.update';
    case ReportsDelete = 'reports.delete';
    case ReportsSearchFilter = 'reports.search_filter';
    case ReportsRecap = 'reports.recap';
    case ReportsExport = 'reports.export';

    case AuditLogView = 'audit_log.view';
    case ProfileUpdate = 'profile.update';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return list<self>
     */
    public static function forRole(UserRole $role): array
    {
        return match ($role) {
            UserRole::SuperAdmin => self::cases(),
            UserRole::Admin => [
                self::UsersView,
                self::UsersCreate,
                self::UsersUpdate,
                self::UsersDelete,
                self::ReportsView,
                self::ReportsCreate,
                self::ReportsUpdate,
                self::ReportsDelete,
                self::ReportsSearchFilter,
                self::ReportsRecap,
                self::ReportsExport,
                self::AuditLogView,
            ],
            UserRole::User => [
                self::ProfileUpdate,
                self::ReportsView,
                self::ReportsCreate,
                self::ReportsUpdate,
                self::ReportsDelete,
            ],
        };
    }
}

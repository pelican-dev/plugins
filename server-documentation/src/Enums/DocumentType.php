<?php

declare(strict_types=1);

namespace Starter\ServerDocumentation\Enums;

/**
 * Document type hierarchy (highest to lowest permission level):
 * - HostAdmin: Root Admin only
 * - ServerAdmin: Server owners + admins with Server Update/Create
 * - ServerMod: Subusers with control permissions
 * - Player: Anyone with server access
 */
enum DocumentType: string
{
    case HostAdmin = 'host_admin';
    case ServerAdmin = 'server_admin';
    case ServerMod = 'server_mod';
    case Player = 'player';

    /**
     * Legacy type for backwards compatibility.
     * Maps to ServerAdmin.
     */
    public const LEGACY_ADMIN = 'admin';

    /**
     * Get the display label for this type.
     */
    public function label(): string
    {
        return match ($this) {
            self::HostAdmin => trans('server-documentation::strings.types.host_admin'),
            self::ServerAdmin => trans('server-documentation::strings.types.server_admin'),
            self::ServerMod => trans('server-documentation::strings.types.server_mod'),
            self::Player => trans('server-documentation::strings.types.player'),
        };
    }

    /**
     * Get the description for this type.
     */
    public function description(): string
    {
        return match ($this) {
            self::HostAdmin => trans('server-documentation::strings.types.host_admin_description'),
            self::ServerAdmin => trans('server-documentation::strings.types.server_admin_description'),
            self::ServerMod => trans('server-documentation::strings.types.server_mod_description'),
            self::Player => trans('server-documentation::strings.types.player_description'),
        };
    }

    /**
     * Get the Filament color for this type.
     */
    public function color(): string
    {
        return match ($this) {
            self::HostAdmin => 'danger',
            self::ServerAdmin => 'warning',
            self::ServerMod => 'info',
            self::Player => 'success',
        };
    }

    /**
     * Get the icon for this type.
     */
    public function icon(): string
    {
        return match ($this) {
            self::HostAdmin => 'tabler-shield-lock',
            self::ServerAdmin => 'tabler-lock',
            self::ServerMod => 'tabler-user-shield',
            self::Player => 'tabler-file-text',
        };
    }

    /**
     * Get the hierarchy level (higher = more privileged).
     */
    public function hierarchyLevel(): int
    {
        return match ($this) {
            self::HostAdmin => 4,
            self::ServerAdmin => 3,
            self::ServerMod => 2,
            self::Player => 1,
        };
    }

    /**
     * Check if this type is visible to a given hierarchy level.
     */
    public function isVisibleToLevel(int $level): bool
    {
        return $level >= $this->hierarchyLevel();
    }

    /**
     * Get all types visible to a given hierarchy level.
     *
     * @return array<string>
     */
    public static function typesVisibleToLevel(int $level): array
    {
        $types = [];
        foreach (self::cases() as $case) {
            if ($case->isVisibleToLevel($level)) {
                $types[] = $case->value;
            }
        }

        if ($level >= self::ServerAdmin->hierarchyLevel()) {
            $types[] = self::LEGACY_ADMIN;
        }

        return $types;
    }

    /**
     * Try to create from a string, handling legacy values.
     */
    public static function tryFromLegacy(string $value): ?self
    {
        if ($value === self::LEGACY_ADMIN) {
            return self::ServerAdmin;
        }

        return self::tryFrom($value);
    }

    /**
     * Check if a string is a valid document type (including legacy).
     */
    public static function isValid(string $value): bool
    {
        return self::tryFromLegacy($value) !== null;
    }

    /**
     * Get options array for form selects.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label() . ' (' . $case->description() . ')';
        }

        return $options;
    }

    /**
     * Get options array with just labels (no descriptions).
     *
     * @return array<string, string>
     */
    public static function simpleOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    /**
     * Format a type string for display, handling legacy values.
     */
    public static function formatLabel(string $value): string
    {
        $type = self::tryFromLegacy($value);

        return $type?->label() ?? $value;
    }

    /**
     * Get color for a type string, handling legacy values.
     */
    public static function formatColor(string $value): string
    {
        $type = self::tryFromLegacy($value);

        return $type?->color() ?? 'gray';
    }

    /**
     * Get icon for a type string, handling legacy values.
     */
    public static function formatIcon(string $value): string
    {
        $type = self::tryFromLegacy($value);

        return $type?->icon() ?? 'tabler-file-text';
    }
}

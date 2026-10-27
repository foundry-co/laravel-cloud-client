<?php

namespace FoundryCo\LaravelCloudClient\Data;

use Carbon\CarbonImmutable;

final class Cast
{
    public static function string(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }

    public static function nullableString(mixed $value): ?string
    {
        return is_scalar($value) ? (string) $value : null;
    }

    public static function int(mixed $value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }

    public static function nullableInt(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    public static function float(mixed $value): float
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }

    public static function nullableFloat(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    public static function bool(mixed $value): bool
    {
        return is_scalar($value) && filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public static function nullableBool(mixed $value): ?bool
    {
        return is_scalar($value) ? filter_var($value, FILTER_VALIDATE_BOOLEAN) : null;
    }

    public static function array(mixed $value): array
    {
        return match (true) {
            is_array($value) => $value,
            $value === null => [],
            default => [$value],
        };
    }

    public static function nullableArray(mixed $value): ?array
    {
        return $value === null ? null : self::array($value);
    }

    public static function date(mixed $value): ?CarbonImmutable
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    public static function enum(string $enum, mixed $value): ?\BackedEnum
    {
        if (! is_scalar($value)) {
            return null;
        }

        if ((string) (new \ReflectionEnum($enum))->getBackingType() === 'int') {
            return is_numeric($value) ? $enum::tryFrom((int) $value) : null;
        }

        return $enum::tryFrom((string) $value);
    }

    public static function relation(array $relationships, string $name): ?string
    {
        $id = $relationships[$name]['data']['id'] ?? null;

        return is_scalar($id) ? (string) $id : null;
    }

    public static function relations(array $relationships, string $name): array
    {
        $items = $relationships[$name]['data'] ?? [];

        return is_array($items) ? array_values(array_filter(array_map(fn ($item) => is_array($item) ? ($item['id'] ?? null) : null, $items))) : [];
    }
}

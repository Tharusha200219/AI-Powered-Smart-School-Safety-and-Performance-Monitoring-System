<?php

namespace App\Enums;

enum Gender: string
{
    case MALE = 'M';
    case FEMALE = 'F';
    case OTHER = 'Other';

    public static function getValues(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function getValidationRule(): string
    {
        return 'in:' . implode(',', self::getValues());
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::MALE => 'Male',
            self::FEMALE => 'Female',
            self::OTHER => 'Other',
        };
    }
}

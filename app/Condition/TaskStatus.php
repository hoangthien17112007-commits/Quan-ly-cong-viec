<?php

namespace App\Condition;

enum TaskStatus: string
{
    case TODO = 'todo';
    case DONE = 'done';

    public function label(): string
    {
        return match ($this) {
            self::TODO => 'Chưa xong',
            self::DONE => 'Hoàn thành',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TODO => 'zinc',
            self::DONE => 'green',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
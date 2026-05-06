<?php

namespace App\Condition;

enum ProjectStatus: string
{
    case PLANNING = 'planning';
    case IN_PROGRESS = 'in_progress';
    case ON_HOLD = 'on_hold';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PLANNING    => 'Lên kế hoạch',
            self::IN_PROGRESS => 'Đang thực hiện',
            self::ON_HOLD     => 'Tạm dừng',
            self::COMPLETED   => 'Hoàn thành',
            self::CANCELLED   => 'Đã huỷ',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PLANNING    => 'zinc',
            self::IN_PROGRESS => 'blue',
            self::ON_HOLD     => 'orange',
            self::COMPLETED   => 'green',
            self::CANCELLED   => 'red',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
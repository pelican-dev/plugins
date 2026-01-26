<?php

namespace Boy132\Tickets\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TicketStatus: string implements HasColor, HasIcon, HasLabel
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Closed = 'closed';

    public function getIcon(): string
    {
        return match ($this) {
            self::Open => 'tabler-circle-dashed',
            self::InProgress => 'tabler-progress',
            self::Closed => 'tabler-circle-check',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Open => 'primary',
            self::InProgress => 'success',
            self::Closed => 'danger',
        };
    }

    public function getLabel(): string
    {
        return str($this->value)->headline();
    }
}

<?php

declare(strict_types=1);

namespace App\Enum;

enum TransactionType: string
{
    case INCOMING = 'incoming';
    case OUTGOING = 'outgoing';
}

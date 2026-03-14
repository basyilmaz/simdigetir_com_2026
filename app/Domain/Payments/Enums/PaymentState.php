<?php

namespace App\Domain\Payments\Enums;

enum PaymentState: string
{
    case Pending = 'pending';
    case Authorized = 'authorized';
    case Succeeded = 'succeeded';
    case Failed = 'failed';
    case Refunded = 'refunded';
    case Cancelled = 'cancelled';
}


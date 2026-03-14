<?php

namespace App\Domain\Orders\Enums;

enum OrderState: string
{
    case Draft = 'draft';
    case PendingPayment = 'pending_payment';
    case Paid = 'paid';
    case Assigned = 'assigned';
    case PickedUp = 'picked_up';
    case Delivered = 'delivered';
    case Closed = 'closed';
    case Cancelled = 'cancelled';
    case Failed = 'failed';
}


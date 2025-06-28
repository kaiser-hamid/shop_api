<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PAID = 'paid';
    case UNPAID = 'unpaid';
    case FAILED = 'failed'; //for payment gateway
    case REFUNDED = 'refunded';
}
<?php

namespace App;

enum EmailDeliveryStatus: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case FAILED = 'failed';
    case BOUNCED = 'bounced';
}

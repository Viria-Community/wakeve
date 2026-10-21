<?php

namespace App\Enums;

enum CompanyStatus: string
{
    case Pending = 'pending';
    case Validated = 'validated';
    case Rejected = 'rejected';
    case Active = 'active';
    case Suspended = 'suspended';
}

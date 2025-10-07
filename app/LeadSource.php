<?php

namespace App;

enum LeadSource: string
{
    case WEBSITE = 'website';
    case FORM = 'form';
    case API = 'api';
    case MANUAL = 'manual';
    case REFERRAL = 'referral';
    case OTHER = 'other';
}

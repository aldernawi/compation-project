<?php

namespace App\Enums;

enum EvaluationStatus: string
{
    case Pending = 'pending';
    case Evaluated = 'evaluated';
    case NeedsReview = 'needs_review';
}

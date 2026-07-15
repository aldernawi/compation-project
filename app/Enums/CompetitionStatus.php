<?php

namespace App\Enums;

enum CompetitionStatus: string
{
    case Upcoming = 'upcoming';
    case Open = 'open';
    case Closed = 'closed';
    case UnderEvaluation = 'under_evaluation';
    case Finished = 'finished';
}

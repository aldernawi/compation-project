<?php

namespace App\Enums;

enum SubmissionKind: string
{
    case Image = 'image';
    case Pdf = 'pdf';
    case Video = 'video';
    case Text = 'text';
    case Link = 'link';
}

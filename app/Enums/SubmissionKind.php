<?php

namespace App\Enums;

enum SubmissionKind: string
{
    case Image = 'image';
    case Pdf = 'pdf';
    case Video = 'video';
    case Text = 'text';
    case Link = 'link';

    /**
     * Validation rules for the uploaded file matching this submission kind.
     *
     * @return array<int, string>
     */
    public function fileRules(): array
    {
        return match ($this) {
            self::Image => ['mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
            self::Pdf => ['mimes:pdf', 'max:20480'],
            self::Video => ['mimes:mp4,mov,avi,webm', 'max:102400'],
            self::Text, self::Link => [],
        };
    }
}

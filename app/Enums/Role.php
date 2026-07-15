<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Organizer = 'organizer';
    case Judge = 'judge';
    case Participant = 'participant';
}

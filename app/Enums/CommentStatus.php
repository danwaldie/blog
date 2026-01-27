<?php

declare(strict_types=1);

namespace App\Enums;

enum CommentStatus: string
{
    case Submitted = 'submitted';
    case Published = 'published';
    case Rejected = 'rejected';
}

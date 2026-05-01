<?php

namespace App\Models;

use App\Traits\HasUniqueUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    use HasFactory, HasUniqueUuid;

    protected $table = 'resume';

    protected $primaryKey = 'id';

    protected $fillable = [
        'filename',
        'candidate_name',
        'candidate_email',
        'candidate_phone',
        'score',
        'technical_score',
        'match_score',
        'summary',
        'skills',
        'category',
        'processing_time_ms',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
        ];
    }
}

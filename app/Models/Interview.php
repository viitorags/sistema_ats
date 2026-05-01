<?php

namespace App\Models;

use App\Traits\HasUniqueUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory, HasUniqueUuid;

    protected $primaryKey = 'id';

    const UPDATED_AT = null;

    protected $fillable = [
        'summary',
        'description',
        'location',
        'start_time',
        'end_time',
        'event_link',
        'status',
        'user_id',
    ];
}

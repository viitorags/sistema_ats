<?php

namespace App\Models;

use App\Traits\HasUniqueUuid;
use Illuminate\Database\Eloquent\Model;

class Interviews extends Model
{
    use HasUniqueUuid;

    protected $primaryKey = "id";

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

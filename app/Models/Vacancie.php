<?php

namespace App\Models;

use App\Traits\HasUniqueUuid;
use Illuminate\Database\Eloquent\Model;

class Vacancie extends Model
{
    use HasUniqueUuid;

    protected $primaryKey = 'id';

    protected $fillable = [
        'title',
        'description',
        'location',
        'is_remote',
        'active',
        'user_id'
    ];
}

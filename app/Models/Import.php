<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    use HasFactory;
    protected static $unguarded = true;

    protected $casts = [
        'completed_at' => 'datetime'
    ];
}

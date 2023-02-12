<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    use HasFactory;
    protected static $unguarded = true;

    protected $casts = [
        'completed_at' => 'datetime'
    ];

    public function scopeInProgress(Builder $builder)
    {
         $builder->whereNull('completed_at');
    }

    public function scopeForModel(Builder $builder, string $model)
    {
        $builder->whereModel($model);
    }
}

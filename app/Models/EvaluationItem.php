<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'sequence',
        'category',
        'year'
    ];

    protected $casts = [
        'sequence' => 'integer',
        'year' => 'integer'
    ];
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationItem extends Model
{
    protected $fillable = ['title', 'type', 'sequence', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

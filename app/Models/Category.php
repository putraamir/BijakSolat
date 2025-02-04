<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'year_id'];
    protected $with = ['evaluationItems'];

    public function evaluationItems()
    {
        return $this->hasMany(EvaluationItem::class)->orderBy('sequence');
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }
}

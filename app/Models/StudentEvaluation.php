<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentEvaluation extends Model
{
    protected $fillable = ['student_id', 'evaluation_item_id', 'status'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function evaluationItem()
    {
        return $this->belongsTo(EvaluationItem::class);
    }
}

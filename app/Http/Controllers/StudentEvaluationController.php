<?php

namespace App\Http\Controllers;

use App\Models\StudentEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentEvaluationController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|exists:students,id',
                'scores' => 'required|array'
            ]);

            DB::beginTransaction();

            foreach ($request->scores as $itemId => $status) {
                StudentEvaluation::updateOrCreate(
                    [
                        'student_id' => $request->student_id,
                        'evaluation_item_id' => $itemId
                    ],
                    ['status' => $status]
                );
            }

            DB::commit();
            return redirect()->back()->with('success', 'Evaluation saved successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to save evaluation']);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TeacherClass;

class TeacherController extends Controller
{
    public function updateClasses(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'classes' => 'required|array',
            'classes.*.year' => 'required|integer|min:1|max:6',
            'classes.*.class_name' => 'required|in:Cemerlang,Gemilang'
        ]);

        $teacher = User::findOrFail($request->teacher_id);

        // Delete existing classes
        $teacher->teacherClasses()->delete();

        // Add new classes
        foreach ($request->classes as $class) {
            $teacher->teacherClasses()->create([
                'year' => $class['year'],
                'class_name' => $class['class_name']
            ]);
        }

        return redirect()->back()
            ->with('message', 'Kelas guru telah dikemaskini');
    }
}

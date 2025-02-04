<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email',
            'classes' => 'array'
        ]);

        $teacher = Teacher::create([
            'name' => $validated['name'],
            'email' => $validated['email']
        ]);

        if (!empty($validated['classes'])) {
            $teacher->classes()->sync($validated['classes']);
        }

        return redirect()->back();
    }

    public function update(Request $request, Teacher $teacher)
    {
        try {
            $validated = $request->validate([
                'classes' => 'array'  // Remove 'required' rule
            ]);

            // Handle empty classes array
            $teacher->classes()->sync($validated['classes'] ?? []);

            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to update teacher']);
        }
    }

    public function destroy(Teacher $teacher)
    {
        try {
            $teacher->classes()->detach();
            $teacher->delete();
            return redirect()->back()->with('success', 'Teacher deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete teacher']);
        }
    }
}

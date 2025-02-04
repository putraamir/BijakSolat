<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('students')->where(function ($query) use ($request) {
                    return $query->where('class_id', $request->class_id);
                })
            ],
            'class_id' => 'required|exists:class_rooms,id'
        ]);

        Student::create($validated);
        return redirect()->back();
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->back();
    }

    public function import(Request $request)
    {
        try {
            Log::info('Import started', ['request' => $request->all()]);

            $request->validate([
                'file' => 'required|file|mimes:csv,txt',
                'class_id' => 'required|exists:class_rooms,id'
            ]);

            if (!$request->hasFile('file')) {
                Log::error('No file in request');
                throw new \Exception('No file uploaded');
            }

            $file = $request->file('file');
            Log::info('File details', [
                'originalName' => $file->getClientOriginalName(),
                'path' => $file->getPathname(),
                'size' => $file->getSize()
            ]);

            // Read file contents directly
            $contents = file_get_contents($file->getPathname());
            if ($contents === false) {
                throw new \Exception('Failed to read file contents');
            }

            $rows = explode("\n", $contents);
            array_shift($rows); // Remove header

            DB::beginTransaction();
            $existingNames = Student::where('class_id', $request->class_id)
                ->pluck('name')
                ->toArray();
            $duplicates = [];
            $added = [];

            foreach ($rows as $row) {
                $name = trim($row);
                if (!empty($name)) {
                    if (in_array($name, $existingNames) || in_array($name, $added)) {
                        $duplicates[] = $name;
                        continue;
                    }
                    Student::create([
                        'name' => $name,
                        'class_id' => $request->class_id
                    ]);
                    $added[] = $name;
                }
            }

            DB::commit();

            if (count($duplicates) > 0) {
                return redirect()->back()->with([
                    'warning' => 'Some students were skipped (duplicate names): ' . implode(', ', $duplicates),
                    'success' => count($added) . ' students were added successfully'
                ]);
            }

            return redirect()->back()->with('success', 'Students imported successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Achievement;

class TeacherDashboardController extends Controller
{
    public function getStats()
    {
        $teacher = Auth::user();

        $classIds = $teacher->classes()->pluck('id')->toArray();

        $students = Student::whereIn('class_id', $classIds)->get();

        $achievements = Achievement::whereHas('student', function($query) use ($classIds) {
                $query->whereIn('class_id', $classIds);
            })
            ->get()
            ->groupBy('type');

        $achievementStats = $achievements->map(function ($items) {
            return [
                'passed' => $items->where('status', 'passed')->count(),
                'failed' => $items->where('status', 'failed')->count(),
            ];
        });

        return response()->json([
            'studentCount' => $students->count(),
            'classCount' => count($classIds),
            'passedCount' => $achievementStats->sum('passed'),
            'failedCount' => $achievementStats->sum('failed'),
            'achievements' => [
                'amaliWuduk' => $achievementStats->get('wuduk', ['passed' => 0, 'failed' => 0]),
                'amaliSolat' => $achievementStats->get('solat', ['passed' => 0, 'failed' => 0]),
                'bacaan' => $achievementStats->get('bacaan', ['passed' => 0, 'failed' => 0]),
                'tahfiz' => $achievementStats->get('tahfiz', ['passed' => 0, 'failed' => 0]),
            ]
        ]);
    }
}

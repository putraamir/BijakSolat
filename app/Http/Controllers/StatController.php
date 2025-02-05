<?php
namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Student;
use App\Models\EvaluationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class StatsController extends Controller
{
    public function fetch(Request $request)
    {
        try {
            $studentsQuery = Student::query()
                ->when($request->class !== 'all', function ($q) use ($request) {
                    $q->where('class_id', $request->class);
                })
                ->whereHas('class', function ($q) use ($request) {
                    $q->where('year_id', $request->year);
                });
            $totalStudents = $studentsQuery->count();
            // Overall stats calculation
            $totalNotEvaluated = (clone $studentsQuery)
                ->whereDoesntHave('evaluations')
                ->count();
            $totalFailed = (clone $studentsQuery)
                ->whereHas('evaluations', function ($q) {
                    $q->where('status', 'not_passed');
                })
                ->count();
            $totalPassed = $totalStudents - ($totalFailed + $totalNotEvaluated);
            // For category summary
            $categoryStats = [];
            // For detailed view
            $categoryDetails = [];
            $categories = Category::where('year_id', $request->year)
                ->with('evaluationItems')
                ->get();
            foreach ($categories as $category) {
                $itemIds = $category->evaluationItems->pluck('id');
                // Summary stats per category
                $passedCount = (clone $studentsQuery)
                    ->whereHas('evaluations', function ($q) use ($itemIds) {
                        $q->whereIn('evaluation_item_id', $itemIds)
                            ->where('status', 'passed');
                    }, '=', count($itemIds))
                    ->count();
                $failedCount = (clone $studentsQuery)
                    ->whereHas('evaluations', function ($q) use ($itemIds) {
                        $q->whereIn('evaluation_item_id', $itemIds)
                            ->where('status', 'not_passed');
                    })
                    ->count();
                $notEvaluatedCount = $totalStudents - ($passedCount + $failedCount);
                $categoryStats[] = [
                    'name' => $category->name,
                    'passed' => $passedCount,
                    'failed' => $failedCount,
                    'notEvaluated' => $notEvaluatedCount
                ];
                // Detailed stats per item
                $itemStats = [];
                foreach ($category->evaluationItems as $item) {
                    // Count passed students for this item
                    $passedCount = (clone $studentsQuery)
                        ->whereHas('evaluations', function ($q) use ($item) {
                            $q->where('evaluation_item_id', $item->id)
                                ->where('status', 'passed');
                        })
                        ->count();
                    // Count failed students for this item
                    $failedCount = (clone $studentsQuery)
                        ->whereHas('evaluations', function ($q) use ($item) {
                            $q->where('evaluation_item_id', $item->id)
                                ->where('status', 'not_passed');
                        })
                        ->count();
                    // Calculate not evaluated
                    $notEvaluatedCount = $totalStudents - ($passedCount + $failedCount);
                    $itemStats[] = [
                        'name' => $item->title,
                        'passed' => $passedCount,
                        'failed' => $failedCount,
                        'notEvaluated' => $notEvaluatedCount
                    ];
                }
                $categoryDetails[$category->name] = [
                    'labels' => collect($itemStats)->pluck('name'),
                    'datasets' => [
                        [
                            'label' => 'Lulus',
                            'backgroundColor' => '#10B981',
                            'data' => collect($itemStats)->pluck('passed')
                        ],
                        [
                            'label' => 'Belum Lulus',
                            'backgroundColor' => '#EF4444',
                            'data' => collect($itemStats)->pluck('failed')
                        ],
                        [
                            'label' => 'Belum Disemak',
                            'backgroundColor' => '#6366F1',
                            'data' => collect($itemStats)->pluck('notEvaluated')
                        ]
                    ]
                ];
            }
            return response()->json([
                'total' => $totalStudents,
                'passed' => $totalPassed,
                'failed' => $totalFailed,
                'notEvaluated' => $totalNotEvaluated,
                'categoryStats' => $categoryStats,
                'categoryDetails' => $categoryDetails
            ]);
        } catch (\Exception $e) {
            Log::error('Stats fetch error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

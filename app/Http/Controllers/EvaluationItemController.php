<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\EvaluationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class EvaluationItemController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'category' => 'required|string',
                'categoryId' => 'nullable|exists:categories,id',
                'year_id' => 'required|exists:years,id',
                'items' => 'required|array',
                'items.*.title' => 'required|string',
                'items.*.type' => 'required|in:RUKUN,SUNAT,WAJIB',
                'items.*.sequence' => 'required|integer'
            ]);

            DB::beginTransaction();

            if (!empty($validated['categoryId'])) {
                $category = Category::findOrFail($validated['categoryId']);
            } else {
                $category = Category::create([
                    'name' => $validated['category'],
                    'year_id' => $validated['year_id']
                ]);
            }

            $sequence = $category->evaluationItems()->count() + 1;
            foreach ($validated['items'] as $item) {
                $category->evaluationItems()->create([
                    'title' => $item['title'],
                    'type' => $item['type'],
                    'sequence' => $sequence++
                ]);
            }

            DB::commit();
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to add evaluation items']);
        }
    }

    public function importCsv(Request $request)
    {
        try {
            $request->validate([
                'csv_file' => 'required|mimes:csv,txt',
                'year' => 'required|exists:years,id'
            ]);

            DB::beginTransaction();

            $file = $request->file('csv_file');
            $content = file_get_contents($file->getPathname());
            $content = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $content);
            $lines = explode("\n", trim($content));

            $csvData = array_map(function ($line) {
                return str_getcsv(trim($line));
            }, $lines);

            $headers = array_map('trim', array_shift($csvData));
            $expectedHeaders = ['title', 'type', 'sequence', 'category'];

            if (array_diff($expectedHeaders, $headers)) {
                throw new \Exception('Invalid CSV format. Required columns: ' . implode(', ', $expectedHeaders));
            }

            $categorizedItems = [];
            foreach ($csvData as $row) {
                if (empty($row) || count($row) !== count($headers)) continue;

                $data = array_combine($headers, array_map('trim', $row));
                $categoryName = $data['category'];

                if (!isset($categorizedItems[$categoryName])) {
                    $categorizedItems[$categoryName] = [];
                }

                $categorizedItems[$categoryName][] = [
                    'title' => $data['title'],
                    'type' => $data['type'],
                    'sequence' => (int)$data['sequence']
                ];
            }

            foreach ($categorizedItems as $categoryName => $items) {
                $category = Category::firstOrCreate([
                    'name' => $categoryName,
                    'year_id' => $request->year
                ]);

                foreach ($items as $item) {
                    $category->evaluationItems()->create($item);
                }
            }

            DB::commit();
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function clearYear($yearId)
    {
        try {
            DB::beginTransaction();

            // Delete all categories for year (will cascade to evaluation items)
            Category::where('year_id', $yearId)->delete();

            DB::commit();
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(EvaluationItem $item)
    {
        try {
            $categoryId = $item->category_id;
            $item->delete();

            // Check if category is empty
            $remainingItems = EvaluationItem::where('category_id', $categoryId)->count();
            if ($remainingItems === 0) {
                Category::find($categoryId)->delete();
            }

            return redirect()->back()->with('success', 'Item deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete item']);
        }
    }
}

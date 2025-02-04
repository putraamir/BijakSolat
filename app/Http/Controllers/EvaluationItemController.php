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
                'file' => 'required|mimes:csv,txt',
                'year' => 'required|exists:years,id'
            ]);

            DB::beginTransaction();

            $file = $request->file('file');
            $content = file_get_contents($file->getPathname());
            $rows = explode("\n", trim($content));

            // Clean and parse header
            $headers = str_getcsv(array_shift($rows));
            $headers = array_map(function ($header) {
                return trim($header, " \t\n\r\0\x0B\"");
            }, $headers);

            // Process rows
            $categorizedItems = [];
            foreach ($rows as $row) {
                if (empty(trim($row))) continue;

                $values = str_getcsv($row);

                // Validate row has same number of columns as header
                if (count($values) !== count($headers)) {
                    Log::warning('Skipping invalid row: ' . $row);
                    continue;
                }

                $data = array_combine($headers, $values);
                $categoryName = trim($data['category'], '"');

                if (!isset($categorizedItems[$categoryName])) {
                    $categorizedItems[$categoryName] = [];
                }

                $categorizedItems[$categoryName][] = [
                    'title' => trim($data['title'], '"'),
                    'type' => trim($data['type'], '"'),
                    'sequence' => (int)trim($data['sequence'], '"')
                ];
            }

            // Create categories and items
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
            return redirect()->back()->with('success', 'CSV imported successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CSV Import Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to import CSV: ' . $e->getMessage()]);
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

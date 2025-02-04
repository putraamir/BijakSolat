<?php

namespace App\Http\Controllers;

use App\Models\EvaluationItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EvaluationItemController extends Controller
{
    public function index(Request $request)
    {
        $items = EvaluationItem::orderBy('category')
            ->orderBy('sequence')
            ->get()
            ->groupBy('category');

        // For debugging
        \Log::info('Items being passed to view:', ['items' => $items]);

        return Inertia::render('EvaluationObject', [
            'items' => $items
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'type' => 'required|in:RUKUN,SUNAT',
            'sequence' => 'required|integer',
            'category' => 'required|string',
            'year' => 'required|integer'
        ]);

        EvaluationItem::create($validated);

        return redirect()->back();
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt'
            ]);

            $file = $request->file('file');
            $handle = fopen($file->getPathname(), 'r');

            // Skip header row
            $headers = fgetcsv($handle);

            \DB::beginTransaction();

            while (($data = fgetcsv($handle)) !== false) {
                EvaluationItem::create([
                    'title' => $data[0],
                    'type' => strtoupper($data[1]),
                    'sequence' => $data[2],
                    'category' => $data[3],
                    'year' => $data[4],
                ]);
            }

            fclose($handle);
            \DB::commit();

            return back()->with('success', 'Import successful');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors(['error' => 'Import failed: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            EvaluationItem::destroy($id);
            return back()->with('success', 'Item deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Delete failed']);
        }
    }
}

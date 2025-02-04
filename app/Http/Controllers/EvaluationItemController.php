<?php
namespace App\Http\Controllers;

use App\Models\EvaluationItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EvaluationItemController extends Controller
{
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
            return back()->with('success', 'Items imported successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}

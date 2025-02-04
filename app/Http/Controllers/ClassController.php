<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClassController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year_id' => 'required|exists:years,id'
        ]);

        ClassRoom::create($validated);

        return redirect()->back();
    }

    public function index($year)
    {
        $classes = ClassRoom::where('year_id', $year)->get();

        return Inertia::render('ClassPage', [
            'year' => $year,
            'classes' => $classes
        ]);
    }
}

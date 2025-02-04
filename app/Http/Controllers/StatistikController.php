<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class StatistikController extends Controller
{
    public function index()
    {
        $overallStats = [
            1 => [
                'total' => 50,
                'passed' => 35,
                'notPassed' => 15,
                'byClass' => [
                    'Cemerlang' => ['total' => 25, 'passed' => 20, 'notPassed' => 5],
                    'Gemilang' => ['total' => 25, 'passed' => 15, 'notPassed' => 10]
                ]
            ],
            2 => [
                'total' => 48,
                'passed' => 40,
                'notPassed' => 8,
                'byClass' => [
                    'Cemerlang' => ['total' => 24, 'passed' => 22, 'notPassed' => 2],
                    'Gemilang' => ['total' => 24, 'passed' => 18, 'notPassed' => 6]
                ]
            ]
        ];

        $categoryStats = [
            1 => [
                'Amali Wuduk' => ['passed' => 40, 'notPassed' => 10],
                'Amali Solat' => ['passed' => 35, 'notPassed' => 15],
                'Bacaan' => ['passed' => 30, 'notPassed' => 20],
                'Tahfiz' => ['passed' => 25, 'notPassed' => 25]
            ]
        ];

        return Inertia::render('Statistik', [
            'overallStats' => $overallStats,
            'categoryStats' => $categoryStats
        ]);
    }
}

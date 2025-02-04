<?php
// app/Imports/EvaluationItemsImport.php
namespace App\Imports;

use App\Models\EvaluationItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EvaluationItemsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            EvaluationItem::create([
                'title' => $row['title'],
                'type' => strtoupper($row['type']),
                'sequence' => $row['sequence'],
                'category' => $row['category'],
                'year' => $row['year'],
            ]);
        }
    }
}

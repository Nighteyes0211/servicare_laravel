<?php 

namespace App\Imports;

use App\Models\Article;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;

class ArticlesImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public function model(array $row)
    {
        
        ini_set('max_execution_time', 900); // 900 seconds (15 minutes)
        $is_exists = Article::where('article_number', $row['artikelnummer'])->exists();
        if (!$is_exists) {
            
            $artikelnummer = ($row['artikelnummer'] !='' || $row['artikelnummer'] != NULL) ? $row['artikelnummer']: '';
            $beschreibung = ($row['beschreibung'] !='' || $row['beschreibung'] != NULL) ? $row['beschreibung']: '';

            return new Article([
                'article_number'  => $artikelnummer,
                'description' => $beschreibung,
            ]);
        } else {
            return null;
        }

       
    }

    public function batchSize(): int
    {
        return 1000; // Insert 1000 rows at a time
    }

    public function chunkSize(): int
    {
        return 1000; // Read 1000 rows at a time
    }
}

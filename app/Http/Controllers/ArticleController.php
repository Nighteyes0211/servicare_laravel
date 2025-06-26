<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use ZipArchive;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ArticlesImport;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::paginate(15);
        return view('articles.index', compact('articles'));
    }

    public function create()
    {
        return view('articles.create');
    }

    public function showImportForm()
    {
        return view('articles.import');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'article_number' => 'required|string',
            'description' => 'required',
        ]);

        Article::create($validated);

        return redirect()->route('articles.index')->with('success', 'Artikel erfolgreich erstellt.');
    }

    public function import(Request $request)
    {
        ini_set('max_execution_time', 900); // 900 seconds (15 minutes)
        $validated = $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:2048',
        ]);

        Excel::import(new ArticlesImport, $request->file('file'));


        return back()->with('success', 'Excel file imported successfully!');
        /*
        if ($_FILES['file']['error'] == 0) {
            $file = $_FILES['file']['tmp_name'];
            $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            
            if ($extension === 'csv') {
                $data = $this->importCSV($file);
            } elseif ($extension === 'xlsx') {
                $data = $this->importXLSX($file);
            } else {
                return redirect()->back()->with('error', 'Invalid file format!');
            }

            if (!empty($data)) {
                $insertData = [];

                foreach ($data as $detail) {
                    $is_exists = Article::where('article_number', $detail['Artikelnummer'])->exists();
                    if (!$is_exists) {
                        $insertData[] = [
                            'article_number' => $detail['Artikelnummer'],
                            'description' => $detail['Beschreibung'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }

                // 🔹 **Daten in 1000er-Chunks aufteilen und in die Datenbank schreiben**
                $chunks = array_chunk($insertData, 1000);
                foreach ($chunks as $chunk) {
                    Article::insert($chunk);
                }
            }

            return redirect()->back()->with('success', 'Artikel erfolgreich importiert.');
        } 
        return redirect()->back()->with('error', 'File upload error!');
        */
    }


    // Function to import CSV file and return key-value array
    function importCSV($file) {
        $handle = fopen($file, 'r');
        $header = fgetcsv($handle, 1000, ","); // Read header row
        $data = [];

        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $data[] = array_combine($header, $row); // Map header to values
        }
        fclose($handle);
        
        return $data;
    }

    // Function to import XLSX file and return key-value array
    function importXLSX($file) {
        $zip = new ZipArchive;
        if ($zip->open($file) !== TRUE) {
            die("Failed to open XLSX file!");
        }

        $xml = simplexml_load_string($zip->getFromName('xl/sharedStrings.xml'));
        $sharedStrings = [];
        foreach ($xml->si as $s) {
            $sharedStrings[] = (string) $s->t;
        }

        $xml = simplexml_load_string($zip->getFromName('xl/worksheets/sheet1.xml'));
        $rows = [];
        foreach ($xml->sheetData->row as $row) {
            $rowData = [];
            foreach ($row->c as $cell) {
                $value = isset($cell->v) ? (string) $cell->v : '';
                if (isset($cell['t']) && $cell['t'] == 's') {
                    $value = $sharedStrings[$value];
                }
                $rowData[] = $value;
            }
            $rows[] = $rowData;
        }
        $zip->close();

        $header = array_shift($rows); // Extract header row
        $data = [];
        foreach ($rows as $row) {
            $data[] = array_combine($header, $row); // Map header to values
        }

        return $data;
    }
    public function show(Article $article)
    {
        return view('articles.show', compact('article'));
    }

    public function edit(Article $article)
    {
        return view('articles.edit', compact('article'));
    }

   public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'article_number' => 'required|string',
            'description' => 'required',
        ]);

        $article->update($validated);

        return redirect()->route('articles.index')->with('success', 'Artikel erfolgreich aktualisiert.');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Artikel erfolgreich gelöscht');
    }

    public function ArticleData($id) {
        $article = Article::find($id);
    
        if (!$article) {
            return response()->json(['error' => 'Article not found'], 404);
        }
    
        return response()->json($article);
    }
}

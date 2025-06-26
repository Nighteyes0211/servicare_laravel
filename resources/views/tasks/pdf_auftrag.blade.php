<!-- resources/views/tasks/pdf-task.blade.php -->
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Auftrag</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .section-title { font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Auftragsdetails</h1>

    <p><strong>Titel:</strong> {{ $task->title }}</p>
    <p><strong>Mitarbeiter:</strong> {{ $task->user?->name ?? 'Nicht zugewiesen' }}</p>
    <h2>Kunde</h2>
<p>
    {{ $task->customer->company_name }}<br>
    {{ $task->customer->street }}<br>
    {{ $task->customer->postal_code }} {{ $task->customer->city }}
</p>


@if ($task->contact)
    <p>
        <strong>Ansprechpartner:</strong><br>
        {{ $task->contact->salutation }} {{ $task->contact->first_name }} {{ $task->contact->last_name }}<br>
        @if ($task->contact->phone) Telefon: {{ $task->contact->phone }}<br>@endif
        @if ($task->contact->mobile) Mobil: {{ $task->contact->mobile }}<br>@endif
        @if ($task->contact->email) E-Mail: {{ $task->contact->email }}<br>@endif
    </p>
@endif
    


    <p><strong>Beschreibung:</strong><br>{{ $task->comment }}</p>
    <p><strong>Status:</strong> {{ $task->status }}</p>
    <p><strong>Zeitraum:</strong> {{ $task->start_time }} bis {{ $task->end_time }}</p>



<p><strong>Erstellt am:</strong>
    {{ $task->created_at ? $task->created_at->format('d.m.Y H:i') : '-' }}
</p>

<p><strong>Erstellt von:</strong>
    {{ $task->user?->name ?? 'Nicht zugewiesen' }}
</p>



</body>


<style type="text/css">
    <style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
        line-height: 1.6;
        padding: 20px;
    }

    h1 {
        font-size: 22px;
        margin-bottom: 20px;
    }

    h2 {
        font-size: 18px;
        margin-top: 30px;
        margin-bottom: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table, th, td {
        border: 1px solid #444;
    }

    th, td {
        padding: 8px;
        text-align: left;
    }
</style>



</html>




<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Servicebericht - Both & Wandless</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #000;
        }
        h1 {
            font-size: 20px;
            margin-bottom: 10px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin: 30px 0 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #999;
            padding: 6px;
            vertical-align: top;
            word-wrap: break-word;
        }
        th {
            background-color: #eee;
            font-weight: bold;
        }
        pre {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            padding: 10px;
            white-space: pre-wrap;
        }
        .entry-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<h1>Servicebericht – Both & Wandless</h1>

<p><strong>Titel:</strong> {{ $modal_title ?? '-' }}</p>
<p><strong>Mitarbeiter:</strong> {{ $user ?? '-' }}</p>
<p><strong>Kunde:</strong> {{ $customer->company_name ?? '-' }}</p>
<p><strong>Anschrift:</strong> {{ $customer->street ?? '' }}, {{ $customer->postal_code ?? '' }} {{ $customer->city ?? '' }}</p>
<p><strong>Beschreibung:</strong> {{ $modal_description ?? '-' }}</p>

<div class="section-title">Allgemeine Informationen</div>
<table>
    <tr><th>Auftraggeber</th><td>{{ $service->auftraggeber ?? '-' }}</td></tr>
    <tr><th>Auftragsnummer</th><td>{{ $service->auftr_nr ?? '-' }}</td></tr>
    <tr><th>Kostenstelle</th><td>{{ $service->kostenst ?? '-' }}</td></tr>
    <tr>
        <th>Art der ausgeführten Arbeiten</th>
        <td>
            Reparatur: {{ $service->reparatur ? 'Ja' : 'Nein' }} |
            Wartung: {{ $service->wartung ? 'Ja' : 'Nein' }} |
            Lieferung: {{ $service->auslieferung ? 'Ja' : 'Nein' }}
        </td>
    </tr>
    <tr>
        <th>Prüfung bestanden?</th>
        <td>
            {{ $service->STK_bestanden_ja ? 'Ja' : ($service->STK_bestanden_nein ? 'Nein' : '-') }}
        </td>
    </tr>
    <tr>
        <th>Messwerte</th>
        <td>
            RSL &lt; 0,3Ω: {{ $service->rsl ?? '-' }} |
            ISO &gt; 1MΩ: {{ $service->iso ?? '-' }} |
            IEA &lt; 3,5mA: {{ $service->iea ?? '-' }}
        </td>
    </tr>
</table>

<div class="section-title">Status am Ende der Arbeit</div>
<table>
    <tr>
        <th>Arbeit fertig?</th>
        <td>{{ $service->arbeit_fertig ? 'Ja' : ($service->arbeit_fertig_nein ? 'Nein' : '-') }}</td>
    </tr>
    <tr>
        <th>Funktionstest bestanden?</th>
        <td>{{ $service->funktiontest_besttanden_ja ? 'Ja' : ($service->funktiontest_besttanden_nein ? 'Nein' : '-') }}</td>
    </tr>
    <tr>
        <th>Kostenpflichtig?</th>
        <td>{{ $service->kostenpflichtig ? 'Ja' : ($service->kostenpflichtig_nein ? 'Nein' : '-') }}</td>
    </tr>
</table>

<div class="section-title">Servicebeschreibung</div>
<pre>{{ $service->servicebericht_both_beschreibung ?? '-' }}</pre>

@if (!empty($service_material_consumption))
    <div class="section-title">Materialverbrauch</div>
    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Stück</th>
                <th style="width: 60%;">Beschreibung</th>
                <th style="width: 30%;">Artikel-Nr.</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($service_material_consumption as $item)
                <tr>
                    <td>{{ $item->piece_stück ?? '-' }}</td>
                    <td>{{ $item->Beschreibung ?? '-' }}</td>
                    <td>{{ $item->art_no_nr ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if (!empty($service_travel_details))
    <div style="page-break-before: always;"></div>
    <div class="section-title">Arbeitszeiten & Fahrten</div>
    @foreach ($service_travel_details as $index => $row)
        <div class="entry-box">
            <strong>Eintrag {{ $index + 1 }}</strong><br>
            <strong>Von:</strong> {{ $row->datum_von ?? '-' }} |
            <strong>Bis:</strong> {{ $row->datum_bis ?? '-' }}<br>
            <strong>Anfahrt:</strong> {{ $row->anfahrtzeit_von ?? '-' }} – {{ $row->anfahrtzeit_bis ?? '-' }} ({{ $row->anfahrtzeit_std ?? '-' }})<br>
            <strong>Rückfahrt:</strong> {{ $row->ruckfahrtzeit_von ?? '-' }} – {{ $row->ruckfahrtzeit_bis ?? '-' }} ({{ $row->ruckfahrtzeit_std ?? '-' }})<br>
            <strong>Fahrt km:</strong> Hin {{ $row->fahrt_km_hin ?? '-' }} | Zurück {{ $row->fahrt_km_zurück ?? '-' }}<br>
            <strong>Pauschale Anfahrt:</strong> {{ $row->pausch_anfahrt ?? '-' }}<br>
            <strong>Wartezeit:</strong> {{ $row->wartezeit ?? '-' }}<br>
            <strong>Arbeitszeit:</strong> {{ $row->arbeitszeit ?? '-' }}<br>
            <strong>Ges. Arbeitszeit:</strong> {{ $row->ges_arbeitszeit ?? '-' }}<br>
            <strong>Ges. (Tag):</strong> {{ $row->ges_arbeitszeit_tag ?? '-' }} |
            <strong>Ges. (Monat):</strong> {{ $row->ges_arbeitszeit_monat ?? '-' }}<br>
            <strong>Personenzahl:</strong> {{ $row->personenzahl ?? '-' }}
        </div>
    @endforeach
@endif

<div class="section-title">Abschluss</div>
<table>
    <tr><th>Techniker</th><td>{{ $service->techniker_name ?? '-' }}</td></tr>
    <tr><th>Datum</th><td>{{ $service->sign_date ?? '-' }}</td></tr>
    <tr>
        <th>Unterschrift des Kunden</th>
        <td>
            @if (!empty($service->kunde_name))
                <img src="{{ public_path('storage/' . $service->kunde_name) }}" style="max-height: 100px;">
            @else
                -
            @endif
        </td>
    </tr>
</table>

</body>
</html>

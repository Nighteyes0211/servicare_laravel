<!-- SERVICEBERICHT WISSNER MODAL -->

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }
    .container {
        width: 90%;
        margin: 20px auto;
        border: 1px solid #000;
        padding: 10px;
    }
    .header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .title {
        font-weight: bold;
    }
    .input-row {
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .checkbox-group {
        margin: 10px 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    table th, table td {
        border: 1px solid #000;
        padding: 5px;
        text-align: center;
    }
    .signature-section {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }
    .signature-container {
        width: 45%;
    }
    .signature-pad {
        border: 1px solid #000;
        width: 100%;
        height: 170px;
    }
    .clear-button {
        margin-top: 5px;
    }
    input[type="text"], input[type="number"], input[type="date"], select {
        width: 90%;
        padding: 5px;
        margin-bottom: 5px;
    }
    textarea {
        width: 100%;
        height: 80px;
    }
    .custom-textarea {
        width: 100%;
        height: 100px;
        padding: 5px;
    }
</style>

<div class="modal fade" id="serviceModal1" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="container">
                    <div class="header">
                        <div class="title">Service-Bescheinigung und Stundennachweis</div>
                    </div>
                    <div class="modal-body">
                        <form id="service-record-form"
                            action="{{ isset($first_service) ? route('service-records.update', $first_service->id) : route('service-records.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <meta name="csrf-token" content="{{ csrf_token() }}">
                            <input type="hidden" name="request_source" value="form">
                            <input type="hidden" name="form_type" value="1">
                            <!-- Hidden Fields for task_id, user_id, customer_id -->
                            <input type="hidden" name="task_id" id="task_id" value="{{ $task->id ?? '' }}">
                            <input type="hidden" name="user_id" id="user_id" value="{{ auth()->id() ?? '' }}">
                            <input type="hidden" name="customer_id" id="customer_id" value="{{ $customer->id ?? '' }}">

                            <div class="input-row">
                                <label>Auftraggeber: <input type="text" name="auftraggeber"
                                        value="{{ old('auftraggeber', $first_service->auftraggeber ?? '') }}" required></label>
                            </div>
                            <div class="input-row">
                                <label>Ansprechpartner: <input type="text" name="ansprechpartner"
                                        value="{{ old('ansprechpartner', $first_service->ansprechpartner ?? '') }}" required></label>
                                <label>Telefon: <input type="text" name="telefon"
                                        value="{{ old('telefon', $first_service->telefon ?? '') }}"></label>
                            </div>
                            <div class="input-row">
                                <label>Service-Beleg-Nr.: <input type="text" name="service_beleg_nr"
                                        value="{{ old('service_beleg_nr', $first_service->service_beleg_nr ?? '') }}" required></label>
                                <label>AB-Nr.: <input type="text" name="ab_nr"
                                        value="{{ old('ab_nr', $first_service->ab_nr ?? '') }}" required></label>
                                <label>Rekla/SA-Nr.: <input type="text" name="rekla_sa_nr"
                                        value="{{ old('rekla_sa_nr', $first_service->rekla_sa_nr ?? '') }}" required></label>
                                <label>Debit-Nr.: <input type="text" name="debit_nr"
                                        value="{{ old('debit_nr', $first_service->debit_nr ?? '') }}" required></label>
                            </div>

                            <div class="checkbox-group">
                                <strong>Reklamation</strong>:
                                <label><input type="checkbox" name="reparatur" value="1"
                                        o{{ old('reparatur', $first_service->reparatur ?? false) ? 'checked' : '' }}>

                                    Reparatur</label>
                                <label><input type="checkbox" name="rep_aufnahme" value="1"
                                        {{ old('rep_aufnahme', $first_service->rep_aufnahme ?? '') == 1 ? 'checked' : '' }}>
                                    Rep.-Aufnahme</label>
                                <label><input type="checkbox" name="wartung" value="1"
                                        {{ old('wartung', $first_service->wartung ?? '') == 1 ? 'checked' : '' }}>
                                    Wartung/STK/BSC</label>
                                <label><input type="checkbox" name="schulung" value="1"
                                        {{ old('schulung', $first_service->schulung ?? '') == 1 ? 'checked' : '' }}>
                                    Schulung</label>
                                <label><input type="checkbox" name="auslieferung" value="1"
                                        {{ old('auslieferung', $first_service->auslieferung ?? '') == 1 ? 'checked' : '' }}>
                                    Auslieferung</label>
                                <label><input type="checkbox" name="bfk" value="1"
                                        {{ old('bfk', $first_service->bfk ?? '') == 1 ? 'checked' : '' }}>
                                    BFK</label>
                            </div>

                            <div class="checkbox-group">
                                <strong>Art der ausgeführten Arbeiten am</strong>:
                                <label><input type="checkbox" name="kb" value="1"
                                        {{ old('kb', $first_service->kb ?? '') == 1 ? 'checked' : '' }}>
                                    KB</label>
                                <label><input type="checkbox" name="pb" value="1"
                                        {{ old('pb', $first_service->pb ?? '') == 1 ? 'checked' : '' }}>
                                    PB</label>
                                <label><input type="checkbox" name="nt" value="1"
                                        {{ old('nt', $first_service->nt ?? '') == 1 ? 'checked' : '' }}>
                                    NT</label>
                                <label><input type="checkbox" name="km" value="1"
                                        {{ old('km', $first_service->km ?? '') == 1 ? 'checked' : '' }}>
                                    KM</label>
                                <label><input type="checkbox" name="sonstiges" value="1"
                                        {{ old('sonstiges', $first_service->sonstiges ?? '') == 1 ? 'checked' : '' }}>
                                    Sonstiges</label>
                            </div>

                            <div class="input-row">
                                <label>Typ: <input type="text" name="typ"
                                        value="{{ old('typ', $first_service->typ ?? '') }}" required></label>
                                <label>Serien-Nr.: <input type="text" name="serien_nr"
                                        value="{{ old('serien_nr', $first_service->serien_nr ?? '') }}" required></label>
                            </div>

                            <div class="checkbox-group">
                                <strong>Funktionstest:</strong>
                                <label><input type="checkbox" name="funktion_in_ordnung" value="1"
                                        {{ old('funktion_in_ordnung', $first_service->funktion_in_ordnung ?? '') == 1 ? 'checked' : '' }}>
                                    in Ordnung</label>
                                <label><input type="checkbox" name="funktion_nicht_in_ordnung" value="1"
                                        {{ old('funktion_nicht_in_ordnung', $first_service->funktion_nicht_in_ordnung ?? '') == 1 ? 'checked' : '' }}>
                                    nicht in Ordnung</label>
                            </div>

                            <div class="checkbox-group">
                                <strong>Materialverbrauch</strong>:
                            </div>

                            <table>
                                <thead>
                                    <tr>
                                        <th>Stück</th>
                                        <th style="width: 60%;">Art.-Nr.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $serviceMaterialConsumptionDetails = $first_service_material_consumption ?? [];
                                    @endphp
                                    @if (!empty($serviceMaterialConsumptionDetails))
                                        @foreach ($serviceMaterialConsumptionDetails as $rowMaterial)
                                            <tr>
                                                <input type="hidden" name="materialId[]"
                                                    value="{{ $rowMaterial->id ?? '' }}">
                                                <td><input type="number" name="materialconslieferung[]"
                                                        value="{{ $rowMaterial->piece_stuck ?? '' }}"></td>
                                                <td><input type="text" name="art_no_nr[]"
                                                        value="{{ $rowMaterial->art_no_nr ?? '' }}"></td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="article-select-id">
                                            <td><input type="number" style="width: 25%"
                                                    name="materialconslieferung[]" required></td>
                                            <td>
                                                <select name="art_no_nr[]" class="form-control article-search" required>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input type="number" style="width: 25%"
                                                    name="materialconslieferung[]" required></td>
                                            <td>
                                                <select name="art_no_nr[]" class="form-control article-search" required>
                                                </select>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-row">
                                        <strong>Bemerkungen</strong>
                                    </div>
                                    <textarea name="bemerkungen" placeholder="Bemerkungen eingeben..." class="custom-textarea" required>{{ old('bemerkungen', $first_service->bemerkungen ?? '') }}</textarea>
                                </div>

                                <div class="col-md-6">
                                    <div class="input-row">
                                        <strong>Beschreibung</strong>:
                                    </div>
                                    <textarea name="beschreibung" placeholder="Beschreibung eingeben..." class="custom-textarea">{{ old('beschreibung', $first_service->beschreibung ?? '') }}</textarea>
                                </div>
                            </div>

                            <div style="overflow-x: scroll;">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Datum</th>
                                            <th>Anfahrtzeit</th>
                                            <th>Rückfahrtzeit</th>
                                            <th>Fahrt km</th>
                                            <th>Pausch. Anfahrt</th>
                                            <th>Wartezeit</th>
                                            <th>Arbeitszeit</th>
                                            <th>ges. Arbeitszeit</th>
                                            <th>Personenzahl</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $serviceTravelDetails = $first_service_travel_details ?? [];
                                        @endphp
                                        @if (!empty($serviceTravelDetails))
                                            @foreach ($serviceTravelDetails as $row)
                                                <tr>
                                                    <input type="hidden" name="travelDetailId[]"
                                                        value="{{ $row->id ?? '' }}">
                                                    <td><input type="date" name="datum_material[]"
                                                            value="{{ $row->datum_material }}" required></td>
                                                    <td><input type="text" name="anfahrtzeit[]"
                                                            value="{{ $row->anfahrtzeit }}"></td>
                                                    <td><input type="text" name="ruckfahrtzeit[]"
                                                            value="{{ $row->ruckfahrtzeit }}"></td>
                                                    <td><input type="number" name="fahrt_km[]"
                                                            value="{{ $row->fahrt_km }}"></td>
                                                    <td><input type="number" name="pausch_anfahrt[]"
                                                            value="{{ $row->pausch_anfahrt }}"></td>
                                                    <td><input type="number" name="wartezeit[]"
                                                            value="{{ $row->wartezeit }}"></td>
                                                    <td><input type="number" name="arbeitszeit[]"
                                                            value="{{ $row->arbeitszeit }}"></td>
                                                    <td><input type="number" name="ges_arbeitszeit[]"
                                                            value="{{ $row->ges_arbeitszeit }}"></td>
                                                    <td><input type="number" name="personenzahl[]"
                                                            value="{{ $row->personenzahl }}"></td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td><input type="date" name="datum_material[]" required></td>
                                                <td><input type="text" name="anfahrtzeit[]"></td>
                                                <td><input type="text" name="ruckfahrtzeit[]"></td>
                                                <td><input type="number" name="fahrt_km[]"></td>
                                                <td><input type="number" name="pausch_anfahrt[]"></td>
                                                <td><input type="number" name="wartezeit[]"></td>
                                                <td><input type="number" name="arbeitszeit[]"></td>
                                                <td><input type="number" name="ges_arbeitszeit[]"></td>
                                                <td><input type="number" name="personenzahl[]"></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <div class="checkbox-group">
                                <label><input type="checkbox" name="hotel_ubernachtung" value="1"
                                        {{ old('hotel_ubernachtung', $first_service->hotel_ubernachtung ?? '') == 1 ? 'checked' : '' }}>
                                    Hotelübernachtung</label>
                                <label>von: <input type="text" name="hotel_von"
                                        value="{{ old('hotel_von', $first_service->hotel_von ?? '') }}"></label>
                                <label>bis: <input type="text" name="hotel_bis"
                                        value="{{ old('hotel_bis', $first_service->hotel_bis ?? '') }}"></label>
                            </div>

                            <div class="checkbox-group">
                                <label><input type="checkbox" name="arbeit_fertig" value="1"
                                        {{ old('arbeit_fertig', $first_service->arbeit_fertig ?? '') == 1 ? 'checked' : '' }}>
                                    Arbeit fertig</label>
                                <label><input type="checkbox" name="kostenpflichtig" value="1"
                                        {{ old('kostenpflichtig', $first_service->kostenpflichtig ?? '') == 1 ? 'checked' : '' }}>
                                    Kostenpflichtig</label>
                                <label><input type="checkbox" name="unter_vorbehalt" value="1"
                                        {{ old('unter_vorbehalt', $first_service->unter_vorbehalt ?? '') == 1 ? 'checked' : '' }}>
                                    unter Vorbehalt</label>
                            </div>

                            <div class="signature-section">
                                <div class="signature-container">
                                    <label>Datum:
                                        <input type="date" name="sign_date"
                                            value="{{ old('sign_date', $first_service->sign_date ?? '') }}" required>
                                    </label>
                                    <label>Klar. + Unterschrift KD-Techniker:</label>
                                    <canvas id="techniker-signature-pad" class="signature-pad"></canvas>
                                    <button type="button" class="clear-button" id="clear-techniker">Löschen</button>
                                    <input type="hidden" name="techniker_name" id="techniker-signature-data">
                                </div>

                                <div class="signature-container">
                                    <label>Name des Kunden:
                                        <input type="text" name="kunde_name"
                                            value="{{ old('kunde_name', $first_service->kunde_name ?? '') }}" required>
                                    </label>
                                    <label>Unterschrift des Kunden:</label>
                                    <canvas id="kunde-signature-pad" class="signature-pad"></canvas>
                                    <button type="button" class="clear-button" id="clear-kunde">Löschen</button>
                                    <input type="hidden" name="kunde_signature" id="kunde-signature-data">
                                </div>
                            </div>

                            <ul id="errors"></ul>
                            <input type="hidden" id="pdffield" name="pdf_base64" class="form-control" value="">

                            <button type="submit" id="pdf_generate" class="btn btn-primary">Speichern</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 for article search
    $('.article-search').select2({
        placeholder: "Artikel auswählen",
        minimumInputLength: 2,
        allowClear: true,
        dropdownAutoWidth: true,
        width: '100%',
        ajax: {
            url: '/fetch-articles',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                                id: item.id,
                                text: item.article_number + " (" + truncateText(item
                                    .description, 20) + ")"
                            };
                    })
                };
            },
            cache: true
        },
        dropdownParent: $('.article-select-id')
    });

    function truncateText(text, maxLength) {
        if (text.length > maxLength) {
            return text.substring(0, maxLength) + '...';
        }
        return text;
    }
});
</script>
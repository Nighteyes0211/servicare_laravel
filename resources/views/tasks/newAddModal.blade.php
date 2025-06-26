<!-- Servicebericht Both & Wandless -->


<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        li {
            list-style: none;
        }

        .d-flex {
            display: flex;
        }

        .align-items-baseline {
            align-items: baseline;
        }

        .w-full {
            width: 100%;
        }

        .font-bold {
            font-weight: bold;
        }

        .gap-1 {
            gap: 0.25rem !important;
        }

        .gap-2 {
            gap: 0.5rem !important;
        }

        .gap-3 {
            gap: 1rem !important;
        }

        .gap-4 {
            gap: 1.5rem !important;
        }

        .gap-5 {
            gap: 3rem !important;
        }


        .text-1 {
            font-size: 0.5rem;
        }

        .text-2 {
            font-size: 1rem;
        }

        .text-3 {
            font-size: 18px;
        }

        .text-4 {
            font-size: 2rem;
        }


        .justify-between {
            justify-content: space-between;
        }

        h1 {
            margin-top: 30px;
            padding-bottom: 10px;

        }

        .form-container {
            max-width: 1280px;
            margin: 0 auto;
            width: 100%;
            padding: 10px;
        }

        .form-wrapper {
            background-color: white;
            border: 1px solid gray;
            height: 100%;
        }

        .form-wrapper form {
            padding: 20px;

        }

        form .form-header {
            display: flex;
            flex-direction: row;
            gap: 4rem;
            justify-content: space-between;
            height: 70px;
        }
        .form-header .header-pick{
            width: 200px;
        }
        .form-header .header-pick img {
            width: 100%;
        }

        .top span {
            font-size: 25px;
        }

        .top .top-black-box .black-item {
            font-size: 30px;
            top: -9px;
        }

        form .client-info,
        .client-selects-wrapper,
        .client-function-wrapper,
        .datum-table {
            display: flex;
            flex-direction: column;
            gap: 26px;
            margin: 33px 0px 33px 0px;
        }

        .consumption-table table,
        .datum-table table {
            border-collapse: collapse;
            width: 100%;
        }

        .client-selects-wrapper .consumption-table table,
        thead,
        tbody,
        tr,
        th,
        td {
            border-collapse: collapse;

        }

        table tbody tr td ,table thead th {
            padding: 15px;
            /* text-align: center; */
        }
        table tbody tr td input
       
        {
            padding:0 !important;
        }
        .service-wrapper .service-table {
            border: 3px solid gray;
            padding: 0px 10px 0px 10px;
        }

        .service-wrapper .left-side,
        .right-side {
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding-block: 20px;
            /* margin-top: 10px; */
        }

        .service-wrapper .left-side {
            border-right: 3px solid gray;

        }

        .checkbox-field label {
            flex: 1;
            position: relative;
            bottom: 0;
        }
        .left-side .footer{
            border: 1px solid black;
            /* padding: 10px; */
        }
        .left-side .footer div {
            border-right: 1px solid black;
            width: 15%;
            padding: 10px;
        }

        .consumption-table table {
            border-collapse: collapse;
            width: 100%;
        }
        .signature-pad {
            width: 400px;
            height: 200px;
            border: 2px solid #000;
            background-color: white;
            touch-action: none;
            cursor: crosshair;
            display: block;
            position: relative;
            z-index: 10;
        }
        .signature-container {
            margin-top: 10px;
            padding: 10px;
        }
}

        
</style>
<div class="modal fade" id="serviceModal2" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="form-container">
                    <div class="form-wrapper">
                        <form id="service-record-form-second" method="POST" action="{{ isset($service) && $service->id ? route('second-service-records.update', $service->id) : route('second-service-records.store') }}" enctype="multipart/form-data">
                            @csrf
                            @if(isset($service) && $service->id)
                                @method('PUT')
                            @endif
                            <meta name="csrf-token" content="{{ csrf_token() }}">

                            <!-- Hidden Fields -->
                            <input type="text" name="form_type" value="2" hidden>
                            <input type="hidden" name="request_source" value="form">
                            <input type="hidden" name="task_id" id="task_id" value="{{ $task->id ?? '' }}">
                            <input type="hidden" name="user_id" id="user_id" value="{{ auth()->id() ?? '' }}">
                            <input type="hidden" name="customer_id" id="modal_customer_id" value="{{ $customer->id ?? '' }}">

                            <!-- Form Header -->
                            <div class="form-header">
                                <div class="header-pick"><img src="images\logo.png" alt=""></div>
                            </div>

                            <div class="service-wrapper">
                                <h1 class="">Service-Bescheinigung und Stundennachweis</h1>
                                        
                                            <input type="hidden" name="is_service_modal" value="1">

                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-sm">
                                                        <label class="form-label">Auftraggeber:</label>
                                                        <input type="text" name="auftraggeber" id="auftraggeber" class="form-control" value="{{ $customer->company_name ?? '' }}" readonly>
                                                    </div>

                                                    <div class="col-sm">
                                                        <label class="form-label">Auftr.-Nr</label>
                                                    <input class="form-control" type="text" name="auftr_nr" id="" value="{{ $task->order_number ?? '' }}"  readonly>
                                                    </div>

                                                    <div class="col-sm">
                                                        <label class="form-label">Kostenst.</label>
                                                <input class="form-control" type="text" name="kostenst" id="" value="{{ old('kostenst', $service->kostenst ?? '') }}">
                                                    </div>

                                                </div>
                                            </div>

                                      

                                            <div class="container form-group">

                                                <div class="row">
                                                    <p>Art der ausgeführten Arbeiten</p>
                                                    <div class="col-sm-3">
                                                        <div class="form-check">
                                                            <label class="form-check-label" class="form-check-label" for="1">Reparatur</label>
                                                            <input class="form-check-input" type="checkbox" name="reparatur" value="1" {{ old('reparatur', $service->reparatur ?? '') == 1 ? 'checked' : '' }}>
                                                        </div>
                                                        <div class="form-check">
                                                            <label class="form-check-label" for="2">Wartung</label>
                                                            <input class="form-check-input" type="checkbox" name="wartung" value="1" {{ old('wartung', $service->wartung ?? '') == 1 ? 'checked' : '' }}>
                                                        </div>
                                                        <div class="form-check">
                                                            <label class="form-check-label"  for="3">Lieferung</label>
                                                            <input class="form-check-input" type="checkbox" name="auslieferung" value="1" {{ old('auslieferung', $service->auslieferung ?? '') == 1 ? 'checked' : '' }}>
                                                        </div>
                                                        <p style="margin-top:15px; margin-bottom:5px !important">Prüfung bestanden?</p>
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label"  style="bottom:0;">
                                                                <input class="form-check-label" type="checkbox" name="STK_bestanden_ja" value="1" {{ old('STK_bestanden_ja', $service->STK_bestanden_ja ?? '') == 1 ? 'checked' : '' }}>
                                                                ja
                                                            </label>
                                                        </div>
                                                        <div class="form-check-inline">
                                                            <label class="form-check-label"  style="bottom:0;">
                                                                <input class="form-check-label" type="checkbox" name="STK_bestanden_nein" value="1" {{ old('STK_bestanden_nein', $service->STK_bestanden_nein ?? '') == 1 ? 'checked' : '' }}>
                                                                nein
                                                            </label>
                                                        </div>
                                            
                                                    </div>



                                                 	<div class="col-sm-8">

		                                                    	<div class="input-group">
                                                                    <span class="input-group-text" id="rsl-addon">RSL < 0,3Ω</span>
                                                                    <input class="form-control" type="text" name="rsl" placeholder="RSL" aria-label="RSL" aria-describedby="rsl-addon" value="{{ old('rsl', $service->rsl ?? '') }}">

                                                                    <span class="input-group-text" id="iso-addon">ISO > 1MΩ</span>
                                                                    <input class="form-control" type="text" name="iso" placeholder="ISO" aria-label="ISO" aria-describedby="iso-addon" value="{{ old('iso', $service->iso ?? '') }}">

                                                                    <span class="input-group-text" id="iea-addon">IEA < 3,5mA</span>
                                                                    <input class="form-control" type="text" name="iea" placeholder="IEA" aria-label="IEA" aria-describedby="iea-addon" value="{{ old('iea', $service->iea ?? '') }}">
                                                                </div>
	                                                    </div>
                                                    

                                                </div>


                                                    <div class="row">
                                                        <div class="col-sm form-group">
                                                            <label for="servicebericht_both_beschreibung" class="form-label">Servicebeschreibung (Both & Wandless)</label>
                                                            <textarea style="min-height:60px;" class="form-control" name="servicebericht_both_beschreibung" id="servicebericht_both_beschreibung" rows="5">{{ old('servicebericht_both_beschreibung', $service->servicebericht_both_beschreibung ?? '') }}</textarea>
                                                        </div>
                                                    </div>

                                            </div>
       

                                
                            </div>

                            <div class="datum-table">
                                <center>
                                    <h1>Materialverbrauch</h1>
                                </center>
                                <div class="" style="overflow-x: scroll;">
                                    <table class="table table-bordered">
                                        <thead>
                                            <th scope="col">Stck.</th>
                                            <th scope="col">Beschreibung</th>
                                            <th scope="col">ARTIKEL NR.</th>
                                            <th scope="col">Aktion</th>
                                        </thead>
                                        <tbody id="materialRows">
                                            @php
                                                $serviceMaterialConsumptionDetails = $service_material_consumption ?? [];
                                            @endphp
                                            @if (count($serviceMaterialConsumptionDetails) != 0)
                                                @foreach ($serviceMaterialConsumptionDetails as $rowMaterial)
                                                    <tr class="item-select-id">
                                                        <input type="text" name="materialId_second[]" value="{{ $rowMaterial->id ?? '' }}" hidden >
                                                        <td><input class="input-group-text" type="text" name="materialconslieferung_second[]" value="{{ $rowMaterial->piece_stück ?? '' }}" ></td>
                                                        <td><input style="width: 100%" class="input-group-text beschreibung-input" type="text" name="Beschreibung_second[]" value="{{ $rowMaterial->Beschreibung ?? '' }}" ></td>

                                                        <td>
                                                            <select name="art_no_nr_second[]" class="form-control item-search" ></select>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else



                                                <tr class="item-select-id">
                                                    <td><input class="input-group-text" type="number" name="materialconslieferung_second[]" ></td>
                                                    <td><input style="width: 100%" class="input-group-text beschreibung-input" type="text" name="Beschreibung_second[]"></td>
                                                    <td>
                                                        <select name="art_no_nr_second[]" class="form-select item-search" ></select>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                    <button style="margin-top:15px;" button type="button" class="btn btn-success" id="addArticleRow">Artikel hinzufügen</button>
                                </div>
                            </div>


                            <center>
                                    <h1>Arbeitszeiten</h1>
                                </center>

                            <div class="datum-table">
                                <div style="overflow-x: scroll;">
                                    <table class="table-bordered table">
    <tr class="w-full">
        <td>Datum von</td>
        <td>Datum bis</td>
        <td colspan="3">Anfahrzeit</td>
        <td colspan="3">Rückfahrzeit*</td>
        <td colspan="2">Fahrt km</td>
        <td rowspan="2">Pausch. Anfahrt</td>
        <td rowspan="2">Wartezeit Std./Min.</td>
        <td rowspan="2">Arbeitszeit Std.</td>
        <td colspan="3" rowspan="2">ges. Arbeitszeit</td>
        <td rowspan="2">Pers. Zahl</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>Std./Min</td>
        <td>von</td>
        <td>bis</td>
        <td>Std./Min</td>
        <td>hin</td>
        <td>zurück*</td>
    </tr>

    @php
        $serviceTravelDetails = $service_travel_details ?? [];
    @endphp

    @if(count($serviceTravelDetails) != 0)
        @foreach ($serviceTravelDetails as $row)
            <tr>
                <input type="hidden" name="travelDetailId_second[]" value="{{ $row->id ?? '' }}" required>
                <td><input class="form-control" type="date" name="datum_von_second[]" value="{{ $row->datum_von ?? '' }}" required></td>
                <td><input class="form-control" type="date" name="datum_bis_second[]" value="{{ $row->datum_bis ?? '' }}"></td>
                <td><input class="input-group-text" type="text" name="anfahrtzeit_von_second[]" value="{{ $row->anfahrtzeit_von ?? '' }}"></td>
                <td><input class="input-group-text" type="text" name="anfahrtzeit_bis_second[]" value="{{ $row->anfahrtzeit_bis ?? '' }}"></td>
                <td><input class="input-group-text" type="text" name="anfahrtzeit_std_second[]" value="{{ $row->anfahrtzeit_std ?? '' }}"></td>
                <td><input class="input-group-text" type="text" name="ruckfahrtzeit_von_second[]" value="{{ $row->ruckfahrtzeit_von ?? '' }}"></td>
                <td><input class="input-group-text" type="text" name="ruckfahrtzeit_bis_second[]" value="{{ $row->ruckfahrtzeit_bis ?? '' }}"></td>
                <td><input class="input-group-text" type="text" name="ruckfahrtzeit_std_second[]" value="{{ $row->ruckfahrtzeit_std ?? '' }}"></td>
                <td><input class="input-group-text" type="text" name="fahrt_km_hin_second[]" value="{{ $row->fahrt_km_hin ?? '' }}"></td>
                <td><input class="input-group-text" type="text" name="fahrt_km_zurück_second[]" value="{{ $row->fahrt_km_zurück ?? '' }}"></td>
                <td><input class="input-group-text" type="number" name="pausch_anfahrt_second[]" value="{{ $row->pausch_anfahrt ?? '' }}"></td>
                <td><input class="input-group-text" type="number" name="wartezeit_second[]" value="{{ $row->wartezeit ?? '' }}"></td>
                <td><input class="input-group-text" type="number" name="arbeitszeit_second[]" value="{{ $row->arbeitszeit ?? '' }}"></td>
                <td><input class="input-group-text" type="number" name="ges_arbeitszeit_second[]" value="{{ $row->ges_arbeitszeit ?? '' }}"></td>
                <td><input class="input-group-text" type="text" name="ges_arbeitszeit_tag_second[]" value="{{ $row->ges_arbeitszeit_tag ?? '' }}"></td>
                <td><input class="input-group-text" type="text" name="ges_arbeitszeit_monat_second[]" value="{{ $row->ges_arbeitszeit_monat ?? '' }}"></td>
                <td><input class="input-group-text" type="number" name="personenzahl_second[]" value="{{ $row->personenzahl ?? '' }}"></td>
            </tr>
        @endforeach
    @else
        <tr>
            <td><input class="form-control" type="date" name="datum_von_second[]" required></td>
            <td><input class="form-control" type="date" name="datum_bis_second[]"></td>
            <td><input class="input-group-text" type="text" name="anfahrtzeit_von_second[]"></td>
            <td><input class="input-group-text" type="text" name="anfahrtzeit_bis_second[]"></td>
            <td><input class="input-group-text" type="text" name="anfahrtzeit_std_second[]"></td>
            <td><input class="input-group-text" type="text" name="ruckfahrtzeit_von_second[]"></td>
            <td><input class="input-group-text" type="text" name="ruckfahrtzeit_bis_second[]"></td>
            <td><input class="input-group-text" type="text" name="ruckfahrtzeit_std_second[]"></td>
            <td><input class="input-group-text" type="text" name="fahrt_km_hin_second[]"></td>
            <td><input class="input-group-text" type="text" name="fahrt_km_zurück_second[]"></td>
            <td><input class="input-group-text" type="number" name="pausch_anfahrt_second[]"></td>
            <td><input class="input-group-text" type="number" name="wartezeit_second[]"></td>
            <td><input class="input-group-text" type="number" name="arbeitszeit_second[]"></td>
            <td><input class="input-group-text" type="number" name="ges_arbeitszeit_second[]"></td>
            <td><input class="input-group-text" type="text" name="ges_arbeitszeit_tag_second[]"></td>
            <td><input class="input-group-text" type="text" name="ges_arbeitszeit_monat_second[]"></td>
            <td><input class="input-group-text" type="number" name="personenzahl_second[]"></td>
        </tr>
    @endif
</table>

                                </div>



                                <center>
                                    <h1>Sonstiges</h1>
                                </center>




                                <div class="container form-group">
                                <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-check-inline">
                                                <p style="margin-top:15px; margin-bottom:5px !important">Arbeiten fertig?</p>
                                                <label class="form-check-label" style="bottom:0;">
                                                <input class="form-check-input" type="checkbox" name="arbeit_fertig" id="" value="1" {{ old('arbeit_fertig', $service->arbeit_fertig ?? '') == 1 ? 'checked' : '' }}>
                                                    ja
                                                </label>
                                                <label class="form-check-label" style="bottom: 0;">
                                                    <input class="form-check-input" type="checkbox" name="arbeit_fertig_nein" id="" value="1" {{ old('arbeit_fertig_nein', $service->arbeit_fertig_nein ?? '') == 1 ? 'checked' : '' }}>
                                                    nein
                                                </label>
                                            </div>
                                        </div>

                                <div class="col-sm-4">
                                     <div class="form-check-inline">
                                     <p style="margin-top:15px; margin-bottom:5px !important">Funktiontest bestanden</p>
                                        <label class="form-check-label" style="bottom: 0;">
                                            <input class="form-check-input" type="checkbox" name="funktiontest_besttanden_ja" id="" value="1" {{ old('funktiontest_besttanden_ja', $service->funktiontest_besttanden_ja ?? '') == 1 ? 'checked' : '' }}>
                                            ja
                                        </label>
                                        <label class="form-check-label" style="bottom: 0;">
                                            <input class="form-check-input" type="checkbox" name="funktiontest_besttanden_nein" id="" value="1" {{ old('funktiontest_besttanden_nein', $service->funktiontest_besttanden_nein ?? '') == 1 ? 'checked' : '' }}>
                                            nein
                                        </label>
                                </div></div>
                                <div class="col-sm-4">
                                    <div class="form-check-inline">
                                    <p style="margin-top:15px; margin-bottom:5px !important">Kostenpflichtig?</p>
                                        <label class="form-check-label" class="" style="bottom: 0;">
                                            <input class="form-check-input" type="checkbox" name="kostenpflichtig" id="" value="1" {{ old('kostenpflichtig', $service->kostenpflichtig ?? '') == 1 ? 'checked' : '' }}>
                                            ja
                                        </label>
                                        <label class="form-check-label" style="bottom: 0;">
                                            <input class="form-check-input" type="checkbox" name="kostenpflichtig_nein" id="" value="1" {{ old('kostenpflichtig_nein', $service->kostenpflichtig_nein ?? '') == 1 ? 'checked' : '' }}>
                                            nein
                                        </label>
                                    </div></div>
                                </div>
                            </div>


                                <div><span class="text-3">Es wird bestätigt, dass die Arbeit in der angegebenen Zeit ausgeführt wurde. Anlass zur Bemängelung besteht nicht.</span></div>
                                <div class="d-flex gap-5 justify-between">
                                    <div><input type="text" name="techniker_name" id="" value="{{ old('techniker_name', $service->techniker_name ?? '') }}" required><span class="text-3" style="text-align:center;display: block;">Servicetechniker</span></div>
                                    <div><input type="date" name="sign_date" id="" value="{{ old('sign_date', $service->sign_date ?? '') }}" required><span class="text-3" style="text-align:center;display: block;">Datum</span></div>
                                    <div class="signature-container">
                                        <label>Unterschrift des Kunden:</label>
                                        <canvas id="kunde-signature-pad2" class="signature-pad"></canvas>
                                        <button type="button" class="clear-button" id="clear-kunde2">Löschen</button>
                                        <input type="hidden" name="kunde_name" id="kunde-signature-data2">
                                    </div>
                                </div>
                            </div>
                            <ul id="errors"></ul>
                            <input type="text" id="pdffield2" name="second_pdf_base64" class="form-control" value="" hidden>
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

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>


    <script>
    $(document).ready(function () {
        console.log('Script gestartet ✅');

        let kundeSignaturePad2;
        const kundeCanvas2 = document.getElementById("kunde-signature-pad2");

        function initializeSignaturePad() {
            if (!kundeCanvas2) {
                console.error("Canvas für Signatur nicht gefunden ❌");
                return;
            }

            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            kundeCanvas2.width = kundeCanvas2.offsetWidth * ratio;
            kundeCanvas2.height = kundeCanvas2.offsetHeight * ratio;
            kundeCanvas2.getContext("2d").scale(ratio, ratio);

            kundeSignaturePad2 = new SignaturePad(kundeCanvas2, {
                backgroundColor: 'rgb(255,255,255)',
                penColor: 'rgb(0,0,0)',
                minWidth: 1,
                maxWidth: 2.5,
            });

            const clearBtn = document.getElementById("clear-kunde2");
            if (clearBtn) {
                clearBtn.addEventListener("click", function () {
                    kundeSignaturePad2.clear();
                    document.getElementById("kunde-signature-data2").value = "";
                    console.log("Unterschrift zurückgesetzt");
                });
            }
        }

        $('#serviceModal2').on('shown.bs.modal', function () {
            initializeSignaturePad();
        });

        setTimeout(() => {
            if (kundeCanvas2 && !kundeSignaturePad2) {
                initializeSignaturePad();
            }
        }, 500);

        $('#service-record-form-second').on('submit', function (e) {
            e.preventDefault();

            const form = document.getElementById('service-record-form-second');
            const formData = new FormData(form);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            $('#pdf_generate').prop('disabled', true).text('PDF wird generiert...');

            // Signatur hinzufügen
            if (kundeSignaturePad2 && !kundeSignaturePad2.isEmpty()) {
                const signatureBase64 = kundeSignaturePad2.toDataURL('image/png');
                formData.set('kunde_name', signatureBase64);
            } else {
                formData.set('kunde_name', '');
            }

            // PDF generieren
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            let y = 20;

            for (let [name, value] of formData.entries()) {
                doc.text(`${name}: ${value}`, 10, y);
                y += 10;
            }

            doc.setFontSize(16);
            doc.text('Service Record Form', 10, 10);

            const pdfDataUrl = doc.output('datauristring');
            formData.set('second_pdf_base64', pdfDataUrl);

            // AJAX senden
            $.ajax({
                url: form.action,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (response) {
                    console.log('Formular gespeichert ✅');

                    const taskId = document.getElementById("task_id").value;
                    const path = response.service_file;
                    const myid = response.service_id;

                    const deleteRoute = "{{ route('second-service-records.destroy', ':id') }}".replace(':id', myid);
                    const assetPath = '/' + path;

                    if (response.flag === 'add' && taskId != 0) {
                        $("#secondServiceList").show();
                        $("#secondServiceList").append(`
                            <li>
                                ${path || 'Kein Pfad'} |
                                <a href="${assetPath}" target="_blank">Bericht Anzeigen</a> |
                                <a href="#" onclick="editSecondForm(${myid})">Bericht bearbeiten</a> |
                                <a href="${deleteRoute}" class="text-danger" onclick="return confirm('Wirklich löschen?')">Bericht löschen</a>
                            </li>
                        `);
                    }

                    $('#secondInputFieldId').val(response.data.id);

                    const modalElement = document.getElementById('serviceModal2');
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (modalInstance) modalInstance.hide();

                    $('#service-record-form-second')[0].reset();
                    $('#pdf_generate').prop('disabled', false).text('Speichern');
                },
                error: function (xhr) {
                    console.error('Fehler beim Senden ❌', xhr);

                    $('#errors').empty().show();

                    const response = xhr.responseJSON;
                    if (response?.errors) {
                        Object.values(response.errors).forEach(messages => {
                            messages.forEach(msg => $('#errors').append(`<li>${msg}</li>`));
                        });
                    } else if (response?.message) {
                        $('#errors').append(`<li>${response.message}</li>`);
                    } else {
                        $('#errors').append('<li>Unbekannter Fehler beim Absenden.</li>');
                    }

                    $('#pdf_generate').prop('disabled', false).text('Speichern');
                }
            });
        });
    });
</script>


<script>

    $(document).ready(function () {
        // Funktion zur Initialisierung von Select2

        function initSelect2(selectElement) {
            console.log('Script läuft 3');
            selectElement.select2({
                placeholder: "Artikel auswählen",
                minimumInputLength: 2,
                allowClear: true,
                width: '100%',
                dropdownParent: selectElement.closest('tr'),
                ajax: {
                    url: '/fetch-articles',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { search: params.term };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    id: item.id,
                                    text: item.article_number,
                                    description: item.description
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
        }

        // Initialisiere bestehende Select2-Felder beim Laden
        $('.item-search').each(function () {
            initSelect2($(this));
        });

        // Neue Zeile generieren
        function createNewArticleRow() {
            return `
                <tr class="item-select-id">
            <td><input type="number" name="materialconslieferung_second[]" required></td>
            <td><input type="text" name="Beschreibung_second[]" required></td>
            <td>
                <select name="art_no_nr_second[]" class="form-control item-search" required></select>
            </td>
            <td>
                <button type="button" class="btn btn-danger delete-row">Löschen</button>
            </td>
        </tr>
            `;
        }

        // Zeile hinzufügen bei Button-Klick
        $('#addArticleRow').on('click', function () {
            const newRow = $(createNewArticleRow());
            $('#materialRows').append(newRow);

            const newSelect = newRow.find('.item-search');
            initSelect2(newSelect);
        });

        // Beschreibung automatisch setzen bei Auswahl
        $(document).on('select2:select', '.item-search', function (e) {
            const selectedData = e.params.data;
            const row = $(this).closest('tr');
            row.find('input[name="Beschreibung_second[]"]').val(selectedData.description);
        });


        $(document).on('click', '.delete-row', function () {
    $(this).closest('tr').remove();
});
        
    });
</script>






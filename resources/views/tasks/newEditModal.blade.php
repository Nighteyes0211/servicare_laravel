
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

        input {
            border: 0;
            outline: none;
            border-bottom: 1px solid black;
            padding: 10px 5px 5px 5px;
            font-size: 18px;
            width: 100%;
            flex: 1;

        }

        label {
            position: relative;
            bottom: -8px;
            font-size: 18px;
            display: inline-block;
        }

        input[type='checkbox'] {
            width: 20px;
            height: 20px;
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
        .d-flex {
            display: flex;
            gap: 20px;
            justify-content: space-between;
            align-items: center;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        input {
            width: 280px;
            height: 40px;
            font-size: 16px;
            padding: 5px;
            text-align: center;
        }
        #signatureCanvas{
            border: 2px solid #000;
        }
        .signature-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .signature-pad {
            border: 2px solid #000;
            background-color: white;
            touch-action: none;
        }

        .clear-button {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
            margin-top: 5px;
        }

        .clear-button:hover {
            background-color: darkred;
        }

        
</style>
<div class="modal fade" id="serviceModal2" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="form-container">
                    <form></form>
                    <div class="form-wrapper">
                        <form id="service-record-form-second" action="{{ isset($service) ? route('second-service-records.update', $service->id) : route('second-service-records.update', $service->id ?? 0) }}">
                            <!--form header-->
                            @csrf
                            <meta name="csrf-token" content="{{ csrf_token() }}">
                            <div class="form-header">
                                <div class="header-pick"><img src="images\logo.png" alt=""></div>

                            </div>
                            <input type="text" name='form_type' value="2" hidden >
                            <div class="service-wrapper">
                                <h1 class="">Service-Bescheinigung und Stundennachweis</h1>
                                <div class="service-table d-flex gap-3">
                                    <div class="left-side" style="width: 70%;">
                                        <div class="d-flex gap-2 align-items-baseline">
                                            <label>Auftraggeber:</label>
                                            <input type="text" name="auftraggeber" id="" value="{{ old('auftraggeber', $service->auftraggeber ?? '') }}">
                                        </div>
                                        <div>
                                            <input type="text" name="" id="">
                                        </div>
                                    </div>
                                    <div class="right-side" style="width: 30%;">
                                        <div class="d-flex gap-2 align-items-baseline">
                                            <label>Auftr.-Nr</label>
                                            <input type="text" name="auftr_nr" id="" value="{{ old('auftr_nr', $service->auftr_nr ?? '') }}">
                                        </div>
                                        <div class="d-flex gap-2 align-items-baseline">
                                            <label>Kostenst.</label>
                                            <input type="text" name="kostenst" id="" value="{{ old('kostenst', $service->kostenst ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="service-table d-flex gap-3">

                                    <div class="left-side" style="width: 70%;">
                                        <div class="d-flex gap-2 align-items-baseline">
                                            <label>AArt der ausgefuhrten</label>
                                            <input type="text" name="aart_der_ausgefuhrten" id="" value="{{ old('aart_der_ausgefuhrten', $service->aart_der_ausgefuhrten ?? '') }}">
                                        </div>
                                        <div>
                                            <input type="text" name="" id="">
                                        </div>
                                        <div>
                                            <input type="text" name="" id="">
                                        </div>
                                        <div class="d-flex  footer align-items-baseline">
                                            <div>RSL<0,3&#8486;</div>
                                            <div><input type="text" name="rsl" id="" style="border-bottom: 0; padding: 0;" value="{{ old('rsl', $service->rsl ?? '') }}"></div>
                                            <div>ISO>1M&#8486;</div>
                                            <div><input type="text" name="iso" id="" style="border-bottom: 0;" value="{{ old('iso', $service->iso ?? '') }}"></div>
                                            <div>IEA<3,5mA</div>
                                            <div style="flex:1;"><input type="text" name="iea" id="" style="border-bottom: 0;" value="{{ old('iea', $service->iea ?? '') }}"></div>
                                        </div>
                                    </div>
                                    <div class="right-side" style="width: 30%;">
                                        <div class="d-flex gap-2 align-items-baseline  checkbox-field">
                                            <label for="1">Reparatur</label>
                                            <input type="checkbox" name="reparatur" value='1' {{ old('reparatur', $service->reparatur  ?? '') == 1 ? 'checked' : '' }}>
                                        </div>
                                        <div class="d-flex gap-2 align-items-baseline  checkbox-field">
                                            <label for="2">Wartung</label>
                                            <input type="checkbox" name="wartung" value='1' {{ old('wartung', $service->wartung  ?? '') == 1 ? 'checked' : '' }}>
                                        </div>
                                        <div class="d-flex gap-2 align-items-baseline  checkbox-field">
                                            <label class="" for="3">Lieferung</label>
                                            <input type="checkbox" name="auslieferung" value='1'{{ old('auslieferung', $service->auslieferung  ?? '') == 1 ? 'checked' : '' }} >
                                        </div>
                                        <div class="d-flex gap-5" style="border-top: 1px solid; padding-top: 10px; margin-top: 16px;">
                                            <div>STK bestanden</div>
                                            <div style=""> <label class="" style="bottom:0;">
                                                <input type="checkbox" name="STK_bestanden_ja" value='1' {{ old('STK_bestanden_ja', $service->STK_bestanden_ja  ?? '') == 1 ? 'checked' : '' }} >
                                                ja
                                            </label>
                                        
                                        </div>
                                            <div>   <label class="" style="bottom:0;">
                                                <input type="checkbox" name="STK_bestanden_nein" value='1' {{ old('STK_bestanden_nein', $service->STK_bestanden_nein  ?? '') == 1 ? 'checked' : '' }} >
                                                nein
                                            </label></div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="material-table">
                                <center>
                                    <h1>Materialverbrauch</h1>
                                </center>
                                <div class="consumption-table" style="overflow-x: scroll;">
                                    <table border="1">
                                        <thead>
                                            <th style="width: 15%;">Stck.</th>
                                            <th style="width: 50%;">Beschreibung</th>
                                            <th style="width: 35%;">ARTIKEL NR.</th>
                                        </thead>
                                        <tbody>
                                        @php
                                            $serviceMaterialConsumptionDetails = $service_material_consumption ?? [];
                                        @endphp
                                        @if (count($serviceMaterialConsumptionDetails) != 0)
                                        @foreach ($serviceMaterialConsumptionDetails as $rowMaterial)
                                            <tr class="item-select-id">
                                                <input type="text" name='materialId_second[]' value="{{ $rowMaterial->id ?? '' }}" hidden >
                                                <td><input type="text" name="materialconslieferung_second[]" id=""   style="border-bottom: 0;" value="{{ $rowMaterial->piece_stück ?? '' }}"></td>
                                                <td><input type="text" name="Beschreibung_second[]" id=""  style="border-bottom: 0;" value="{{ $rowMaterial->Beschreibung ?? '' }}"></td>
                                                <td>
                                                    <select name="art_no_nr_second[]" class="form-control item-search">
                                                    </select>
                                                    {{-- <input type="text" name="art_no_nr_second[]" id=""  style="border-bottom: 0;" value="{{ $rowMaterial->art_no_nr ?? '' }}"> --}}
                                                </td>
                                            </tr>
                                            @endforeach
                                        @else 
                                            <tr class="item-select-id">
                                                <td><input type="number" name='materialconslieferung_second[]' ></td>
                                                <td><input type="text" name="Beschreibung_second[]" id=""></td>
                                                <td>
                                                    <select name="art_no_nr_second[]" class="form-control item-search">
                                                    </select>
                                                    {{-- <input type="text" name='art_no_nr_second[]' > --}}
                                                </td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="datum-table">
                                <div style="overflow-x: scroll;">
                                    <table border="1">
                                        <tr class="w-full">
                                            <td colspan="2">Datum</td>
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
                                            <td>Tag</td>
                                            <td>Monat</td>
                                            <td>von</td>
                                            <td>bis </td>
                                            <td>Std./Min</td>
                                            <td>von</td>
                                            <td>bis </td>
                                            <td>Std./Min</td>
                                            <td>hin</td>
                                            <td>zurück*</td>

                                        </tr>
                                        @php
                                            $serviceTravelDetails = $service_travel_details ?? [];
                                        @endphp
                                        @if( count($serviceTravelDetails) != 0)
                                        @foreach ($serviceTravelDetails as $row)
                                        <tr>
                                                <input type="text" name='travelDetailId_second[]' value="{{ $row->id ?? '' }}" hidden>
                                                <td><input type="date" name='datum_material_tag_second[]' value="{{ $row->datum_material_tag ?? '' }}" ></td>
                                                <td><input type="date" name='datum_material_monat_second[]' value="{{ $row->datum_material_monat ?? '' }}" ></td>
                                                <td><input type="text" name='anfahrtzeit_von_second[]' value="{{ $row->anfahrtzeit_von ?? '' }}" ></td>
                                                <td><input type="text" name='anfahrtzeit_bis_second[]' value="{{ $row->anfahrtzeit_bis ?? '' }}" ></td>
                                                <td><input type="text" name='anfahrtzeit_std_second[]' value="{{ $row->anfahrtzeit_std ?? '' }}" ></td>
                                                <td><input type="text" name='ruckfahrtzeit_von_second[]' value="{{ $row->ruckfahrtzeit_von ?? '' }}" ></td>
                                                <td><input type="text" name='ruckfahrtzeit_bis_second[]' value="{{ $row->ruckfahrtzeit_bis ?? '' }}" ></td>
                                                <td><input type="text" name='ruckfahrtzeit_std_second[]' value="{{ $row->ruckfahrtzeit_std ?? '' }}" ></td>
                                                <td><input type="text" name='fahrt_km_hin_second[]' value="{{ $row->fahrt_km_hin ?? '' }}" ></td>
                                                <td><input type="text" name='fahrt_km_zurück_second[]' value="{{ $row->fahrt_km_zurück ?? '' }}" ></td>
                                                <td><input type="number" name='pausch_anfahrt_second[]' value="{{ $row->pausch_anfahrt ?? '' }}" ></td>
                                                <td><input type="number" name='wartezeit_second[]' value="{{ $row->wartezeit ?? '' }}" ></td>
                                                <td><input type="number" name='arbeitszeit_second[]' value="{{ $row->arbeitszeit ?? '' }}" ></td>
                                                <td><input type="number" name='ges_arbeitszeit_second[]' value="{{ $row->ges_arbeitszeit ?? '' }}" ></td>
                                                <td><input type="text" name='ges_arbeitszeit_tag_second[]' value="{{ $row->ges_arbeitszeit_tag ?? '' }}" ></td>
                                                <td><input type="text" name='ges_arbeitszeit_monat_second[]' value="{{ $row->ges_arbeitszeit_monat ?? '' }}" ></td>
                                                <td><input type="number" name='personenzahl_second[]' value="{{ $row->personenzahl ?? '' }}" ></td>
                                            </tr>
                                        @endforeach
                                        @else 
                                            <tr>
                                                <td><input type="date" name='datum_material_tag_second[]' ></td>
                                                <td><input type="date" name='datum_material_monat_second[]'  ></td>
                                                <td><input type="text" name='anfahrtzeit_von_second[]'  ></td>
                                                <td><input type="text" name='anfahrtzeit_bis_second[]'  ></td>
                                                <td><input type="text" name='anfahrtzeit_std_second[]'></td>
                                                <td><input type="text" name='ruckfahrtzeit_von_second[]' ></td>
                                                <td><input type="text" name='ruckfahrtzeit_bis_second[]' ></td>
                                                <td><input type="text" name='ruckfahrtzeit_std_second[]' ></td>
                                                <td><input type="text" name='fahrt_km_hin_second[]' ></td>
                                                <td><input type="text" name='fahrt_km_zurück_second[]' ></td>
                                                <td><input type="number" name='pausch_anfahrt_second[]' ></td>
                                                <td><input type="number" name='wartezeit_second[]' ></td>
                                                <td><input type="number" name='arbeitszeit_second[]' ></td>
                                                <td><input type="number" name='ges_arbeitszeit_second[]' ></td>
                                                <td><input type="text" name='ges_arbeitszeit_tag_second[]' ></td>
                                                <td><input type="text" name='ges_arbeitszeit_monat_second[]' ></td>
                                                <td><input type="number" name='personenzahl_second[]'></td>
                                            </tr>
                                        @endif
                                    </table>
                                
                                </div>
                                <div class="d-flex gap-3 justify-between"
                                    style="border-bottom: 3px solid black; padding-bottom: 10px;">
                                    <div class="d-flex gap-3">
                                        <div>Arbeit fertig</div>
                                        <label class="" style="bottom:0;">
                                            <input type="checkbox" name="arbeit_fertig" id="" value='1' {{ old('arbeit_fertig', $service->arbeit_fertig  ?? '') == 1 ? 'checked' : '' }} >
                                            ja
                                        </label>
                                        <label style="bottom: 0;">
                                            <input type="checkbox" name="arbeit_fertig_nein" id="" value='1' {{ old('arbeit_fertig_nein', $service->arbeit_fertig_nein  ?? '') == 1 ? 'checked' : '' }}>
                                            nein
                                        </label>
                                    </div>
                                    <div class="d-flex gap-3">
                                        <div>Funktiontest besttanden</div>
                                        <label style="bottom: 0;">
                                            <input type="checkbox" name="funktiontest_besttanden_ja" id="" value='1' {{ old('funktiontest_besttanden_ja', $service->funktiontest_besttanden_ja  ?? '') == 1 ? 'checked' : '' }}>
                                            ja
                                        </label>
                                        <label style="bottom: 0;">
                                            <input type="checkbox" name="funktiontest_besttanden_nein" id="" value='1' {{ old('funktiontest_besttanden_nein', $service->funktiontest_besttanden_nein  ?? '') == 1 ? 'checked' : '' }}>
                                            nein
                                        </label>
                                    </div>
                                    <div class="d-flex gap-3">
                                        <div>Kostenpflichtig</div>
                                        <label class="" style="bottom: 0;">
                                            <input type="checkbox" name="kostenpflichtig" id="" value='1' {{ old('kostenpflichtig', $service->kostenpflichtig  ?? '') == 1 ? 'checked' : '' }}>
                                            ja
                                        </label>
                                        <label style="bottom: 0;">
                                            <input type="checkbox" name="kostenpflichtig_nein" id="" value='1' {{ old('kostenpflichtig_nein', $service->kostenpflichtig_nein  ?? '') == 1 ? 'checked' : '' }}>
                                            nein
                                        </label>
                                    </div>
                                </div>

                                <div><span class="text-3">Es wird bestätigt, dass die Arbeit in der angegebenen Zeit ausgeführt
                                        wurde. Anlass zur
                                        Bemängelung besteht nicht.</span></div>
                                        <div class="d-flex">
                                            <div class="form-group">
                                                <input type="text" name="techniker_name" value="{{ old('techniker_name', $service->techniker_name  ?? '') }}">
                                                <span class="text-3">Servicetechniker</span>
                                            </div>
                                            <div class="form-group">
                                                <input type="date" name="sign_date" value="{{ old('sign_date', $service->sign_date  ?? '') }}">
                                                <span class="text-3">Datum</span>
                                            </div>
                                            <div class="signature-container">
                                                <canvas id="signatureCanvas"></canvas>
                                                <input type="hidden" name="kunde_name" id="kundeSignature">
                                                <button type="button" class="clear-button" onclick="clearSignature()">Clear</button>
                                                <span class="text-3">Unterschrift des Kunden</span>
                                            </div>
                                        </div>
                            </div>
                            <ul id='errors'>
                            </ul>
                            <input type="text" id='pdffield2' name='second_pdf_base64' class="form-control" value="" hidden>
                            <button type="submit" id="pdf_generate" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <!-- Include Signature Pad Library -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad"></script>
    

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
        {{-- <script>
        const canvas = document.getElementById('signatureCanvas');
        const signaturePad = new SignaturePad(canvas);
        const kundeSignatureInput = document.getElementById('kundeSignature');


        kundeSignatureInput.width = kundeSignatureInput.offsetWidth;
        kundeSignatureInput.height = kundeSignatureInput.offsetHeight;

        function clearSignature() {
            signaturePad.clear();
        }

        // Save signature as image before form submission
        document.querySelector('form')?.addEventListener('submit', function() {
            if (!signaturePad.isEmpty()) {
                kundeSignatureInput.value = signaturePad.toDataURL();
            }
        });
    </script> --}}
    <script>
    $(document).ready(function() {
    const canvas = document.getElementById('signatureCanvas');
    const signaturePad = new SignaturePad(canvas);
    const kundeSignatureInput = document.getElementById('kundeSignature');
    
    window.clearSignature = function() {
        signaturePad.clear();
    };

    $('#clearSignature').on('click', clearSignature);

    $('#service-record-form-second').on('submit', function(e) {
        e.preventDefault();
        console.log('Processing form submission...');

        $('#pdf_generate').prop('disabled', true).text('Generating PDF...');

        let formData = $(this).serializeArray();
        formData.push({ name: "task_id", value: $('#taskId').val() });
        formData.push({ name: "user_id", value: $('#user_id').val() });
        formData.push({ name: "customer_id", value: $('#customer_id').val() });

        if (!signaturePad.isEmpty()) {
            formData.push({ name: "kunde_name", value: signaturePad.toDataURL() });
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        let y = 20;
        doc.setFontSize(16);
        doc.text('Service Record Form', 10, 10);

        formData.forEach(function(field) {
            doc.text(`${field.name}: ${field.value}`, 10, y);
            y += 10;
        });

        const pdfDataUrl = doc.output('datauristring');
        $('#pdf_data').val(pdfDataUrl);
        $('#pdffield2').val(pdfDataUrl);

        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function(response) {
                $('#secondInputFieldId').val(response.data.id);
                if (response.flag === 'add') {
                    $('#secondServiceList').show().append(`
                        <li>
                            ${response.service_file}
                            <a href="${response.service_file}" target="_blank"> Show PDF </a>
                            <a href="#" onclick="editSecondForm(${response.service_id})" data-bs-toggle="modal" data-bs-target="#serviceModal2"> Edit Report </a>
                            <a href="second-service-records.destroy,${response.service_id}"> Delete Report </a>
                        </li>
                    `);
                }
                let modalInstance = bootstrap.Modal.getInstance(document.getElementById('serviceModal2'));
                if (modalInstance) modalInstance.hide();
                $('#pdf_generate').prop('disabled', false).text('Submit');
            },
            error: function(xhr) {
                let errors = xhr.responseJSON;
                $('#serviceModal #errors').empty().show();
                if (errors.errors) {
                    Object.values(errors.errors).forEach(msgArray => msgArray.forEach(msg => $('#serviceModal #errors').append(`<li>${msg}</li>`)));
                } else if (errors.message) {
                    $('#serviceModal #errors').append(`<li>${errors.message}</li>`);
                }
                $('#pdf_generate').prop('disabled', false).text('Submit');
            }
        });
    });
});

</script>

<script>
    $(document).ready(function() {
        $('.item-search').select2({
            placeholder: "Artikel auswählen",
            multiple: true,
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
            dropdownParent: $('.item-select-id')
        });

        function truncateText(text, maxLength) {
            if (text.length > maxLength) {
                return text.substring(0, maxLength) + '...';
            }
            return text;
        }
    });
</script>
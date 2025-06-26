@extends('layouts.app')

@section('title', 'Auftrag erstellen')

@section('content')
    <style>
        .modal-content {
            width: 211% !important;
            margin-left: -55%;
        }


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

        .input-row label {
            display: flex;
            flex-direction: column;
            font-weight: bold;
            margin-bottom: 10px;
            margin-right: 10px;
        }

        .input-row input {
            width: 100%;
            padding: 10px;
            margin-left: 10px !important;
            border: 1px solid #ccc;
            border-radius: 5px;
            /* Yeh round karega */
            outline: none;
            font-size: 16px;
            transition: border 0.3s ease-in-out;
        }

        .input-row input:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .checkboxes label {
            margin-right: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 50px;
            margin-top: 20px;
            width: 100%;
        }

        .signature-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            width: calc(50% - 25px);
            min-width: 300px;
        }

        .signature-pad {
            width: 400px;
            height: 200px;
            border: 2px solid #000;
            background-color: white;
        }

        .clear-button {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
        }

        .clear-button:hover {
            background-color: darkred;
        }

        @media (max-width: 768px) {
            .signature-section {
                flex-direction: column;
            }

            .signature-container {
                width: 100%;
            }
        }

        .custom-textarea {
            width: 100%;
            min-height: 65px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: vertical;
            font-size: 14px;
        }

        .input-row {
            margin-bottom: 8px;
        }
    </style>

    <h1>Auftrag erstellen</h1>

    <form action="{{ route('tasks.store') }}" method="POST" id="create_task_form" onsubmit="updateColorPicker()">
        @csrf

        <input type='hidden' id='taskId' value="0">

        @php
            $successMsg = session()->get('success');
            session()->forget('success');
        @endphp
        @if ($successMsg)
            <div class="alert alert-success">
                {{ $successMsg }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <!-- Aufgaben-Titel -->
        <div class="mb-3">
            <label for="title" class="form-label">Bezeichnung des Auftrags</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
            @if ($errors->has('title'))
                <div class="text-danger">{{ $errors->first('title') }}</div>
            @endif
        </div>

        <!-- Benutzer-Auswahl -->
        <div class="mb-3">
            <label for="user_id" class="form-label">Zuständiger Mitarbeiter</label>
            <select name="user_id" id="user_id" class="form-control searchable-dropdown" >
                <option value="">Mitarbeiter auswählen</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}</option>
                @endforeach
            </select>
            @if ($errors->has('user_id'))
                <div class="text-danger">{{ $errors->first('user_id') }}</div>
            @endif
        </div>

        <!-- Kunden-Auswahl -->
        <div class="mb-3">
            <label for="customer_id" class="form-label">Kunde</label>
            <select name="customer_id" id="customer_id" class="form-control searchable-dropdown" required>
                <option value="">Kunde auswählen</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                        {{ $customer->company_name }} | {{ $customer->street }}, {{ $customer->postal_code }} {{ $customer->city }}
                    </option>
                @endforeach
            </select>
            @if ($errors->has('customer_id'))
                <div class="text-danger">{{ $errors->first('customer_id') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="contact_id" class="form-label">Ansprechpartner</label>
            <select name="contact_id" id="contact_id" class="form-control">
                <option value="">Bitte erst Kunde wählen</option>
            </select>
        </div>

        <!-- Kategorien-Auswahl -->
        <div class="mb-3">
            <label for="categories" class="form-label">Art des Auftrags</label>
            <div>
                @foreach ($categories as $category)
                    <div class="form-check">
                        <input class="form-check-input category-checkbox" type="checkbox" name="categories[]"
                            id="category_{{ $category->id }}" value="{{ $category->id }}"
                            {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="category_{{ $category->id }}">
                            {{ $category->name }}
                        </label>
                    </div>
                @endforeach
            </div>
            @if ($errors->has('categories'))
                <div class="text-danger">{{ $errors->first('categories') }}</div>
            @endif
        </div>


        
    

        <!-- Hidden input to store color -->
        <input type="hidden" name="color_picker" id="color_picker" value="">


        <!-- Beschreibung -->
        <div class="mb-3">
            <label for="comment" class="form-label">Beschreibung</label>
            <textarea style="height:100% !important;" class="form-control" id="comment" name="comment" rows="4">{{ old('description') }}</textarea>
            @if ($errors->has('comment'))
                <div class="text-danger">{{ $errors->first('description') }}</div>
            @endif
        </div>

        <!-- Status -->
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="">Bitte wählen</option>
                <option value="open" {{ old('status') == 'open' ? 'selected' : '' }}>Offen</option>
                <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>Erledigt</option>
                <option value="not_done" {{ old('status') == 'not_done' ? 'selected' : '' }}>Nicht erledigt</option>
                <option value="billed" {{ old('status') == 'billed' ? 'selected' : '' }}>Abgerechnet</option>
            </select>
            @if ($errors->has('status'))
                <div class="text-danger">{{ $errors->first('status') }}</div>
            @endif
        </div>

        <!-- Startzeit -->
        <div class="mb-3">
            <label for="start_time" class="form-label">Startdatum</label>
            <input type="date" name="start_time" id="start_time" class="form-control" style="text-align: left;"
    value="{{ old('start_time')  }}" >


            @if ($errors->has('start_time'))
                <div class="text-danger">{{ $errors->first('start_time') }}</div>
            @endif
        </div>
        <!-- Endzeit -->
        <div class="mb-3">
            <label for="end_time" class="form-label">Enddatum</label>
            <input type="date" name="end_time" id="end_time" class="form-control" style="text-align: left;"
                value="{{ old('end_time') }}" >
            @error('end_time')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- <div class="mb-3">
            <label for="diagnosis_id" class="form-label">Passende Diagnose auswählen</label>
            <select name="diagnosis_id" id="diagnosis_id" class="form-control searchable-dropdown">
                <option value="">Diagnosis auswählen</option>
                @foreach ($diagnoses as $diagnosis)
                    <option value="{{ $diagnosis->id }}">
                        ({{ $diagnosis->name ?? 'Unknown' }})
                    </option>
                @endforeach
            </select>
            @if ($errors->has('diagnosis_id'))
                <div class="text-danger">{{ $errors->first('diagnosis_id') }}</div>
            @endif
        </div> --}}

        <div class="mb-3">
            <ul id='secondServiceList'>
            </ul>
        </div>




        <input type="text" id='inputFieldId' name='service_report_id' class="form-control"
            value="{{ old('service_report_id') }}" hidden>
        <input type="text" id='pdffield' name='pdf_base64' class="form-control" value="{{ old('pdf_base64') }}"
            hidden>

        <input type="text" id='secondInputFieldId' name='second_service_report_id' class="form-control"
            value="{{ old('second_service_report_id') }}" hidden>
        <input type="text" id='pdffield2' name='second_pdf_base64' class="form-control"
            value="{{ old('second_pdf_base64') }}" hidden>
      

        <!-- Speichern -->
        <button type="submit" class="btn btn-success">Speichern</button>
        <button type="button" class="btn btn-primary" onclick="add()" id="wissnerBtn" disabled>
    Servicebericht Wissner erstellen
</button>
<button type="button" class="btn btn-primary" onclick="addSecondForm()" id="bothBtn" disabled>
    Both & Wandless
</button>
    </form>


    <!-- Modal for Additional Inputs -->
    <!-- <div class="modal fade" id="serviceModal1" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
                                                                                                                                            <div class="modal-dialog">
                                                                                                                                                <div class="modal-content">
                                                                                                                                                    <div class="modal-header">
                                                                                                                                                        <h5 class="modal-title" id="serviceModalLabel">Servicebericht Wissner</h5>
                                                                                                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                                                                                    </div>
                                                                                                                                                    <div class="modal-body"> -->


    {{-- @include('tasks.pdfform_generate') --}} <!-- Load the form content -->

    


    <div id="serviceModalContent"></div>
    <div id="serviceSecondModalContent"></div>

    <div id="serviceModalContainer">
        
    </div>

    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.2.0/dist/signature_pad.umd.min.js"></script>
    <script>
     
        function add() {
            $.get("{{ url('/add-service-records') }}")
                .done(function(addResponse) {
                    $('#serviceModalContent').empty().append(addResponse);
                    console.log(addResponse);

                    setTimeout(() => {
                        let modalElement = document.getElementById('serviceModal1');
                        if (!modalElement) {
                            console.error("Modal element not found after AJAX call.");
                            return;
                        }

                        var modal = new bootstrap.Modal(modalElement);
                        modal.show();

                        $(modalElement).on('shown.bs.modal', function() {
                            let technikerCanvas = document.getElementById("techniker-signature-pad");
                            let kundeCanvas = document.getElementById("kunde-signature-pad");

                            if (!technikerCanvas || !kundeCanvas) {
                                console.error("Signature pad canvas not found!");
                                return;
                            }

                            technikerCanvas.width = technikerCanvas.offsetWidth;
                            technikerCanvas.height = technikerCanvas.offsetHeight;
                            kundeCanvas.width = kundeCanvas.offsetWidth;
                            kundeCanvas.height = kundeCanvas.offsetHeight;

                            let technikerSignaturePad = new SignaturePad(technikerCanvas);
                            let kundeSignaturePad = new SignaturePad(kundeCanvas);

                            console.log("SignaturePad initialized!");

                            // FORM SUBMISSION HANDLING
                            document.getElementById("service-record-form").addEventListener("submit",
                                function(event) {
                                    let technikerSignatureData = technikerSignaturePad.toDataURL();
                                    let kundeSignatureData = kundeSignaturePad.toDataURL();

                                    document.getElementById("techniker-signature-data").value =
                                        technikerSignaturePad.isEmpty() ? "" :
                                        technikerSignatureData;
                                    document.getElementById("kunde-signature-data").value =
                                        kundeSignaturePad.isEmpty() ? "" : kundeSignatureData;

                                    setTimeout(() => {
                                        console.log("Submitting form...");
                                        console.log("Techniker Signature:", document
                                            .getElementById("techniker-signature-data")
                                            .value);
                                        console.log("Kunde Signature:", document
                                            .getElementById("kunde-signature-data")
                                            .value);

                                        this.submit();
                                    }, 100);

                                    event
                                        .preventDefault();
                                });


                            // Clear button functionality
                            document.getElementById("clear-techniker").addEventListener("click",
                                function() {
                                    technikerSignaturePad.clear();
                                    document.getElementById("techniker-signature-data").value = "";
                                    console.log("Techniker signature cleared.");
                                });

                            document.getElementById("clear-kunde").addEventListener("click",
                                function() {
                                    kundeSignaturePad.clear();
                                    document.getElementById("kunde-signature-data").value = "";
                                    console.log("Kunde signature cleared.");
                                });
                        });
                    }, 100);
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.error("Error:", textStatus, errorThrown);
                });
        }


        function addSecondForm() {
    $.get("{{ url('/add-second-service-records') }}")
        .done(function(addSecondresponse) {
            $('#serviceSecondModalContent').empty().append(addSecondresponse);

            let modalElement = document.getElementById('serviceModal2');
            if (!modalElement) {
                console.error("Modal element not found after AJAX call.");
                return;
            }

            const modal = new bootstrap.Modal(modalElement);
            modal.show();

            // Warte bis DOM wirklich verfügbar ist
            setTimeout(() => {
                const selectedCustomerId = document.getElementById('customer_id').value;
                const selectedCustomerText = document.getElementById('customer_id')
                    .options[document.getElementById('customer_id').selectedIndex].text;

                // Modal-Felder
                const modalCustomerSelect = document.getElementById('modal_customer_id');
                const auftraggeberInput = document.getElementById('auftraggeber');

                if (modalCustomerSelect) {
                // Setze den Wert korrekt für Select2
                $(modalCustomerSelect)
                    .val(selectedCustomerId)
                    .trigger('change'); // wichtig für visuelle Aktualisierung
            }


                if (auftraggeberInput) {
                    auftraggeberInput.value = selectedCustomerText;
                }
            }, 300); // Warten auf vollständiges Einfügen
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Fehler beim Laden des Modals:", textStatus, errorThrown);
        });
}




    </script>

    <script>
        function updateColorPicker() {
            let prioritySelect = document.getElementById("priority");
            let selectedColor = prioritySelect.value;
            document.getElementById("color_picker").value = selectedColor;
        }
    </script>



    <script>
        document.addEventListener("DOMContentLoaded", function() {

            document.getElementById("create_task_form")?.addEventListener("submit", function() {
                document.getElementById("techniker-signature-data").value = technikerSignaturePad
                    .toDataURL();
                document.getElementById("kunde-signature-data").value = kundeSignaturePad.toDataURL();
            });
        });
    </script>




    <script>
        $(document).ready(function() {


                
                $('#service-record-form').on('submit', function(e) {
                    e.preventDefault(); // Prevent the form from submitting the traditional way
                    // Show loading or disable submit button to prevent multiple clicks (optional)
                    $('#pdf_generate').prop('disabled', true).text('Generating PDF...');

                    // Capture form data
                    var formData = $(this).serializeArray(); // Serialize the form data

                   
                    
                    // Create a new jsPDF instance
                    const {
                        jsPDF
                    } = window.jspdf;
                    const doc = new jsPDF();


                    // Loop through form data and add it to the PDF
                    let y = 20; // Starting Y position for text
                    formData.forEach(function(field) {
                        doc.text(field.name + ": " + field.value, 10, y); // Add field data to PDF
                        y += 10; // Move to the next line
                    });

                    // Optional: Add a title to the PDF
                    doc.setFontSize(16);
                    doc.text('Service Record Form', 10, 10);
                    // Convert the PDF to a data URL (Base64 encoded)
                    const pdfDataUrl = doc.output('datauristring'); // Get PDF as a data URL

                    // Append the generated PDF data URL to the hidden field
                    $('#pdf_data').val(pdfDataUrl);

                    document.getElementById('pdffield').value = pdfDataUrl;
                    // Reset the button state   
                    $('#pdf_generate').prop('disabled', false).text('Generate PDF');
                    // console.log(pdfDataUrl);

                    // Send AJAX request
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    $.ajax({
                        url: $(this).attr('action'), // The URL to submit the form to
                        type: 'POST',
                        data: formData, // The form data
                        headers: {
                            'X-CSRF-TOKEN': csrfToken // Add CSRF token to the headers
                        },
                        success: function(response) {

                            if (response.task_id) {
                                $('#taskId').val(response.task_id);
                                $('#wissnerBtn').prop('disabled', false);
                                $('#bothBtn').prop('disabled', false);
                            }
                            
                            // Handle the successful response (e.g., show a success message)
                            document.getElementById('inputFieldId').value = response.data.id;
                            var path = response.path;
                            var myid = response.data.id;
                            console.log(path);
                            console.log(myid);
                            console.log(response.flag);
                            if (response.flag == "add") {
                                console.log('here');
                                $("#secondServiceList").show();
                                if (taskId != 0) {
                                    console.log('after here');
                                    $("#secondServiceList").append("<li>" + path +
                                        "<a href='${assetUrl}/${path}' target='_blank'> | Bericht anzeigen </a><a href='#' onclick='edit(${myid})' data-bs-toggle='modal' data-bs-target='#serviceModal1'> | Bericht bearbeiten </a><a href='/service-records/{id} )}}'> | Delete Report </a> </li>"
                                    );
                                    console.log('Link appended to #secondServiceList');
                                } else {
                                    $('#inputFieldId').val(response.data.id);
                                }
                            }
                            console.log(response);
                            let modalElement = document.getElementById('serviceModal2');

                            // Create a Bootstrap Modal instance
                            let modalInstance = bootstrap.Modal.getInstance(modalElement);

                            // Check if the modal instance exists and hide it
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                            // alert('Form submitted successfully!');
                            $('#pdf_generate').prop('disabled', false).text(
                                'Submit'); // Enable button again

                            // Optionally, you can clear the form fields or update the UI here
                            // $('#service-record-form')[0].reset(); // Reset form fields (optional)
                        },
                        error: function(xhr, status, error) {
                            // Handle the error response (e.g., show an error message)
                            var errors = xhr.responseJSON;
                            console.log(errors);
                            if (errors.errors) {
                                Object.keys(errors.errors).forEach(function(key) {
                                    errors.errors[key].forEach(function(message) {
                                        $("#serviceModal #errors").show();
                                        $("#serviceModal #errors").append('<li>' +
                                            message + '</li>');
                                    });
                                });
                            } else if (errors.message) {
                                $("#serviceModal #errors").show();
                                $("#serviceModal #errors").append('<li>' + errors.message +
                                    '</li>');
                            }
                            // alert('There was an error submitting the form. Please try again.');
                            $('#pdf_generate').prop('disabled', false).text(
                                'Submit'); // Enable button again
                        }
                    });
                });
            });
    </script>




    <script>
        $(document).ready(function() {
            $('.article-search').select2({
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

<script>
    $(document).ready(function() {
        $('.item-article-search').select2({
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const startInput = document.getElementById('start_time');
    const endInput = document.getElementById('end_time');
    const oldContactId = '{{ old('contact_id') }}';

    const toggleFields = () => {
        const selectedCategories = Array.from(document.querySelectorAll('input[name="categories[]"]:checked'))
            .map(input => parseInt(input.value));

        const urlaubId = {{ \App\Models\TaskCategory::where('name', 'Urlaub')->first()?->id ?? 'null' }};
        const krankId = {{ \App\Models\TaskCategory::where('name', 'Krank')->first()?->id ?? 'null' }};

        const isSpecial = selectedCategories.includes(urlaubId) || selectedCategories.includes(krankId);

        const titleField = document.querySelector('.task-title-field');
        const customerField = document.querySelector('.customer-field');
        const statusField = document.querySelector('.status-field');
        const priorityField = document.querySelector('.priority-field');

        if (titleField) titleField.style.display = isSpecial ? 'none' : 'block';
        if (customerField) customerField.style.display = isSpecial ? 'none' : 'block';
        if (statusField) statusField.style.display = isSpecial ? 'none' : 'block';
        if (priorityField) priorityField.style.display = isSpecial ? 'none' : 'block';

        document.getElementById('title')?.toggleAttribute('required', !isSpecial);
        document.getElementById('customer_id')?.toggleAttribute('required', !isSpecial);
        document.getElementById('status')?.toggleAttribute('required', !isSpecial);
        document.getElementById('priority')?.toggleAttribute('required', !isSpecial);
    };

    document.querySelectorAll('input[name="categories[]"]').forEach(cb => {
        cb.addEventListener('change', toggleFields);
    });

    toggleFields();

    //  Enddatum +1 Tag Logik
    startInput.addEventListener('change', function () {
        const startDate = new Date(this.value);
        if (!isNaN(startDate)) {
            const nextDate = new Date(startDate);
            nextDate.setDate(startDate.getDate() + 1);

            const yyyy = nextDate.getFullYear();
            const mm = String(nextDate.getMonth() + 1).padStart(2, '0');
            const dd = String(nextDate.getDate()).padStart(2, '0');
            const formattedDate = `${yyyy}-${mm}-${dd}`;

            if (!endInput.value || new Date(endInput.value) <= startDate) {
                endInput.value = formattedDate;
            }
        }
    });

    // Ansprechpartner bei Kundenwechsel laden
    const customerSelect = document.getElementById('customer_id');
    const contactSelect = document.getElementById('contact_id');

    $(document).on('change', '#customer_id', function () {
    const customerId = this.value;
    const contactSelect = document.getElementById('contact_id');
    contactSelect.innerHTML = '<option value="">Lade Ansprechpartner...</option>';

    fetch(`/customers/${customerId}/contacts`)
        .then(response => response.json())
        .then(data => {
            contactSelect.innerHTML = '<option value="">Ansprechpartner auswählen</option>';
            data.forEach(contact => {
                const option = document.createElement('option');
                option.value = contact.id;
                option.text = `${contact.salutation} ${contact.first_name} ${contact.last_name}`;
                contactSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Fehler beim Laden der Ansprechpartner:', error);
            contactSelect.innerHTML = '<option value="">Fehler beim Laden</option>';
        });
});


    if (customerSelect.value) {
    const customerId = customerSelect.value;
    const oldContactId = '{{ old('contact_id') }}';

    contactSelect.innerHTML = '<option value="">Lade Ansprechpartner...</option>';

    fetch(`/customers/${customerId}/contacts`)
        .then(response => response.json())
        .then(data => {
            contactSelect.innerHTML = '<option value="">Ansprechpartner auswählen</option>';
            data.forEach(contact => {
                const option = document.createElement('option');
                option.value = contact.id;
                option.text = `${contact.salutation} ${contact.first_name} ${contact.last_name}`;
                if (oldContactId && contact.id == oldContactId) {
                    option.selected = true;
                }
                contactSelect.appendChild(option);
            });

            console.log('Kontakte geladen:', data);
        })
        .catch(error => {
            console.error('Fehler beim Laden der Ansprechpartner:', error);
            contactSelect.innerHTML = '<option value="">Fehler beim Laden</option>';
        });
}

});
</script>



<script>
document.addEventListener('change', function (e) {
    if (e.target && e.target.id === 'modal_customer_id') {
        const customerName = e.target.options[e.target.selectedIndex].text;
        const auftraggeberInput = document.getElementById('auftraggeber');
        if (auftraggeberInput) {
            auftraggeberInput.value = customerName;
        }
    }
});
</script>





@endsection

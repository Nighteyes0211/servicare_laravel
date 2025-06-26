

@extends('layouts.app')

@section('title', 'Aufgabe bearbeiten')

@section('content')
    <style>
        .modal-content {
            width: 211% !important;
            margin-left: -55%;
        }

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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
@php
    $isAdmin = auth()->user()?->role === 'admin';
@endphp
    <h1>Auftrag bearbeiten</h1>

    @php
        $successMsg = session()->get('success');
        session()->forget('success');
        @endphp
        @if ($successMsg)
            <div class="alert alert-success">
                {{ $successMsg }}
            </div>
        @endif
        
        @php
            $errorMsgs = session()->get('error');
            session()->forget('error');
        @endphp
        @if ($errorMsgs)
            <div class="alert alert-danger">
                {{ $errorMsgs }}
            </div>
        @endif

    <form action="{{ route('tasks.update', $task->id) }}" method="POST" id="edit_task_form">
        @csrf
        @method('PUT')




        <!-- Aufgaben-Titel -->
        <div class="form-group">
    <label for="order_number">Auftragsnummer</label>
    <input type="text" id="order_number" class="form-control" value="{{ $task->order_number }}" readonly>
</div>
        <div class="mb-3 task-title-field">
            <label for="title" class="form-label">Bezeichnung des Auftrags</label>
            @if (auth()->user()->isAdmin())
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $task->title) }}">
            @else
                <input type="text" class="form-control" value="{{ $task->title }}" readonly disabled>
            @endif
        </div>



        <input type='hidden' id='taskId' value="{{ $task->id }}">
        <!-- Benutzer-Auswahl -->
        <div class="mb-3">
            <label for="user_id" class="form-label">Zuständiger Mitarbeiter</label>

            @if($isAdmin)
                <select name="user_id" id="user_id" class="form-control searchable-dropdown">
                    <option value="">Mitarbeiter auswählen</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}"
                            {{ old('user_id', $task->user_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control" value="{{ $task->user?->name ?? 'Kein Mitarbeiter zugeordnet' }}" readonly>
            @endif
        </div>

        <!-- Kunden-Auswahl -->
     

<div class="mb-3 customer-field">
    <label for="customer_id" class="form-label">Kunde</label>

    @if($isAdmin)
        <select name="customer_id" id="customer_id" class="form-control searchable-dropdown">
            <option value="">Kunde auswählen</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}"
                    {{ old('customer_id', $task->customer_id) == $customer->id ? 'selected' : '' }}>
                    {{ $customer->company_name }}
                </option>
            @endforeach
        </select>
    @else
        <input type="text" class="form-control" 
               value="{{ $task->customer?->company_name ?? 'Kein Kunde zugewiesen' }}" 
               readonly>
    @endif
</div>


       <div class="mb-3">
            <label for="contact_id" class="form-label">Ansprechpartner</label>
            <select name="contact_id" id="contact_id" class="form-control" required>
                <option value="">Bitte erst Kunde wählen</option>
            </select>
        </div>


<div class="mb-3">
    <label for="categories" class="form-label">Art des Auftrags</label>

    @if ($isAdmin)
        <div>
            @foreach ($categories as $category)
                <div class="form-check">
                    <input class="form-check-input category-checkbox" type="checkbox" name="categories[]"
                        id="category_{{ $category->id }}" value="{{ $category->id }}"
                        {{ in_array($category->id, old('categories', $task->categories->pluck('id')->toArray())) ? 'checked' : '' }}>

                    <label class="form-check-label" for="category_{{ $category->id }}">
                        {{ $category->name }}
                    </label>
                </div>
            @endforeach
        </div>
    @else
        <div class="form-control" style="height: auto;">
            @php
                $selectedCategoryNames = $task->categories->pluck('name')->toArray();
            @endphp
            {{ implode(', ', $selectedCategoryNames) ?: 'Keine Kategorien zugewiesen' }}
        </div>
    @endif
</div>


        <!-- Hidden input to store color -->
        <input type="hidden" name="color_picker" id="color_picker"
            value="{{ old('color_picker', $task->color_picker ?? '#ffffff') }}">


        <!-- Beschreibung -->
       
        <div class="mb-3">
            <label for="comment" class="form-label">Beschreibung</label>
            <textarea style="height:100% !important;" class="form-control" id="comment" name="comment" rows="4">{{ old('comment', $task->comment ?? '') }}</textarea>
            @if ($errors->has('comment'))
                <div class="text-danger">{{ $errors->first('comment') }}</div>
            @endif
        </div>


        <!-- Status -->
        <div class="mb-3 status-field">
            <label for="status" class="form-label">Status</label>
            @if (auth()->user()->isAdmin())
            <select name="status" id="status" class="form-control">
                <option value="">Bitte wählen</option>
                <option value="open" {{ old('status', $task->status) == 'open' ? 'selected' : '' }}>Offen</option>
                <option value="done" {{ old('status', $task->status) == 'done' ? 'selected' : '' }}>Erledigt</option>
                <option value="not_done" {{ old('status', $task->status) == 'not_done' ? 'selected' : '' }}>Nicht erledigt</option>
                <option value="billed" {{ old('status', $task->status) == 'billed' ? 'selected' : '' }}>Abgerechnet</option>
            </select>
            @else
            <input type="text" class="form-control" value="{{ ucfirst($task->status) }}" readonly disabled>
            @endif
            @if ($errors->has('status'))
                <div class="text-danger">{{ $errors->first('status') }}</div>
            @endif
        </div>

        <!-- Startzeit -->


        <div class="mb-3">
            <label for="start_time" class="form-label">Startzeit</label>
            @if ($isAdmin)
                <input type="date" name="start_time" id="start_time" class="form-control"
                    value="{{ old('start_time', $task->start_time ? $task->start_time->format('Y-m-d') : '') }}">
                @if ($errors->has('start_time'))
                    <div class="text-danger">{{ $errors->first('start_time') }}</div>
                @endif
            @else
                <div class="form-control">
                    {{ $task->start_time ? \Carbon\Carbon::parse($task->start_time)->format('d.m.Y') : 'Keine Startzeit gesetzt' }}
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="end_time" class="form-label">Endzeit</label>
            @if ($isAdmin)
                <input type="date" name="end_time" id="end_time" class="form-control"
                    value="{{ old('end_time', $task->end_time ? $task->end_time->format('Y-m-d') : '') }}">
            @else
                <div class="form-control">
                    {{ $task->end_time ? \Carbon\Carbon::parse($task->end_time)->format('d.m.Y') : 'Keine Endzeit gesetzt' }}
                </div>
            @endif
        </div>


        


        <div class="mb-3">
            <label for="status" class="form-label">Wissner</label>
            <br />
            <ul id="ServiceList">
                @if ($task_service_forms->isNotEmpty())
                    @foreach ($task_service_forms as $first_form_key => $task_service_form)
                        <li>
                            {{ $task_service_form->first_service_file ?? 'No file path' }} |
                            <a href="{{ asset($task_service_form->first_service_file) }}" target="_blank">Bericht anzeigen</a> |
                            
                            @if ($task_service_form->first_services_id)
                                <a href="{{ route('service-records.destroy', $task_service_form->first_services_id) }}" class="text-danger" onclick="return confirm('Are you sure you want to delete this report?')">Bericht löschen</a>
     
                            
                            @endif
                        </li>
                    @endforeach
                @else
                    <li>Keine Berichte vorhanden.</li>
                @endif
            </ul>
        </div>


        <div class="mb-3">
            <label for="status" class="form-label">Both & Wandless</label>
            <br />
            <ul id='secondServiceList'>
                @if ($task_second_service_forms->isNotEmpty())
                    @foreach ($task_second_service_forms as $second_form_key => $task_second_service_form)
                        <li>
                            {{ $task_second_service_form->second_service_file ?? 'No file path' }} |
                            <a href="{{ asset($task_second_service_form->second_service_file) }}" target="_blank">Bericht Anzeigen</a> |
                            
                            @if (!empty($task_second_service_form->service_id))
                                <a href="{{ route('second-service-records.destroy', $task_second_service_form->service_id) }}" class="text-danger" onclick="return confirm('Are you sure you want to delete this report?')">Bericht löschen</a>
                            @endif
                        </li>
                    @endforeach
                @else
                    <li>Keine Berichte vorhanden.</li>
                @endif
            </ul>
        </div>
        
        @php
        $successMsg = session()->get('success');
        session()->forget('success');
        @endphp
        @if ($successMsg)
            <div class="alert alert-success">
                {{ $successMsg }}
            </div>
        @endif
        
        @php
            $errorMsgs = session()->get('error');
            session()->forget('error');
        @endphp
        @if ($errorMsgs)
            <div class="alert alert-danger">
                {{ $errorMsgs }}
            </div>
        @endif

        <!-- Fälligkeitsdatum -->
        <input type="text" id='inputFieldId' name='service_report_id' class="form-control"
            value="{{ old('service_report_id') }}" hidden>
        <input type="text" id='secondInputFieldId' name='second_service_report_id' class="form-control"
            value="{{ old('service_report_id') }}" hidden>

        <!-- Speichern -->
       @if (auth()->user()->isAdmin())
    <button type="submit" class="btn btn-success">Speichern</button>
@endif
        @if ($task->status !== 'billed')
            <button type="button" class="btn btn-primary" onclick="add()">
                Servicebericht Wissner
            </button>
            <button 
                type="button" 
                class="btn btn-primary"
                onclick="addSecondForm({{ $task->id }})">
                Servicebericht Both & Wandless
            </button>
        @else
            <div class="alert alert-warning mt-2">
                Dieser Auftrag wurde bereits <strong>abgerechnet</strong>. Es können keine Serviceberichte mehr hinzugefügt oder bearbeitet werden.
            </div>
        @endif

    </form>

<div class="mb-3 mt-3">
    @if($task->pdf_path)
    <a href="{{ asset($task->pdf_path) }}" target="_blank" class="btn btn-info">
        📄 Auftrag herunterladen
    </a>
@endif
</div>


    <div id="serviceModalContent"></div>
    <div id="serviceSecondModalContent"></div>
    <div id="serviceModalContainer">





            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/signature_pad"></script>
            <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

            <!-- Select2 CSS -->
            <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

            <!-- Select2 JS -->
            <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

            <script>
                function add() {
                    $.get("{{ url('/add-service-records') }}")
                        .done(function(addResponse) {
                            $('#serviceModalContent').empty().append(addResponse);

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

                                    // Set hidden field values
                                    $('#task_id').val('{{ $task->id }}');
                                    $('#user_id').val('{{ auth()->id() }}');
                                    $('#customer_id').val('{{ $task->customer_id }}');
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
                            // $('#serviceModal2').length > 0 ? ('#serviceModal2').modal('hide') : '';
                            $('#serviceSecondModalContent').empty().append(addSecondresponse);
                            // $('#serviceModal2').modal('show');

                            let modalElement = document.getElementById('serviceModal2');
                                if (!modalElement) {
                                    console.error("Modal element not found after AJAX call.");
                                    return;
                                }

                                var modal = new bootstrap.Modal(modalElement);
                                modal.show();


                                setTimeout(() => {
                                    const selectedCustomerId = document.getElementById('customer_id').value;
                                    const selectedCustomerText = document.getElementById('customer_id')
                                        .options[document.getElementById('customer_id').selectedIndex].text;

                                    const modalCustomerSelect = document.querySelector('#serviceModal2 select[name="customer_id"]');
                                    const auftraggeberInput = document.querySelector('#serviceModal2 input#auftraggeber');

                                    if (modalCustomerSelect) {
                                        $(modalCustomerSelect)
                                            .val(selectedCustomerId)
                                            .trigger('change');
                                    }

                                    if (auftraggeberInput) {
                                        auftraggeberInput.value = selectedCustomerText;
                                    }
                                }, 300);

                                $('#task_id').val('{{ $task->id }}');

                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            console.error("Error:", textStatus, errorThrown);
                        });
                }

                function edit($id) {
                    $.get("{{ url('/service-records') }}/" + $id + "/edit")
                        .done(function(editResponse) {
                            // Remove any leftover backdrops
                            $('.modal-backdrop').remove();
                            $('#serviceModalContent').empty();
                            $('#serviceSecondModalContent').empty();
                            $('#serviceModalContent').append(editResponse);

                            setTimeout(() => {
                                let modalElement = document.getElementById('serviceModalEdit1');
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

                                    // FORM SUBMISSION HANDLING
                                    document.getElementById("service-record-form").addEventListener("submit", function(event) {
                                        let technikerSignatureData = technikerSignaturePad.toDataURL();
                                        let kundeSignatureData = kundeSignaturePad.toDataURL();

                                        document.getElementById("techniker-signature-data").value = technikerSignaturePad.isEmpty() ? "" : technikerSignatureData;
                                        document.getElementById("kunde-signature-data").value = kundeSignaturePad.isEmpty() ? "" : kundeSignatureData;

                                        setTimeout(() => {
                                            console.log("Submitting form...");
                                            console.log("Techniker Signature:", document.getElementById("techniker-signature-data").value);
                                            console.log("Kunde Signature:", document.getElementById("kunde-signature-data").value);

                                            this.submit();
                                        }, 100);

                                        event.preventDefault();
                                    });

                                    // Clear button functionality
                                    document.getElementById("clear-techniker").addEventListener("click", function() {
                                        technikerSignaturePad.clear();
                                        document.getElementById("techniker-signature-data").value = "";
                                        console.log("Techniker signature cleared.");
                                    });

                                    document.getElementById("clear-kunde").addEventListener("click", function() {
                                        kundeSignaturePad.clear();
                                        document.getElementById("kunde-signature-data").value = "";
                                        console.log("Kunde signature cleared.");
                                    });

                                    // Set hidden field values
                                    $('#task_id').val('{{ $task->id }}');
                                    $('#user_id').val('{{ auth()->id() }}');
                                    $('#customer_id').val('{{ $task->customer_id }}');
                                });
                            }, 100);
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            console.error("Error:", textStatus, errorThrown);
                        });
                }
                

                function editSecondForm($id) {
                    $.get("{{ url('/service-records-second') }}/" + $id + "/edit")
                        .done(function(editSecondResponse) {
                            // $('#serviceModal2').length > 0 ? ('#serviceModal2').modal('hide'):'';
                            // Remove any leftover backdrops
                            $('.modal-backdrop').remove();
                            $('#serviceSecondModalContent').empty();
                            $('#serviceModalContent').empty();
                            $('#serviceSecondModalContent').append(editSecondResponse);
                            // $('#serviceModal2').modal('show');
                            let modal = new bootstrap.Modal(document.getElementById('serviceModal2'));
                            modal.show();
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            console.error("Error:", textStatus, errorThrown);
                        });
                }
            </script>

    

            
    <script>





//i think no need now but delete in future
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
                                    text: item.article_number + " (" + truncateText(item.description, 20) + ")"
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

 <script>
document.addEventListener('DOMContentLoaded', function () {
    const startInput = document.getElementById('start_time');
    const endInput = document.getElementById('end_time');
    const customerSelect = document.getElementById('customer_id');
    const contactSelect = document.getElementById('contact_id');
    const oldContactId = '{{ old('contact_id', $task->contact_id) }}';

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

    // Endzeit +1 Tag setzen
    startInput?.addEventListener('change', function () {
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

    // Ansprechpartner bei Kundenwechsel nachladen
    const loadContacts = (customerId, selectedContactId = null) => {
        contactSelect.innerHTML = '<option value="">Lade Ansprechpartner...</option>';

        fetch(`/customers/${customerId}/contacts`)
            .then(response => response.json())
            .then(data => {
                contactSelect.innerHTML = '<option value="">Ansprechpartner auswählen</option>';
                data.forEach(contact => {
                    const option = document.createElement('option');
                    option.value = contact.id;
                    option.text = `${contact.salutation} ${contact.first_name} ${contact.last_name}`;
                    if (selectedContactId && contact.id == selectedContactId) {
                        option.selected = true;
                    }
                    contactSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Fehler beim Laden der Ansprechpartner:', error);
                contactSelect.innerHTML = '<option value="">Fehler beim Laden</option>';
            });
    };

    if (customerSelect && contactSelect) {
        customerSelect.addEventListener('change', function () {
            const customerId = this.value;
            if (customerId) {
                loadContacts(customerId);
            } else {
                contactSelect.innerHTML = '<option value="">Bitte erst Kunde wählen</option>';
            }
        });

        // Beim Laden der Seite vorausgewählten Kontakt anzeigen
        if (customerSelect.value) {
            loadContacts(customerSelect.value, oldContactId);
        }
    }
});
</script>




<script>
function addSecondForm(taskId) {
    $.ajax({
        url: '/service-records/second-create-modal',
        method: 'GET',
        data: { task_id: taskId },
        success: function (response) {
            $('#serviceSecondModalContent').html(response);

            setTimeout(() => {
                const modalElement = document.getElementById('serviceModal2');
                if (!modalElement) return console.error("Modal #serviceModal2 not found in DOM");

                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }, 100);
        },
        error: function (xhr) {
            alert('Fehler beim Laden des Formulars.');
        }
    });
}

</script>







            {{-- <script>
    $(document).ready(function() {
        $('.searchable-dropdown').select2({
            width: '100%',
            placeholder: "Auswählen...",
            allowClear: true
        });
    });
</script> --}}






        @endsection

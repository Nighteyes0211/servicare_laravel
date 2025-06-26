@extends('layouts.app')

@section('title', 'Diagnose und Aufnahme')
@section('content')

<!-- <h1>Neuen Diagnosebogen erstellen</h1>
<form action="{{ route('diagnoses.store') }}" method="POST">
    @csrf
    <label>Kunde:</label>
    <select name="customer_id" required>
        @foreach($customers as $customer)
            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
        @endforeach
    </select>
    <label>Diagnosedetails:</label>
    <textarea name="diagnosis_details" required></textarea>
    <label>Diagnosedatum:</label>
    <input type="date" name="diagnosis_date" required>
    <button type="submit">Speichern</button>
</form> -->



<div class="form-container">
    <!-- Header Section -->
    <div class="header">
        <h1>Aufnahme-/Diagnosebogen</h1>
    </div>
    <!-- Fehlermeldungen anzeigen -->
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form action="{{ route('diagnoses.store') }}" method="POST">
        @csrf


        <div class="row mb-3">
            <div class="col-md-6">
                 <label for="customer">Kunde/Einrichtung:</label>
                 <select id="customer" name="customer_id" class="form-select" required>
                    <option value="" disabled selected>Bitte wählen Sie einen Kunden</option>

                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->company_name }}</option>
                    @endforeach
                </select>

                <div id="customerDetails" style="margin-top: 20px;">
                    <!-- Customer details will appear here -->
                </div>

                <!-- 
                <label class="form-label" for="address">Adresse:</label>
                <textarea name="address" id="address" required class="form-control"></textarea>


                    <label for="phone">Telefon</label>
                            <input class="form-control" type="text" id="phone" name="phone"  placeholder="" placeholder="Telefonnummer" required>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-control" required>

                        </div>
                        <div class="col-md-6">
                            <label for="phone">Telefon</label>
                            <input type="text" id="phone" name="phone" class="form-control" placeholder="" placeholder="4921513341772" required>
                        </div>
                    </div>
                -->

          

                 <label class="form-label">Aufgenommen durch:</label>
                <label><input class="form-check-input" type="checkbox" name="action[]" value="PB"> PB</label>
                <label><input class="form-check-input" type="checkbox" name="action[]" value="PW"> PW</label>
                <label><input class="form-check-input" type="checkbox" name="action[]" value="JT"> JT</label>
                <label><input class="form-check-input" type="checkbox" name="action[]" value="DM"> DM</label>
                <label><input class="form-check-input" type="checkbox" name="action[]" value="other">Sonstige</label>
                <br>
               


            </div>
        
            <div class="col-md-6">

                <label class="form-label">Diagnosedetails:</label>
                <textarea class="form-control" name="diagnosis_details" placeholder="Details zur Diagnose" required></textarea>

                <label class="form-label" for="forward_to">Diagnose Datum:</label>
                <input type="date" class="form-control" name="diagnosis_date" required>

                <!-- Checkbox Group -->

                <label class="form-label"><input class="form-check-input" type="checkbox" name="type[]" value="repair"> Reparatur</label>
                <label class="form-label"><input class="form-check-input" type="checkbox" name="type[]" value="complaint"> Reklamation</label>
                <label class="form-label"><input class="form-check-input" type="checkbox" name="type[]" value="confirmation"> Auftragsbestätigung</label>
                <label class="form-label"><input class="form-check-input" type="checkbox" name="type[]" value="inquiry"> Anfrage</label>
                <label class="form-label"><input class="form-check-input" type="checkbox" name="type[]" value="quote"> KV erstellen</label>
                <label class="form-label"><input class="form-check-input" type="checkbox" name="type[]" value="maintenance"> Wartung</label>
                <label class="form-label"><input class="form-check-input" type="checkbox" name="type[]" value="note"> Gesprächsnotiz</label>
                <label class="form-label"><input class="form-check-input" type="checkbox" name="type[]" value="order"> Bestellung</label>
            </div>
        </div>

        <button class="btn btn-primary mb-3" type="submit">Speichern</button>
    </form>

    <script>
        
        $('#customer').change(function () {
            // Get the selected customer's ID
            const customerId = $(this).val();

            if (customerId) {
                // Make the AJAX request
                // $.ajax({
                //     url: `{{ url('/customers-data') }} http://165.232.70.164/customers-data/${customerId}`,
                //     method: 'GET',
                //     dataType: 'json',
                //     success: function (response) {
                //         // Handle the response (assuming it's JSON)
                //         console.log(response);

                //         // Example: Display data in a div or process it further
                //         $('#customerDetails').html(`
                //             <p><strong>Customer Name:</strong> ${response.company_name}</p>
                //             <p><strong>Contact:</strong> ${response.contact_person}</p>
                //             <p><strong>Email:</strong> ${response.email}</p>
                //         `);
                //     },
                //     error: function (xhr, status, error) {
                //         console.error(`Error: ${xhr.responseText}`);
                //         alert('An error occurred while fetching customer data.');
                //     }
                // });

                $.get("{{ url('/customer-data') }}/" + customerId )
                .done(function(response) {
                    // console.log(response);
                    // Example: Display data in a div or process it further
                    $('#customerDetails').html(`
                        <p><strong>Customer Name:</strong> ${response.company_name}</p>
                        <p><strong>Contact:</strong> ${response.phone}</p>
                        <p><strong>Email:</strong> ${response.email}</p>
                        <p><strong>Customer Address:</strong> ${response.address}</p>
                        <input type="hidden" class="form-control" name="name" value="${response.company_name}" >
                        <input type="hidden" class="form-control" name="phone" value="${response.phone}" >
                        <input type="hidden" class="form-control" name="phone" value="${response.phone}" >
                    `);

                    // <p><strong>Customer Postal Code:</strong> ${response.postal_code}</p>
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.error("Error:", textStatus, errorThrown);
                });
            }
        });
    </script>

    @endsection
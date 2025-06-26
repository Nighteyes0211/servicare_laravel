<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'B&W')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- FullCalendar -->
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.2/main.min.css' rel='stylesheet' />
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.2/main.min.js'></script>

    <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery (must be included before Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">





    <style>

        #loader-overlay {
    position: fixed;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.8);
    z-index: 9999;
    top: 0;
    left: 0;
    display: flex;
    justify-content: center;
    align-items: center;
}

.spinner {
    width: 50px;
    height: 50px;
    border: 6px solid #ccc;
    border-top-color: #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

        .pagination svg {
            height: 50px;
            width: 50px;
        }

        .pagination a {
            text-decoration: none;
        }

        .form-control,
        .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
            padding: .375rem .75rem;
            font-size: 1rem;
            border-radius: .25rem;
        }

        .select2-container .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 2.25rem;
        }

        .select2-container--default .select2-results>.select2-results__options {
            max-height: 250px !important;
            overflow-y: auto
        }

        .searchable-dropdown {
            max-width: 100% !important;
            width: 100% !important;
        }

        @media (max-width: 768px) {
            .searchable-dropdown {
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>

<div id="loader-overlay" style="display: none;">
    <div class="spinner"></div>
</div>


<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}"><img src="{{ asset('img/logo.png') }}"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                     @if (auth()->user() && auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customers.index') }}">Kundenverwaltung</a>
                    </li>
                      @endif
                      @if (auth()->user() && auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('articles.index') }}">Artikel</a>
                    </li>
                      @endif

            <!--    <li class="nav-item">
                        <a class="nav-link" href="{{ route('diagnoses.index') }}">Diagnosebögen</a>
                    </li>
            -->
            
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tasks.index') }}">Auftragsverwaltung</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tasks.planner') }}">Planer</a>
                    </li>
                    @if (auth()->user() && auth()->user()->isAdmin())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Administration
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                <li><a class="dropdown-item"
                                        href="{{ route('user_management.index') }}">Benutzerverwaltung</a></li>
                                <li><a class="dropdown-item"
                                        href="{{ route('task_categories.index') }}">Aufgabenkategorien</a></li>
                            </ul>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('vacation.index') }}">Urlaubsanträge
                            @if (auth()->user() && auth()->user()->isAdmin())
                                <span id='leaveCount'
                                    class="position-absolute top-0 right-0 rounded-circle bg-danger badge badge-pill badge-light">0</span>
                            @endif
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.index') }}">Profile</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4 card-body">
        @yield('content')
    </div>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            pendingLeaveCount();
        });

        function pendingLeaveCount() {
            $.get("{{ url('/pending-count') }}")
                .done(function(response) {
                    $('#leaveCount').empty();
                    $('#leaveCount').text(response.total_count);
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.error("Error:", textStatus, errorThrown);
                });
        }


        $(document).ready(function() {
            $('.searchable-dropdown').select2({
                width: '100%',
                placeholder: "Auswählen...",
                allowClear: true
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>

    <script>
    window.addEventListener("beforeunload", function () {
        document.getElementById("loader-overlay").style.display = "flex";
    });
</script>


</body>

</html>

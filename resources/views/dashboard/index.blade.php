@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')


<style type="text/css">
    .card {
        position: relative;
        display: flex;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background: none !important;
        background-clip: border-box;
        border: none !important;
        border-radius: .25rem;
    }
</style>

<div class="container py-4">

    <!-- Willkommen Nachricht -->
    <div class="mb-4">
        <h2>Willkommen zurück, {{ auth()->user()->name }}!</h2>
        <p class="text-muted">Schön, dich wieder hier zu sehen.</p>
    </div>

    <!-- Quick Links -->
    @if (auth()->user() && auth()->user()->isAdmin())

        <div class="row g-4">
            <div class="col-md-3">
                <a href="{{ route('customers.index') }}" class="card text-decoration-none text-dark h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title mb-3">Übersicht Kunden</h5>
                        <i class="bi bi-people-fill" style="font-size: 2rem;"></i>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('articles.index') }}" class="card text-decoration-none text-dark h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title mb-3">Übersicht Artikel</h5>
                        <i class="bi bi-box-seam" style="font-size: 2rem;"></i>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('tasks.index') }}" class="card text-decoration-none text-dark h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title mb-3">Übersicht Aufträge</h5>
                        <i class="bi bi-list-task" style="font-size: 2rem;"></i>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('tasks.create') }}" class="card text-decoration-none text-dark h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title mb-3">Neuen Auftrag anlegen</h5>
                        <i class="bi bi-plus-circle" style="font-size: 2rem;"></i>
                    </div>
                </a>
            </div>
        </div>

    @endif






</div>

<!-- Bootstrap Icons laden -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

@endsection

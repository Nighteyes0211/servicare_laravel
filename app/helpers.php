<?php

if (!function_exists('translateTaskStatus')) {
    function translateTaskStatus(string $status): string
    {
        return match ($status) {
            'open' => 'Offen',
            'done' => 'Erledigt',
            'not_done' => 'Nicht erledigt',
            'billed' => 'Abgerechnet',
            default => ucfirst($status),
        };
    }
}

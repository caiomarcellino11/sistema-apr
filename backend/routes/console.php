<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Comandos Artisan
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Agendamentos de seguranca
|--------------------------------------------------------------------------
|
| Remove tokens do Sanctum expirados ha mais de 24 horas.
| A rotina e executada diariamente e evita execucoes simultaneas.
|
*/

Schedule::command('sanctum:prune-expired --hours=24')
    ->dailyAt('03:00')
    ->timezone('America/Sao_Paulo')
    ->withoutOverlapping();
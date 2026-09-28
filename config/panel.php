<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Layback-optie
    |--------------------------------------------------------------------------
    |
    | Staat deze uit, dan kan layback niet meer gekozen worden bij nieuwe
    | offertes en orders. Regels die al layback hebben (bestaande offertes en
    | orders) behouden hem gewoon. Weer aanzetten: PANEL_LAYBACK_ENABLED=true
    | in .env zetten en `php artisan config:clear` draaien.
    |
    */
    'layback_enabled' => env('PANEL_LAYBACK_ENABLED', false),

];

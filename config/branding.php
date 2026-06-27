<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Nama Singkat (sidebar, header, tab browser)
    |--------------------------------------------------------------------------
    */

    'short_name' => env('APP_BRAND_SHORT', 'Polmas Ditres PPA & PPO NTB'),

    /*
    |--------------------------------------------------------------------------
    | Nama Lengkap (halaman login, kop PDF, dokumen resmi)
    |--------------------------------------------------------------------------
    */

    'full_name' => env(
        'APP_BRAND_FULL',
        'Laporan Kegiatan Polmas Ditres PPA & PPO Polda NTB',
    ),

    /*
    |--------------------------------------------------------------------------
    | Logo (path relatif dari folder public)
    |--------------------------------------------------------------------------
    */

    'logo' => env('APP_BRAND_LOGO', 'images/logo.png'),

    'logo_height' => env('APP_BRAND_LOGO_HEIGHT', '5rem'),

];

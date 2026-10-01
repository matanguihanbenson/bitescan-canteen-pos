<?php

return array(
    'public_path' => null,
    
    /*
    |--------------------------------------------------------------------------
    | DOMPDF Chroot
    |--------------------------------------------------------------------------
    |
    | DOMPDF's "chroot" setting should be explicitly set to prevent file path 
    | traversal attempts.
    |
    */
    'chroot' => realpath(base_path()),

    'default_paper_size' => 'letter',

    'font_cache' => storage_path('fonts/'),
    
    'options' => array(
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'isPhpEnabled' => true,
        'isJavascriptEnabled' => true,
        'isFontSubsettingEnabled' => true,
        'defaultMediaType' => 'screen',
        'defaultPaperSize' => 'letter',
        'dpi' => 96,
        'fontHeightRatio' => 0.9,
        'enable_remote' => true,
    ),
);

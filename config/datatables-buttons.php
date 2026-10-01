<?php

return [
    'pdf_generator' => 'domPDF',
    'namespace' => [
        'base'  => 'DataTables',
        'model' => '',
    ],
    'pdf_generator_class' => \Barryvdh\DomPDF\PDF::class,
    'parameters' => [
        'dom' => 'Bfrtip',
        'buttons' => [
            'excel',
            'csv',
            [
                'extend' => 'pdf',
                'action' => 'function(e, dt, node, config) {
                    e.preventDefault();
                    window.open("/inventory/products/export-pdf", "_blank");
                }',
                'className' => 'pdf-button',
                'text' => 'PDF'
            ],
            [
                'extend' => 'print',
                'title' => 'Canteen BiteScan Report',
                'exportOptions' => [
                    'columns' => ':visible'
                ]
            ]
        ],
    ],
    'builder' => [
        'parameters' => []
    ]
];

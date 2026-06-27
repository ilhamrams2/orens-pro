<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('generate:flowchart-pdf', function () {
    $this->info('Generating Orens Pro Flowchart and Feature List PDF...');
    
    try {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.app_flow_pdf');
        $pdf->save(base_path('OrensPro_Flowchart_List.pdf'));
        $this->info('PDF successfully generated at: ' . base_path('OrensPro_Flowchart_List.pdf'));
    } catch (\Exception $e) {
        $this->error('Failed to generate PDF: ' . $e->getMessage());
    }
})->purpose('Generate Orens Pro Flowchart and Feature List PDF');

Artisan::command('generate:activity-pdf', function () {
    $this->info('Generating Orens Pro Activity Diagram Rincian PDF...');
    
    try {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.activity_pdf');
        $pdf->save(base_path('OrensPro_Activity_Diagrams.pdf'));
        $this->info('PDF successfully generated at: ' . base_path('OrensPro_Activity_Diagrams.pdf'));
    } catch (\Exception $e) {
        $this->error('Failed to generate PDF: ' . $e->getMessage());
    }
})->purpose('Generate Orens Pro Activity Diagram Details PDF');

Artisan::command('generate:erd-pdf', function () {
    $this->info('Generating Orens Pro Database ERD Specification PDF...');
    
    try {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.database_erd_pdf');
        $pdf->save(base_path('OrensPro_Database_ERD.pdf'));
        $this->info('PDF successfully generated at: ' . base_path('OrensPro_Database_ERD.pdf'));
    } catch (\Exception $e) {
        $this->error('Failed to generate PDF: ' . $e->getMessage());
    }
})->purpose('Generate Orens Pro Database ERD Specification PDF');




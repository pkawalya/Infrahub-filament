<?php

namespace Database\Seeders;

use App\Models\CdeDocument;
use App\Models\CdeProject;
use App\Models\User;
use Illuminate\Database\Seeder;

class CdeDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $projects = CdeProject::all();

        if ($projects->isEmpty()) {
            return;
        }

        $drawingsData = [
            [
                'document_number' => 'DWG-ARC-001',
                'title' => 'Main Terminal Floor Plan & Layout',
                'description' => 'Architectural floor plan layout showing structural grid lines, entryways, and core elevator shafts.',
                'discipline' => 'Architectural',
                'status' => 'approved',
                'revision' => 'Rev C',
                'file_size' => 4520000,
                'file_type' => 'pdf',
            ],
            [
                'document_number' => 'DWG-STR-102',
                'title' => 'Foundation Rebar & Beam Schedule',
                'description' => 'Structural reinforcement detail for pile caps, grade beams, and column starter bars.',
                'discipline' => 'Structural',
                'status' => 'approved',
                'revision' => 'Rev B',
                'file_size' => 6100000,
                'file_type' => 'pdf',
            ],
            [
                'document_number' => 'DWG-MEP-201',
                'title' => 'HVAC & Ductwork Schematic Layout',
                'description' => 'Mechanical electrical plumbing ductwork routing, air handler unit positions, and diffuser locations.',
                'discipline' => 'MEP',
                'status' => 'under_review',
                'revision' => 'Rev A',
                'file_size' => 3800000,
                'file_type' => 'pdf',
            ],
            [
                'document_number' => 'SPC-CIV-004',
                'title' => 'Geotechnical Soil Investigation & Compaction Report',
                'description' => 'Standard Proctor density testing results, bearing capacity analysis, and subgrade stabilization specs.',
                'discipline' => 'Specs & Reports',
                'status' => 'approved',
                'revision' => 'Rev 01',
                'file_size' => 2400000,
                'file_type' => 'pdf',
            ],
            [
                'document_number' => 'DWG-ELE-305',
                'title' => 'Single Line Electrical Distribution Diagram',
                'description' => 'High voltage transformer connections, main switchgear board schematics, and emergency generator routing.',
                'discipline' => 'MEP',
                'status' => 'wip',
                'revision' => 'Draft',
                'file_size' => 5120000,
                'file_type' => 'pdf',
            ],
            [
                'document_number' => 'DWG-ARC-020',
                'title' => 'Curtain Wall & Exterior Elevation Details',
                'description' => 'Glazing specifications, thermal barrier flashing details, and aluminum mullion anchor points.',
                'discipline' => 'Architectural',
                'status' => 'approved',
                'revision' => 'Rev B',
                'file_size' => 7800000,
                'file_type' => 'pdf',
            ],
            [
                'document_number' => 'SPC-STR-012',
                'title' => 'Structural Steel Welding & NDT Inspection Standard',
                'description' => 'Non-destructive testing protocol, ultrasonic weld testing standards, and torque values for A325 bolts.',
                'discipline' => 'Specs & Reports',
                'status' => 'published',
                'revision' => 'Final',
                'file_size' => 1950000,
                'file_type' => 'pdf',
            ],
        ];

        foreach ($projects as $project) {
            $user = User::where('company_id', $project->company_id)->first() ?? User::first();

            $folder = \App\Models\CdeFolder::firstOrCreate([
                'company_id' => $project->company_id,
                'cde_project_id' => $project->id,
                'name' => 'Blueprints & Specifications',
            ], [
                'created_by' => $user?->id,
            ]);

            foreach ($drawingsData as $index => $doc) {
                CdeDocument::updateOrCreate(
                    [
                        'company_id' => $project->company_id,
                        'cde_project_id' => $project->id,
                        'document_number' => $doc['document_number'] . '-P' . $project->id,
                    ],
                    [
                        'cde_folder_id' => $folder->id,
                        'title' => $doc['title'],
                        'description' => $doc['description'],
                        'discipline' => $doc['discipline'],
                        'status' => $doc['status'],
                        'revision' => $doc['revision'],
                        'file_size' => $doc['file_size'],
                        'file_type' => $doc['file_type'],
                        'uploaded_by' => $user?->id,
                    ]
                );
            }
        }
    }
}

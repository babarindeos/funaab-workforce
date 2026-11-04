<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Starting list of document categories and types. Registry can add,
 * rename or remove them in Admin → Documents → Categories & Types.
 * Re-running only adds what is missing.
 */
class DocumentCategorySeeder extends Seeder
{
    private array $list = [
        'Appointment Records' => [
            'Letter of Offer of Appointment', 'Acceptance of Offer', 'Letter of Assumption of Duty',
            'Letter of Confirmation of Appointment', 'Letter of Conversion / Transfer of Service',
        ],
        'Promotion & Increment Records' => [
            'Promotion Letter', 'Step Increment Letter', 'Upgrading / Conversion Letter', 'Salary Adjustment Letter',
        ],
        'Educational Qualifications' => [
            'First School Leaving Certificate', 'SSCE / WAEC / NECO Result', 'OND / NCE Certificate', 'HND Certificate',
            "Bachelor's Degree Certificate", "Master's Degree Certificate", 'Ph.D. Certificate', 'Academic Transcript',
            'NYSC Discharge / Exemption Certificate',
        ],
        'Professional Qualifications' => [
            'Professional Certificate', 'Professional Body Membership', 'Practising Licence',
        ],
        'Personal & Identity Records' => [
            'Birth Certificate / Declaration of Age', 'Certificate of State of Origin', 'Marriage Certificate',
            'Change of Name (Affidavit / Gazette)', 'National Identification (NIN) Slip', 'Passport Photograph',
        ],
        'Commendations & Disciplinary Records' => [
            'Letter of Commendation', 'Query', 'Reply to Query', 'Warning Letter', 'Disciplinary Decision',
        ],
        'Leave & Training Records' => [
            'Study Leave Approval', 'Sabbatical Leave Approval', 'Training / Course Certificate',
        ],
    ];

    public function run(): void
    {
        $order = 0;
        foreach ($this->list as $category => $types) {
            $order++;
            $catId = DB::table('document_categories')->where('name', $category)->value('id')
                ?? DB::table('document_categories')->insertGetId([
                    'uuid' => (string) Str::uuid(), 'name' => $category, 'sort_order' => $order,
                    'created_at' => now(), 'updated_at' => now(),
                ]);

            foreach ($types as $i => $type) {
                $exists = DB::table('document_types')->where('document_category_id', $catId)->where('name', $type)->exists();
                if (!$exists) {
                    DB::table('document_types')->insert([
                        'uuid' => (string) Str::uuid(), 'document_category_id' => $catId, 'name' => $type,
                        'sort_order' => $i + 1, 'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}

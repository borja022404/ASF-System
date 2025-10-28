<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Symptom;

class SymptomsTableSeeder extends Seeder
{
    public function run()
    {
        $symptoms = [
            // LOW RISK
            ['name' => 'Loss of appetite', 'risk_level' => 'low', 'description' => 'Pig eats less or refuses to eat'],
            ['name' => 'Lethargy / Weakness', 'risk_level' => 'low', 'description' => 'Reduced activity, lying down more often'],
            ['name' => 'Slight fever', 'risk_level' => 'low', 'description' => 'Up to 40–41°C – noticeable if measured or through warm skin'],
            ['name' => 'Reduced social behavior', 'risk_level' => 'low', 'description' => 'Isolation from herd or less playful'],
            ['name' => 'Dry or rough hair coat', 'risk_level' => 'low', 'description' => 'Dull appearance of the skin and hair'],
            ['name' => 'Mild coughing or breathing difficulty', 'risk_level' => 'low', 'description' => 'Occasional signs of discomfort'],
            ['name' => 'Slight weight loss', 'risk_level' => 'low', 'description' => 'May not be immediately obvious'],
            ['name' => 'Constipation followed by diarrhea', 'risk_level' => 'low', 'description' => 'Initial constipation then diarrhea'],
            ['name' => 'Reduced fertility in sows', 'risk_level' => 'low', 'description' => 'Irregular heat cycles or abortion'],

            // MEDIUM RISK
            ['name' => 'High fever', 'risk_level' => 'medium', 'description' => '40.5–42°C – persistent, not responsive to antibiotics'],
            ['name' => 'Red to purplish skin discoloration', 'risk_level' => 'medium', 'description' => 'Especially on ears, snout, abdomen, and legs'],
            ['name' => 'Swelling around the eyes and neck', 'risk_level' => 'medium', 'description' => 'Visible puffiness'],
            ['name' => 'Watery discharge from eyes or nose', 'risk_level' => 'medium', 'description' => 'Clear or watery secretion'],
            ['name' => 'Vomiting', 'risk_level' => 'medium', 'description' => 'May contain food or bile'],
            ['name' => 'Diarrhea (sometimes bloody)', 'risk_level' => 'medium', 'description' => 'Can cause dehydration'],
            ['name' => 'Labored or rapid breathing', 'risk_level' => 'medium', 'description' => 'Noticeable chest movement'],
            ['name' => 'Unsteady walking / staggering gait', 'risk_level' => 'medium', 'description' => 'Weakness in legs'],
            ['name' => 'Shivering or trembling', 'risk_level' => 'medium', 'description' => 'Due to fever and internal pain'],
            ['name' => 'Reduced body temperature in extremities', 'risk_level' => 'medium', 'description' => 'Ears and limbs feel cold'],
            ['name' => 'Abortion in pregnant sows', 'risk_level' => 'medium', 'description' => 'Fetal death or premature birth'],
            ['name' => 'Pale mucous membranes', 'risk_level' => 'medium', 'description' => 'Inside mouth or eyes appear whitish'],

            // HIGH RISK
            ['name' => 'Severe skin hemorrhages', 'risk_level' => 'high', 'description' => 'Dark red to black patches – especially on ears, abdomen, and legs'],
            ['name' => 'Open skin sores or lesions', 'risk_level' => 'high', 'description' => 'Visible bleeding or crusted spots'],
            ['name' => 'Bloody diarrhea', 'risk_level' => 'high', 'description' => 'With foul odor'],
            ['name' => 'Bloody froth from nose or mouth', 'risk_level' => 'high', 'description' => 'Severe internal bleeding signs'],
            ['name' => 'Difficulty standing or complete collapse', 'risk_level' => 'high', 'description' => 'Extreme weakness'],
            ['name' => 'Severe vomiting', 'risk_level' => 'high', 'description' => 'May contain blood'],
            ['name' => 'Foaming at the mouth', 'risk_level' => 'high', 'description' => 'Due to respiratory distress'],
            ['name' => 'Convulsions or muscle twitching', 'risk_level' => 'high', 'description' => 'Just before death'],
            ['name' => 'Blue-purple discoloration of ears, tail, and legs', 'risk_level' => 'high', 'description' => 'Sign of severe hypoxia'],
            ['name' => 'Sudden death without visible symptoms', 'risk_level' => 'high', 'description' => 'Common in acute outbreaks'],
            ['name' => 'Coma or unresponsiveness', 'risk_level' => 'high', 'description' => 'Final phase before death'],
        ];

        foreach ($symptoms as $symptom) {
            Symptom::create($symptom);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Neighborhood;
use Illuminate\Database\Seeder;

class NeighborhoodSeeder extends Seeder
{
    public function run(): void
    {
        $neighborhoods = [
            // Lagos Island
            ['name' => 'Victoria Island', 'lga' => 'Eti-Osa', 'sort_order' => 1],
            ['name' => 'Ikoyi', 'lga' => 'Eti-Osa', 'sort_order' => 2],
            ['name' => 'Lagos Island', 'lga' => 'Lagos Island', 'sort_order' => 3],
            
            // Lekki Axis
            ['name' => 'Lekki Phase 1', 'lga' => 'Eti-Osa', 'sort_order' => 4],
            ['name' => 'Lekki Phase 2', 'lga' => 'Eti-Osa', 'sort_order' => 5],
            ['name' => 'Chevron/Lekki', 'lga' => 'Eti-Osa', 'sort_order' => 6],
            ['name' => 'Ajah', 'lga' => 'Eti-Osa', 'sort_order' => 7],
            ['name' => 'Sangotedo', 'lga' => 'Eti-Osa', 'sort_order' => 8],
            ['name' => 'Abraham Adesanya', 'lga' => 'Eti-Osa', 'sort_order' => 9],
            ['name' => 'Ikate', 'lga' => 'Eti-Osa', 'sort_order' => 10],
            
            // Mainland - Ikeja Axis
            ['name' => 'Ikeja', 'lga' => 'Ikeja', 'sort_order' => 11],
            ['name' => 'Ikeja GRA', 'lga' => 'Ikeja', 'sort_order' => 12],
            ['name' => 'Maryland', 'lga' => 'Ikeja', 'sort_order' => 13],
            ['name' => 'Ojodu', 'lga' => 'Ikeja', 'sort_order' => 14],
            ['name' => 'Ogba', 'lga' => 'Ikeja', 'sort_order' => 15],
            ['name' => 'Agidingbi', 'lga' => 'Ikeja', 'sort_order' => 16],
            ['name' => 'Alausa', 'lga' => 'Ikeja', 'sort_order' => 17],
            
            // Mainland - Yaba/Surulere
            ['name' => 'Yaba', 'lga' => 'Yaba', 'sort_order' => 18],
            ['name' => 'Surulere', 'lga' => 'Surulere', 'sort_order' => 19],
            ['name' => 'Ebute Metta', 'lga' => 'Lagos Mainland', 'sort_order' => 20],
            ['name' => 'Akoka', 'lga' => 'Yaba', 'sort_order' => 21],
            
            // Mainland - Gbagada/Magodo
            ['name' => 'Gbagada', 'lga' => 'Kosofe', 'sort_order' => 22],
            ['name' => 'Magodo', 'lga' => 'Kosofe', 'sort_order' => 23],
            ['name' => 'Anthony', 'lga' => 'Kosofe', 'sort_order' => 24],
            ['name' => 'Ogudu', 'lga' => 'Kosofe', 'sort_order' => 25],
            ['name' => 'Ketu', 'lga' => 'Kosofe', 'sort_order' => 26],
            
            // Mainland - Festac/Apapa
            ['name' => 'Festac Town', 'lga' => 'Amuwo-Odofin', 'sort_order' => 27],
            ['name' => 'Apapa', 'lga' => 'Apapa', 'sort_order' => 28],
            ['name' => 'Amuwo Odofin', 'lga' => 'Amuwo-Odofin', 'sort_order' => 29],
            
            // Mainland - Isolo/Oshodi
            ['name' => 'Isolo', 'lga' => 'Isolo', 'sort_order' => 30],
            ['name' => 'Oshodi', 'lga' => 'Oshodi-Isolo', 'sort_order' => 31],
            ['name' => 'Ejigbo', 'lga' => 'Isolo', 'sort_order' => 32],
            
            // Mainland - Mushin/Ilupeju
            ['name' => 'Mushin', 'lga' => 'Mushin', 'sort_order' => 33],
            ['name' => 'Ilupeju', 'lga' => 'Mushin', 'sort_order' => 34],
            ['name' => 'Palmgrove', 'lga' => 'Lagos Mainland', 'sort_order' => 35],
            
            // Mainland - Other Areas
            ['name' => 'Agege', 'lga' => 'Agege', 'sort_order' => 36],
            ['name' => 'Alimosho', 'lga' => 'Alimosho', 'sort_order' => 37],
            ['name' => 'Ikorodu', 'lga' => 'Ikorodu', 'sort_order' => 38],
            ['name' => 'Badagry', 'lga' => 'Badagry', 'sort_order' => 39],
            ['name' => 'Epe', 'lga' => 'Epe', 'sort_order' => 40],
            
            // Other
            ['name' => 'Other Lagos Area', 'lga' => null, 'sort_order' => 99],
        ];

        foreach ($neighborhoods as $neighborhood) {
            Neighborhood::updateOrCreate(
                ['name' => $neighborhood['name'], 'city' => 'Lagos'],
                array_merge($neighborhood, [
                    'city' => 'Lagos',
                    'state' => 'Lagos',
                ])
            );
        }
    }
}

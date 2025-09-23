<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class ModulesAndSubModulesSeeder extends Seeder
{
    /**
     * Untuk setiap course, buat 3–6 modul dan tiap modul 2–5 submodul.
     */
    public function run()
    {
        $courses = \App\Models\Course::all();
        foreach ($courses as $course) {
            $numModules = rand(3, 6);
            for ($m = 1; $m <= $numModules; $m++) {
                $module = \App\Models\Module::factory()->create([
                    'course_id' => $course->id,
                    'urutan' => $m,
                ]);

                $numSubs = rand(2, 5);
                for ($s = 1; $s <= $numSubs; $s++) {
                    \App\Models\SubModule::factory()->create([
                        'module_id' => $module->id,
                        'urutan' => $s,
                    ]);
                }
            }
        }
    }
}



<?php

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Module;
use App\Models\SubModule;

class ModulesAndSubModulesSeeder extends Seeder
{
    /**
     * Untuk setiap course, buat 3–6 modul dan tiap modul 2–5 submodul.
     */
    public function run()
    {
        $courses = Course::all();
        foreach ($courses as $course) {
            $numModules = rand(3, 6);
            for ($m = 1; $m <= $numModules; $m++) {
                $module = factory(Module::class)->create([
                    'course_id' => $course->id,
                    'urutan' => $m,
                ]);

                $numSubs = rand(2, 5);
                for ($s = 1; $s <= $numSubs; $s++) {
                    factory(SubModule::class)->create([
                        'module_id' => $module->id,
                        'urutan' => $s,
                    ]);
                }
            }
        }
    }
}



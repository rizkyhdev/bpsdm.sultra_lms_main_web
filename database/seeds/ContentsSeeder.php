<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\SubModule;
use App\Models\Content;

class ContentsSeeder extends Seeder
{
    /**
     * Untuk setiap submodul, buat 1–3 materi dengan tipe campuran.
     */
    public function run()
    {
        $types = ['text', 'pdf', 'video', 'audio', 'image'];
        foreach (SubModule::all() as $sub) {
            $count = rand(1, 3);
            for ($i = 1; $i <= $count; $i++) {
                $type = $types[array_rand($types)];
                $slug = Str::slug($sub->judul.'-'.$i);
                $file = [
                    'text' => "storage/app/public/contents/{$slug}.html",
                    'pdf' => "storage/app/public/contents/{$slug}.pdf",
                    'video' => "storage/app/public/contents/{$slug}.mp4",
                    'audio' => "storage/app/public/contents/{$slug}.mp3",
                    'image' => "storage/app/public/contents/{$slug}.jpg",
                ][$type];

                factory(Content::class)->create([
                    'sub_module_id' => $sub->id,
                    'tipe' => $type,
                    'urutan' => $i,
                    'file_path' => $file,
                    'judul' => 'Materi: '.$sub->judul.' #'.$i,
                ]);
            }
        }
    }
}



<?php

namespace Database\Seeders;

use App\Models\Content;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $content = Content::where('flag', 'term')->first();
        if(is_null($content)){
            Content::create([
                'id' => Str::orderedUuid(),
                'title' => 'Term & Condition',
                'image' => null,
                'content' => null,
                'flag' => 'term',
            ]);
        }
    }
}

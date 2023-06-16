<?php

namespace Database\Seeders;

use App\Models\Cost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $super = User::role('super_admin')->first();
        Cost::insert([
            [
                'id' => Str::orderedUuid(),
                'name' => 'button',
                'slug' => 'button',
                'point' => 100,
                'description' => '<b>button description</b>',
                'created_by' => $super->id
            ],
            [
                'id' => Str::orderedUuid(),
                'name' => 'media',
                'slug' => 'media',
                'point' => 100,
                'description' => '<b>media description</b>',
                'created_by' => $super->id
            ],
            [
                'id' => Str::orderedUuid(),
                'name' => 'schedule',
                'slug' => 'schedule',
                'point' => 100,
                'description' => '<b>schedule description</b>',
                'created_by' => $super->id
            ],
            [
                'id' => Str::orderedUuid(),
                'name' => 'template',
                'slug' => 'template',
                'point' => 100,
                'description' => '<b>template description</b>',
                'created_by' => $super->id
            ],
            [
                'id' => Str::orderedUuid(),
                'name' => 'contact',
                'slug' => 'contact',
                'point' => 100,
                'description' => '<b>contact description</b>',
                'created_by' => $super->id
            ],
            [
                'id' => Str::orderedUuid(),
                'name' => 'location',
                'slug' => 'location',
                'point' => 100,
                'description' => '<b>location description</b>',
                'created_by' => $super->id
            ],
            [
                'id' => Str::orderedUuid(),
                'name' => 'text',
                'slug' => 'text',
                'point' => 100,
                'description' => '<b>text description</b>',
                'created_by' => $super->id
            ],
            [
                'id' => Str::orderedUuid(),
                'name' => 'manual',
                'slug' => 'manual',
                'point' => 100,
                'description' => '<b>manual description</b>',
                'created_by' => $super->id
            ],
            [
                'id' => Str::orderedUuid(),
                'name' => 'now',
                'slug' => 'now',
                'point' => 100,
                'description' => '<b>now description</b>',
                'created_by' => $super->id
            ],
        ]);
    }
}
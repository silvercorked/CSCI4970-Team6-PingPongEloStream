<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;

class UserSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $password = 'Attic383';
        User::factory()->create([
            'name' => 'Alex Wissing',
            'email' => 'awissing@unomaha.edu',
            'admin' => 2,
            'password' => bcrypt($password)
        ]);
        User::factory()->create([
            'name' => 'Colin Gregurich',
            'email' => 'cgregurich@unomaha.edu',
            'admin' => 2,
            'password' => bcrypt($password)
        ]);
        User::factory()->create([
            'name' => 'Terrah Quinlan',
            'email' => 'tquinlan@unomaha.edu',
            'admin' => 2,
            'password' => bcrypt($password)
        ]);
        User::factory()->create([
            'name' => 'Kelvin Chin',
            'email' => 'kchin@unomaha.edu',
            'admin' => 2,
            'password' => bcrypt($password)
        ]);
        User::factory(6)->create();
    }
}

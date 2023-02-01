<?php

namespace Database\Seeders;

use App\Models\Person;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'MadeFy',
            'email' => 'cc@madefy.com.br',
            'password' => bcrypt('MadeFy@2022'),
            'nickname' => 'MadeFy'
        ]);

        $user->group()->attach(2);

        Person::create([
            'name' => $user->name,
            'cpf' => '00000000000',
            'user_id' => $user->id,
            'rg' => '000',
            'birthdate' => Carbon::now(),
            'phone' => '5562994421733',
            'is_whatsapp' => true,
            'approved_at' => Carbon::now(),
            'approved_by' => $user->id
        ]);
    }
}

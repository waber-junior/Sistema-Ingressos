<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = [
            0 => [
                'name' => 'Revendedores',
                'description' => 'Grupo de Revendedores'
            ],
            1 => [
                'name' => 'Administradores',
                'description' => 'Grupo de Administradores da Casabella'
            ],
            2 => [
                'name' => 'Funcionários',
                'description' => 'Grupo de Funcionários da Casabella'
            ]
        ];

        foreach ($groups as $gp) {
            if (!$this->isExist($gp['name'])) {
                Group::create($gp);
            }
        }
    }


    protected function isExist($name)
    {
        $group = Group::where('name', $name)->count();
        if ($group > 0) {
            return true;
        }
        return false;
    }
}

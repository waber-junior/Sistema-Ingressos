<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function cpfIsValid ($cpf = null)
    {
         // Extrai somente os números
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );

        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    public function hasPermission(User $user, $permission)
    {
        $group = $user->group;
        if (sizeof($group) === 0) {
            return false;
        }

        if ($group[0]->name === 'Administradores') {
            return true;
        }

        $role = Permission::where('name', $permission)
            ->with('rules', function ($q) use($group) {
                $q->where('group_id', $group[0]->id);
            })
            ->count();
        if ($role > 0) {
            return true;
        }
        return false;
    }
}

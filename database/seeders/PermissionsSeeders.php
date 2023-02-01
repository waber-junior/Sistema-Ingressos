<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionsSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            0 => [
                'name' => 'Patrocinador',
                'description' => 'visualização do cadastro de patrocinador',
                'module' => 'partner'
            ],
            1 => [
                'name' => 'Cadastro Patrocinador',
                'description' => 'Alteração no cadastro de patrocinador',
                'module' => 'partner'
            ],
            2 => [
                'name' => 'Solicitações',
                'description' => 'Acesso a solicitações',
                'module' => 'tickets'
            ],
            3 => [
                'name' => 'Chat',
                'description' => 'Acesso ao Chat',
                'module' => 'chat'
            ],
            4 => [
                'name' => 'Termos',
                'description' => 'Criar / editar Termos',
                'module' => 'terms'
            ],
            5 => [
                'name' => 'Auditoria',
                'description' => 'Acesso ao cadastro de auditoria.',
                'module' => 'audit'
            ],
            6 => [
                'name' => 'Usuário',
                'description' => 'Cadastro de usuário',
                'module' => 'users'
            ],
            7 => [
                'name' => 'Categoria e Grupo',
                'description' => 'Cadastro de categoria e grupos de usuário',
                'module' => 'users'
            ],
            8 => [
                'name' => 'Permissões',
                'description' => 'Acesso a permissões do grupo',
                'module' => 'users'
            ],
            9 => [
                'name' => 'Campanhas',
                'description' => 'Visualizar Campanhas',
                'module' => 'campaign'
            ],
            10 => [
                'name' => 'Campanhas',
                'description' => 'Adicionar Campanhas',
                'module' => 'campaign'
            ],
            11 => [
                'name' => 'Termos',
                'description' => 'Cadastrar Termos',
                'module' => 'term'
            ],
            12 => [
                'name' => 'NFT',
                'description' => 'Visualizar NFT',
                'module' => 'nft'
            ],
            13 => [
                'name' => 'Cadastrar NFT',
                'description' => 'Alterar/ Cadastrar NFT',
                'module' => 'nft'
            ],
            14 => [
                'name' => 'Classificação e Categoria',
                'description' => 'Cadastrar Classificação e Categoria',
                'module' => 'nft'
            ],
            15 => [
                'name' => 'Floral',
                'description' => 'Visualizar Floral',
                'module' => 'floral'
            ],
            16 => [
                'name' => 'Movimentação de Floral',
                'description' => 'Movimentar Floral',
                'module' => 'floral'
            ],
            17 =>[
                'name' => 'Auditoria',
                'description' => 'Visualizar e cadastrar Tickets',
                'module' => 'auditoria'
            ],
            18 =>[
                'name' => 'Transferência NFT',
                'description' => 'Alterar/ Cadastrar NFT',
                'module' => 'nft'
            ],
        ];

        foreach ($permissions as $item) {
            if (!$this->isExist($item['name'])) {
                Permission::create($item);
            }
        }
    }

    protected function isExist ($name)
    {
        $permission = Permission::where('name', $name)->count();
        if ($permission > 0) {
            return true;
        }
        return false;
    }
}

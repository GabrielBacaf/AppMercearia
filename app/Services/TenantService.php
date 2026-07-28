<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

class TenantService
{
    /**
     * Cria um novo Tenant (loja), seu domínio e seu usuário Admin inicial.
     */
    public function createTenant(array $data): Tenant
    {
        // Cria o tenant (isso vai criar o banco de dados e rodar as migrations na pasta tenant/)
        $tenant = Tenant::create(['id' => $data['id']]);

        // Cria o subdomínio associado
        $tenant->domains()->create(['domain' => $data['domain']]);

        // Executa código no contexto do novo tenant recém-criado
        $tenant->run(function () use ($data) {
            // Cria o usuário Admin inicial na mercearia (já no banco de dados da loja)
            $user = \App\Models\User::create([
                'name' => $data['admin_name'],
                'login' => $data['admin_login'],
                'email' => $data['admin_email'],
                'password' => Hash::make($data['admin_password']),
            ]);

            // Cria o perfil (Role) 'admin' caso ele não exista no banco de dados da loja
            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
            
            // Vincula o perfil 'admin' ao usuário inicial
            $user->assignRole($role);
        });

        return $tenant;
    }

    /**
     * Atualiza os dados ou o domínio de um Tenant.
     */
    public function updateTenant(Tenant $tenant, array $data): Tenant
    {
        // Se enviou um novo domínio, atualiza
        if (isset($data['domain'])) {
            $tenant->domains()->first()->update(['domain' => $data['domain']]);
        }

        // Atualiza campos adicionais na tabela tenants (na coluna json `data` por padrão no tenancy)
        if (isset($data['data'])) {
            $tenant->update($data['data']);
        }

        return $tenant;
    }

    /**
     * Remove um Tenant do sistema.
     */
    public function deleteTenant(Tenant $tenant): void
    {
        $tenant->delete();
    }
}

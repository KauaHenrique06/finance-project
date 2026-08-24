<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public const PERMISSIONS = [
        'transaction.view' => 'Visualizar transações',
        'transaction.create' => 'Criar transações',
        'transaction.update' => 'Editar transações',
        'transaction.delete' => 'Excluir transações',
        'transaction.pay' => 'Marcar transação como paga',

        'group.view' => 'Visualizar grupos de transação',
        'group.create' => 'Criar grupos de transação',
        'group.update' => 'Editar grupos de transação',
        'group.delete' => 'Excluir grupos de transação',
        'group.assignParticipant' => 'Adicionar participantes ao grupo',
        'group.assignInstance' => 'Vincular instância do WhatsApp ao grupo',
        'group.viewMessage' => 'Visualizar mensagens do grupo',
        'group.sendMessage' => 'Enviar mensagens no grupo',

        'whatsapp.view' => 'Visualizar instâncias do WhatsApp',
        'whatsapp.create' => 'Criar instâncias do WhatsApp',
        'whatsapp.delete' => 'Excluir instâncias do WhatsApp',

        'notification.view' => 'Visualizar notificações',
        'notification.update' => 'Marcar notificações como lidas',

        'user.view' => 'Visualizar usuários',
        'user.update' => 'Editar o próprio perfil',

        'role.view' => 'Visualizar cargos',
        'permission.view' => 'Visualizar permissões',
    ];

    public const CLIENT_PERMISSIONS = [
        'transaction.view',
        'transaction.create',
        'transaction.update',
        'transaction.delete',
        'transaction.pay',

        'group.view',
        'group.create',
        'group.update',
        'group.delete',
        'group.assignParticipant',
        'group.assignInstance',
        'group.viewMessage',
        'group.sendMessage',

        'whatsapp.view',
        'whatsapp.create',
        'whatsapp.delete',

        'notification.view',
        'notification.update',

        'user.update',
    ];

    public const GUARD = 'api';

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $name => $label) {
            Permission::updateOrCreate(
                ['name' => $name, 'guard_name' => self::GUARD],
                ['label' => $label]
            );
        }

        Permission::where('guard_name', self::GUARD)
            ->whereNotIn('name', array_keys(self::PERMISSIONS))
            ->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}

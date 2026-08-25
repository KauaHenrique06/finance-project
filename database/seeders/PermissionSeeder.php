<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public const PERMISSIONS = [
        'event.view' => 'Visualizar eventos',
        'event.create' => 'Criar eventos',
        'event.update' => 'Editar eventos',
        'event.delete' => 'Excluir eventos',
        'event.assignInstance' => 'Vincular instância do WhatsApp ao evento',

        'group.view' => 'Visualizar grupos de transação',
        'group.create' => 'Criar grupos de transação',
        'group.update' => 'Editar grupos de transação',
        'group.delete' => 'Excluir grupos de transação',
        'group.assignParticipant' => 'Adicionar participantes ao grupo',
        'group.viewMessage' => 'Visualizar mensagens do grupo',
        'group.sendMessage' => 'Enviar mensagens no grupo',

        'transaction.pay' => 'Marcar transação como paga',

        'whatsapp.view' => 'Visualizar instâncias do WhatsApp',
        'whatsapp.create' => 'Criar instâncias do WhatsApp',
        'whatsapp.delete' => 'Excluir instâncias do WhatsApp',

        'notification.view' => 'Visualizar notificações',
        'notification.update' => 'Marcar notificações como lidas',

        'user.view' => 'Visualizar usuários',
        'user.update' => 'Editar o próprio perfil',

        'dashboard.view' => 'Visualizar dashboard',

        'role.view' => 'Visualizar cargos',
        'permission.view' => 'Visualizar permissões',
    ];

    public const CLIENT_PERMISSIONS = [
        'event.view',
        'event.create',
        'event.update',
        'event.delete',
        'event.assignInstance',

        'group.view',
        'group.create',
        'group.update',
        'group.delete',
        'group.assignParticipant',
        'group.viewMessage',
        'group.sendMessage',

        'transaction.pay',

        'whatsapp.view',
        'whatsapp.create',
        'whatsapp.delete',

        'notification.view',
        'notification.update',

        'dashboard.view',

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

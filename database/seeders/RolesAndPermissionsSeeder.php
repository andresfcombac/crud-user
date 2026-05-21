<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
{
    // 1. Crear o buscar Permisos básicos de forma segura
    $ver = Permission::firstOrCreate(['nombre' => 'ver-usuarios'], ['descripcion' => 'Ver lista de usuarios']);
    $crear = Permission::firstOrCreate(['nombre' => 'crear-usuarios'], ['descripcion' => 'Crear nuevos usuarios']);
    $editar = Permission::firstOrCreate(['nombre' => 'editar-usuarios'], ['descripcion' => 'Modificar usuarios existentes']);
    $eliminar = Permission::firstOrCreate(['nombre' => 'eliminar-usuarios'], ['descripcion' => 'Borrar usuarios del sistema']);

    // 2. Crear o buscar Roles de forma segura
    $admin = Role::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Acceso total']);
    $editor = Role::firstOrCreate(['nombre' => 'Editor'], ['descripcion' => 'Puede ver y modificar pero no borrar']);
    $usuario = Role::firstOrCreate(['nombre' => 'Usuario'], ['descripcion' => 'Acceso limitado de lectura']);

    // 3. Sincronizar Permisos a Roles (Evita duplicados en la tabla intermedia)
    $admin->permissions()->sync([$ver->id, $crear->id, $editar->id, $eliminar->id]);
    $editor->permissions()->sync([$ver->id, $editar->id]);
    $usuario->permissions()->sync([$ver->id]);
}

}

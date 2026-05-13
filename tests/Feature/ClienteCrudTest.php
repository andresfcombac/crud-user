<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Cliente;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClienteCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_usuario_no_autenticado_es_redireccionado_al_login()
    {
        $response = $this->get('/clientes');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_un_administrador_puede_iniciar_sesion_correctamente()
    {
        $admin = Cliente::create([
            'nombres' => 'Admin',
            'apellidos' => 'Pruebas',
            'correo' => 'admin@test.com',
            'cargo' => 'QA Engineer',
            'tipo_usuario' => 'Administrador',
            'password' => Hash::make('password123')
        ]);

        $response = $this->post('/login', [
            'correo' => 'admin@test.com',
            'password' => 'password123'
        ]);

        // Sintaxis corregida aquí:
        $response->assertStatus(302);
        $response->assertRedirect('/clientes');
        $this->assertEquals('Administrador', session('user_id') ? $admin->tipo_usuario : '');
    }

    public function test_el_formulario_de_registro_exige_un_correo_valido_y_unico()
    {
        session(['user_id' => 1, 'user_rol' => 'Administrador']);

        Cliente::create([
            'nombres' => 'Juan',
            'apellidos' => 'Perez',
            'correo' => 'duplicado@test.com',
            'cargo' => 'Soporte',
            'tipo_usuario' => 'Soporte',
            'password' => Hash::make('123456')
        ]);

        $response = $this->post('/clientes', [
            'nombres' => 'Carlos',
            'apellidos' => 'Gomez',
            'correo' => 'duplicado@test.com',
            'cargo' => 'Desarrollador',
            'tipo_usuario' => 'Operador',
            'password' => '123456'
        ]);

        $response->assertSessionHasErrors(['correo']);
    }

    public function test_la_contrasena_debe_tener_minimo_seis_caracteres()
    {
        session(['user_id' => 1, 'user_rol' => 'Administrador']);

        $response = $this->post('/clientes', [
            'nombres' => 'Ana',
            'apellidos' => 'Rios',
            'correo' => 'ana@test.com',
            'cargo' => 'Contadora',
            'tipo_usuario' => 'Operador',
            'password' => '1234'
        ]);

        $response->assertSessionHasErrors(['password']);
    }
}

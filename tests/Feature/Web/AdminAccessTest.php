<?php

namespace Tests\Feature\Web;

use App\Models\Conta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_trying_to_access_admin(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_user_can_login_and_access_admin_dashboard(): void
    {
        $user = User::create([
            'name' => 'Conta Web',
            'email' => 'web@example.com',
            'password' => 'password',
        ]);

        $conta = Conta::create([
            'nome_fantasia' => 'Conta Web',
            'slug' => 'conta-web',
            'email' => 'web@example.com',
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);

        $conta->usuarios()->attach($user->id, [
            'papel' => 'owner',
            'ativo' => true,
            'ultimo_acesso_em' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => 'web@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);

        $this->get('/admin')
            ->assertOk()
            ->assertSee('Painel administrativo')
            ->assertSee('Conta Web')
            ->assertSee('Centro de comando')
            ->assertSee('Inicio');
    }
}

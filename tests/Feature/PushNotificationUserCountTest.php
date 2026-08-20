<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PushNotificationUserCountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('action');
                $table->string('auditable_type');
                $table->unsignedBigInteger('auditable_id');
                $table->text('description');
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamps();
            });
        }
    }

    public function test_push_screen_shows_registered_user_counts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(2)->create(['role' => 'consumer']);
        User::factory()->create(['role' => 'salon']);
        User::factory()->count(2)->create(['role' => 'distributor']);

        $this->actingAs($admin)
            ->get(route('admin.notifications.create'))
            ->assertOk()
            ->assertSee('6 usuários cadastrados', false)
            ->assertSee('Todos os Usuários (6)', false)
            ->assertSee('Apenas Consumidores / Salões (3)', false)
            ->assertSee('Apenas Distribuidores (2)', false);
    }
}

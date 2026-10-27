<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PortalDashboardTest extends TestCase
{
    public function test_developer_dashboard_renders_live_counts_and_service_checks(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('role')->default('customer');
        });
        Schema::create('api_request_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('method', 10);
            $table->string('path');
            $table->unsignedSmallInteger('status');
            $table->unsignedInteger('duration_ms');
            $table->timestamps();
        });
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('moderation_status')->default('published');
        });

        $developer = new User(['name' => 'Developer', 'email' => 'developer@example.test', 'role' => 'developer']);
        $developer->id = 101;

        $this->actingAs($developer)
            ->get('/developer/dashboard')
            ->assertOk()
            ->assertSee('Developer dashboard')
            ->assertSee('Platform modules')
            ->assertSee('Live request feed')
            ->assertSee('portal-traffic-chart');

        $this->actingAs($developer)
            ->get('/developer/dashboard/live')
            ->assertOk()
            ->assertJsonStructure(['updatedAt', 'stats' => ['total', 'successful', 'clientErrors', 'serverErrors', 'averageDuration'], 'traffic', 'feed']);

        $admin = new User(['name' => 'Administrator', 'email' => 'admin@example.test', 'role' => 'admin']);
        $admin->id = 102;

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Admin dashboard')
            ->assertSee('Live request feed')
            ->assertSee('portal-traffic-chart');
    }
}

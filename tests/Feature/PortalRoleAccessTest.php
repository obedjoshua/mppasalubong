<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsurePortalRole;
use App\Http\Middleware\EnsureStaffUser;
use App\Models\User;
use Illuminate\Http\Request;
use Tests\TestCase;

class PortalRoleAccessTest extends TestCase
{
    public function test_administrator_has_superuser_access_to_staff_operations(): void
    {
        $admin = new User([
            'name' => 'Administrator',
            'email' => 'admin@example.test',
            'role' => 'admin',
        ]);
        $request = Request::create('/staff/dashboard');
        $request->setUserResolver(fn () => $admin);

        $response = (new EnsureStaffUser)->handle($request, fn () => response('allowed'));

        $this->assertSame('allowed', $response->getContent());
    }

    public function test_customer_cannot_access_elevated_portals_even_with_company_email(): void
    {
        $customer = new User([
            'name' => 'Company email customer',
            'email' => 'customer@masagana.ph',
            'role' => 'customer',
        ]);
        $customer->id = 101;

        $this->actingAs($customer)->get('/admin/dashboard')->assertForbidden();
        $this->actingAs($customer)->get('/developer/dashboard')->assertForbidden();
        $this->actingAs($customer)->get('/staff/dashboard')->assertRedirect(route('dashboard'));
    }

    public function test_admin_and_developer_share_equal_portal_privileges(): void
    {
        $developer = new User([
            'name' => 'Developer',
            'email' => 'developer@example.test',
            'role' => 'developer',
        ]);
        $developerRequest = Request::create('/admin/users');
        $developerRequest->setUserResolver(fn () => $developer);
        $developerResponse = (new EnsurePortalRole)->handle($developerRequest, fn () => response('allowed'), 'admin');
        $this->assertSame('allowed', $developerResponse->getContent());

        $admin = new User([
            'name' => 'Administrator',
            'email' => 'admin@example.test',
            'role' => 'admin',
        ]);
        $adminRequest = Request::create('/developer/posts');
        $adminRequest->setUserResolver(fn () => $admin);
        $adminResponse = (new EnsurePortalRole)->handle($adminRequest, fn () => response('allowed'), 'developer');
        $this->assertSame('allowed', $adminResponse->getContent());

        $developerStaffRequest = Request::create('/staff/dashboard');
        $developerStaffRequest->setUserResolver(fn () => $developer);
        $staffResponse = (new EnsureStaffUser)->handle($developerStaffRequest, fn () => response('allowed'));
        $this->assertSame('allowed', $staffResponse->getContent());
    }
}

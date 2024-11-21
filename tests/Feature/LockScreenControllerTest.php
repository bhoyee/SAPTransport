<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Mockery;

class LockScreenControllerTest extends TestCase
{
    use RefreshDatabase;
    

// In your LockScreenControllerTest.php

public function test_lockscreen_route_is_accessible()
{
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/lock'); // Use '/lock' instead of '/lockscreen'

    $response->assertOk();
    $response->assertViewIs('auth.lockscreen');
}

public function test_unlock_with_correct_password()
{
    $user = User::factory()->create(['status' => 'active']); // Set status to active
    $this->actingAs($user);
    session()->put('is_locked', true); 

    $response = $this->post('/unlock', [
        'password' => 'password' 
    ]);

    $response->assertRedirect(route('passenger.dashboard')); 
    $this->assertFalse(session()->has('is_locked')); 
}
    
    public function test_unlock_with_incorrect_password()
    {
        Log::shouldReceive('info')->twice(); // Expect 2 log messages

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/unlock', [
            'password' => 'wrong_password' 
        ]);

        $response->assertRedirect(route('lockscreen.show')); 
        $response->assertSessionHasErrors('password'); 
    }

    public function test_unlock_with_missing_password()
    {
        Log::shouldReceive('info')->twice(); // Expect 2 log messages

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/unlock'); // No password provided

        $response->assertRedirect(route('lockscreen.show'));
        $response->assertSessionHasErrors('password');
    }

    public function test_unlock_with_suspended_user()
    {
        Log::shouldReceive('info')->twice(); 

        $user = User::factory()->create(['status' => 'suspend']);
        $this->actingAs($user);

        $response = $this->post('/unlock', [
            'password' => 'password' 
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('error');
    }

    public function test_unlock_with_deleted_user()
    {
        Log::shouldReceive('info')->twice();

        $user = User::factory()->create(['status' => 'deleted']);
        $this->actingAs($user);

        $response = $this->post('/unlock', [
            'password' => 'password' 
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('error');
    }

    public function test_unlock_redirects_based_on_role()
    {
        $this->instance(Log::class, function () {
            return Mockery::mock('alias:Illuminate\Support\Facades\Log')
                ->shouldReceive('info')
                ->andReturnNull()
                ->getMock();
        });
        $admin = User::factory()->create(['status' => 'active']);
        $adminRole = Role::create(['name' => 'admin']);
        $admin->assignRole($adminRole);

        $consultant = User::factory()->create(['status' => 'active']);
        $consultantRole = Role::create(['name' => 'consultant']);
        $consultant->assignRole($consultantRole);

        $this->actingAs($admin);
        session()->put('is_locked', true);
        $response = $this->post('/unlock', ['password' => 'password']);
        $response->assertRedirect(route('admin.dashboard'));

        $this->actingAs($consultant);
        session()->put('is_locked', true);
        $response = $this->post('/unlock', ['password' => 'password']);
        $response->assertRedirect(route('staff.dashboard'));
    }

    // ... Add more tests for handleGoogleUnlock and checkSessionStatus
}
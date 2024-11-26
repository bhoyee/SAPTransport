<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase; 

    public function test_show_account_page()
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'passenger']);
        $user->assignRole($role);

        $this->actingAs($user);

        $response = $this->get(route('account.settings'));

        $response->assertOk();
        $response->assertViewIs('passenger.account');
        $response->assertViewHas('user', $user); 
    }

    public function test_update_account_with_valid_data()
    {
        Storage::fake('public'); 

        $user = User::factory()->create();
        $role = Role::create(['name' => 'passenger']);
        $user->assignRole($role);
        $this->actingAs($user);

        $response = $this->post(route('account.update'), [
            'phone' => '1234567890',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Profile updated successfully!');

        $user->refresh(); 

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'phone' => '1234567890',
        ]);
    }

    public function test_update_account_with_profile_image()
    {
        // Fake the storage for the public disk (still useful to simulate file uploads)
        Storage::fake('public');
    
        // Create a user and assign the 'passenger' role
        $user = User::factory()->create();
        $role = Role::create(['name' => 'passenger']);
        $user->assignRole($role);
    
        // Authenticate the user
        $this->actingAs($user);
    
        // Create a fake profile image file
        $file = UploadedFile::fake()->image('profile.jpg');
    
        // Perform the post request to update the account with the profile image
        $response = $this->post(route('account.update'), [ 
            'profile_image' => $file,
        ]);
    
        // Assert the redirect and success message
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Profile updated successfully!');
    
        // Refresh the user to get the updated data
        $user->refresh();
    
        // Assert that the profile_image field is populated (i.e., not null)
        $this->assertNotNull($user->profile_image);
    
        // Ensure the profile_image field contains the generated file name (based on time)
        // The file name will be time-based, so we check if it contains the 'profile.jpg' part
        $this->assertStringContainsString('profile.jpg', $user->profile_image);
    }
    

    
    public function test_update_account_with_invalid_phone()
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'passenger']);
        $user->assignRole($role);
        $this->actingAs($user);

        $response = $this->post(route('account.update'), [ 
            'phone' => 'invalid-phone', 
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('phone'); 
    }

    public function test_update_account_with_invalid_profile_image()
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'passenger']);
        $user->assignRole($role);
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf'); 

        $response = $this->post(route('account.update'), [
            'profile_image' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('profile_image'); 
    }

    public function test_update_account_with_duplicate_phone()
    {
        $user1 = User::factory()->create(['phone' => '1234567890']);
        $user2 = User::factory()->create();
        $role = Role::create(['name' => 'passenger']);
        $user2->assignRole($role);
        $this->actingAs($user2);

        $response = $this->post(route('account.update'), [ 
            'phone' => '1234567890', 
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'The phone number already exists. Please choose a different number.');
    }
}

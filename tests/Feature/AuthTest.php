<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase; // Automatically runs migrations and resets the database after each test

    /**
     * Test successful registration.
     */
    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Abdus samad',
            'email' => 'Abdus.samad1@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert: Check the status code is 201 (Created)
        $response->assertStatus(201);

        // Assert: Check that the response contains the token and user details
        $response->assertJsonStructure([
            'user' => ['id', 'name', 'email', 'created_at', 'updated_at'],
            'access_token',
            'token_type',
        ]);

        // Assert: Ensure the user was actually saved in the database
        $this->assertDatabaseHas('users', [
            'email' => 'Abdus.samad1@example.com',
        ]);
    }

    /**
     * Test registration fails with duplicate email.
     */
    public function test_registration_fails_if_email_already_exists(): void
    {
        // Create an existing user
        User::factory()->create([
            'email' => 'duplicate@example.com',
        ]);

        // Try to register with the same email
        $response = $this->postJson('/api/register', [
            'name' => 'Abdus samad1',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert: Check status is 422 (Unprocessable Entity / Validation Error)
        $response->assertStatus(422);

        // Assert: Check that we got a validation error for the email field
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Test registration fails if passwords do not match.
     */
    public function test_registration_fails_if_passwords_do_not_match(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Abdus samad1234',
            'email' => 'Abdus.samad1234@example.com',
            'password' => 'password1234',
            'password_confirmation' => 'different_password',
        ]);

        // Assert: Check status is 422
        $response->assertStatus(422);

        // Assert: Check that we got a validation error for the password field
        $response->assertJsonValidationErrors(['password']);
    }
}

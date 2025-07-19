<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // --- 1. 会員登録機能 ---

    /**
     * @test
     * 入力がされていない場合、バリデーションメッセージが表示される
     */
    public function registration_validation_fails_when_fields_are_empty(): void
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $response->assertStatus(302); // エラーでリダイレクトされることを確認
        $this->assertGuest(); // 認証されていないことを確認
    }

    /**
     * @test
     * メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     */
    public function registration_validation_fails_when_email_is_empty(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $response->assertStatus(302);
        $this->assertGuest();
    }

    /**
     * @test
     * パスワードが入力されていない場合、バリデーションメッセージが表示される
     */
    public function registration_validation_fails_when_password_is_empty(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
        $response->assertStatus(302);
        $this->assertGuest();
    }

    /**
     * @test
     * パスワードが8文字以下の場合、バリデーションメッセージが表示される
     */
    public function registration_validation_fails_when_password_is_too_short(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short', // 7文字以下
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors(['password']);
        $response->assertStatus(302);
        $this->assertGuest();
    }

    /**
     * @test
     * パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される
     */
    public function registration_validation_fails_when_password_confirmation_mismatches(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'mismatch', // 不一致
        ]);

        $response->assertSessionHasErrors(['password']);
        $response->assertStatus(302);
        $this->assertGuest();
    }

    /**
     * @test
     * 全ての項目が入力されている場合、登録情報が作成され、ログイン画面に遷移される
     */
    public function new_user_can_register_and_is_redirected_to_dashboard(): void
    {
        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/mypage/profile');
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
        ]);
        $this->assertAuthenticated();
    }

    // --- 2. ログイン機能 ---

    /**
     * @test
     * メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     */
    public function login_validation_fails_when_email_is_empty(): void
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $response->assertStatus(302);
        $this->assertGuest();
    }

    /**
     * @test
     * パスワードが入力されていない場合、バリデーションメッセージが表示される
     */
    public function login_validation_fails_when_password_is_empty(): void
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
        $response->assertStatus(302);
        $this->assertGuest();
    }

    /**
     * @test
     * 入力情報が間違っている場合、バリデーションメッセージが表示される
     */
    public function login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('correctpassword'),
        ]);

        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'wrongpassword', // 間違ったパスワード
        ]);

        $response->assertSessionHasErrors(['email']);
        $response->assertStatus(302);
        $this->assertGuest();
    }

    /**
     * @test
     * 正しい情報が入力された場合、ログイン画面が提供される
     */
    public function user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'loginuser@example.com',
            'password' => Hash::make('secretpassword'),
        ]);

        $response = $this->post('/login', [
            'email' => 'loginuser@example.com',
            'password' => 'secretpassword',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    // --- 3. ログアウト機能 ---

    /**
     * @test
     * ログアウト
     */
    public function user_can_logout(): void
    {
        $user = User::factory()->create();

        // ログイン状態にする
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertStatus(302);
        $response->assertRedirect('/');
        $this->assertGuest();
    }
}

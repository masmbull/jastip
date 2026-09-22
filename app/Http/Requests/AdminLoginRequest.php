<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'min:3'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username Minimal 3 karakter.',
            'password.required' => 'Password wajib diisi.',
        ];
    }

    /**
     * Authenticate the user.
     */
    public function authenticate(): void
    {
        $credentials = [
            'username' => $this->input('username'),
            'password' => $this->input('password'),
        ];

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'username' => ['Kredensial tidak cocok dengan catatan kami.'],
            ]);
        }
    }

    /**
     * Get the needed authorization credentials from the request.
     */
    public function credentials(): array
    {
        return [
            'username' => $this->input('username'),
            'password' => $this->input('password'),
        ];
    }
}
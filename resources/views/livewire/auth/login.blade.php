<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|digits:10')]
    public string $national_code = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['national_code' => $this->national_code, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'national_code' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'national_code' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->national_code).'|'.request()->ip());
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header title="ورود به حساب کاربری" description="کد ملی و رمز عبور خود را وارد کنید" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- National Code -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">کد ملی</label>
            <input
                wire:model="national_code"
                id="national_code"
                type="text"
                name="national_code"
                required
                autofocus
                autocomplete="national-code"
                placeholder="1234567890"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
            />
        </div>

        <!-- Password -->
        <div class="relative">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">رمز عبور</label>
            <input
                wire:model="password"
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="رمز عبور"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"
            />

            @if (Route::has('password.request'))
                <flux:link class="absolute right-0 top-0 text-sm text-gray-600 dark:text-gray-400" :href="route('password.request')" wire:navigate>
                    رمز عبور خود را فراموش کرده‌اید؟
                </flux:link>
            @endif
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input
                wire:model="remember"
                id="remember"
                type="checkbox"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded dark:bg-gray-800 dark:border-gray-600"
            />
            <label for="remember" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">مرا به خاطر بسپار</label>
        </div>

        <div class="flex items-center justify-end">
            <button
                type="submit"
                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                ورود
            </button>
        </div>
    </form>

    @if (Route::has('register'))
      <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
          حساب کاربری ندارید؟
          <flux:link :href="route('register')" wire:navigate>ثبت‌نام کنید</flux:link>
      </div>
    @endif
</div>
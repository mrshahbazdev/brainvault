<x-layouts.auth title="Reset Password">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Reset your password') }}</h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Enter your new password below.') }}</p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Email Address') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required
                   class="mt-1.5 block w-full px-4 py-2.5 bg-white dark:bg-surface-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
            @error('email')
                <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('New Password') }}</label>
            <input id="password" name="password" type="password" required
                   class="mt-1.5 block w-full px-4 py-2.5 bg-white dark:bg-surface-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                   placeholder="{{ __('Min 8 characters') }}">
            @error('password')
                <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Confirm New Password') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                   class="mt-1.5 block w-full px-4 py-2.5 bg-white dark:bg-surface-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                   placeholder="{{ __('Confirm your password') }}">
        </div>

        <button type="submit"
                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/25 hover:shadow-primary-500/40 transition-all duration-200">
            {{ __('Reset Password') }}
        </button>
    </form>
</x-layouts.auth>

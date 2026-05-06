<x-layouts.auth title="Verify Email">
    <div class="text-center">
        <div class="mx-auto w-16 h-16 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Check your email') }}</h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 max-w-sm mx-auto">
            {{ __("We've sent a verification link to your email address. Please click the link to verify your account.") }}
        </p>
    </div>

    @if (session('status'))
        <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-700 dark:text-green-400 text-center">
            {{ __('A new verification link has been sent!') }}
        </div>
    @endif

    <div class="mt-6 space-y-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/25 hover:shadow-primary-500/40 transition-all duration-200">
                {{ __('Resend Verification Email') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-surface-800 transition-colors">
                {{ __('Sign Out') }}
            </button>
        </form>
    </div>
</x-layouts.auth>

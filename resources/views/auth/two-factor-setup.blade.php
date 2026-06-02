<x-guest-layout>

    @error('code')
    <div class="flex items-center p-4 mb-4 text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
        <div class="ms-3 text-sm font-medium">
            {{ $message }}
        </div>
    </div>
    @enderror

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900">
            {{ __('auth.two_factor_setup_title') }}
        </h2>

        <p class="mt-2 text-sm text-gray-600">
            {{ __('auth.two_factor_setup_description') }}
        </p>
    </div>

    <div class="flex justify-center mb-6">
        {!! QrCode::size(250)->generate($qrCodeUrl) !!}
    </div>

    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            {{ __('auth.two_factor_manual_key') }}
        </label>

        <div class="p-3 bg-gray-100 rounded border text-sm break-all">
            {{ $user->two_factor_secret }}
        </div>

        <p class="mt-2 text-xs text-gray-500">
            {{ __('auth.two_factor_manual_key_hint') }}
        </p>
    </div>

    <form method="POST" action="{{ route('2fa.setup.store') }}">
        @csrf

        <div>
            <x-input-label
                for="code"
                :value="__('auth.authentication_code')"
            />

            <x-text-input
                id="code"
                class="block mt-1 w-full"
                type="text"
                name="code"
                maxlength="6"
                required
                autofocus
                autocomplete="one-time-code"
                placeholder="123456"
            />

            <x-input-error
                :messages="$errors->get('code')"
                class="mt-2"
            />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                {{ __('auth.activate_2fa') }}
            </x-primary-button>
        </div>
    </form>

</x-guest-layout>

<x-guest-layout>

    @error('code')
    <div class="flex items-center p-4 mb-4 text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
        <div class="ms-3 text-sm font-medium">
            {{ $message }}
        </div>
    </div>
    @enderror

    @if(Session::has('error'))
        <div class="flex items-center p-4 mb-4 text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            <div class="ms-3 text-sm font-medium">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900">
            {{ __('auth.Tweestapsverificatie') }}
        </h2>

        <p class="mt-2 text-sm text-gray-600">
            {{ __('auth.Open Microsoft Authenticator en voer de 6-cijferige verificatiecode in.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('2fa.verify.post') }}">
        @csrf

        <div>
            <x-input-label
                for="code"
                :value="__('auth.Authenticatiecode')"
            />

            <x-text-input
                id="code"
                class="block mt-1 w-full"
                type="text"
                name="code"
                maxlength="6"
                autofocus
                autocomplete="one-time-code"
                placeholder="123456"
                required
            />

            <x-input-error
                :messages="$errors->get('code')"
                class="mt-2"
            />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                {{ __('auth.Verifiëren') }}
            </x-primary-button>
        </div>
    </form>

</x-guest-layout>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('SMS Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's sms information.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update.sms') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="sms_token" :value="__('SMS_TOKEN')" />

            <x-text-input id="sms_token" name="sms_token" type="text" class="mt-1 block w-full" :value="old('sms_token', $user->sms_token)" required />

            <x-input-error class="mt-2" :messages="$errors->get('sms_token')" />
        </div>

        <div class="flex items-center gap-4">
            <x-danger-button name="action" value="disable">{{ __('Disable') }}</x-danger-button>
            <x-primary-button name="action" value="save">{{ __('Save') }}</x-primary-button>
        </div>
    </form>
</section>
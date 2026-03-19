<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Parent Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Your profile information is view-only. You can update emergency contact details below.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="pt-4 border-t border-gray-200">
            <h3 class="text-md font-medium text-gray-900 mb-4">Parent Information</h3>
            <div class="bg-gray-50 p-4 rounded-lg space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="first_name" :value="__('First Name')" />
                        <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full bg-gray-100" :value="old('first_name', $user->first_name ?? '')" autocomplete="given-name" readonly />
                    </div>

                    <div>
                        <x-input-label for="last_name" :value="__('Last Name')" />
                        <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full bg-gray-100" :value="old('last_name', $user->last_name ?? '')" autocomplete="family-name" readonly />
                    </div>
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email Address')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-gray-100" :value="old('email', $user->email ?? '')" autocomplete="username" readonly />

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div>
                            <p class="text-sm mt-2 text-gray-800">
                                {{ __('Your email address is unverified.') }}

                                <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>
                            </p>

                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 font-medium text-sm text-green-600">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="phone" :value="__('Contact Number')" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full bg-gray-100" :value="old('phone', $user->phone ?? '')" autocomplete="tel" readonly />
                    </div>

                    <div>
                        <x-input-label for="address" :value="__('Address')" />
                        <x-text-input id="address" name="address" type="text" class="mt-1 block w-full bg-gray-100" :value="old('address', $user->address ?? '')" autocomplete="street-address" readonly />
                    </div>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-md font-medium text-gray-900 mb-4">Emergency Contact Information</h3>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600 mb-3">This information will be used in case of emergencies involving your child(ren).</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="emergency_contact_name" :value="__('Emergency Contact Name')" />
                        <x-text-input id="emergency_contact_name" name="emergency_contact_name" type="text" class="mt-1 block w-full" :value="old('emergency_contact_name', $user->emergency_contact_name ?? '')" />
                        <x-input-error class="mt-2" :messages="$errors->get('emergency_contact_name')" />
                    </div>
                    <div>
                        <x-input-label for="emergency_contact_phone" :value="__('Emergency Contact Phone')" />
                        <x-text-input id="emergency_contact_phone" name="emergency_contact_phone" type="text" class="mt-1 block w-full" :value="old('emergency_contact_phone', $user->emergency_contact_phone ?? '')" />
                        <x-input-error class="mt-2" :messages="$errors->get('emergency_contact_phone')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save Changes') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
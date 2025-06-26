<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Passwort aktualisieren') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Vergewissern Sie sich, dass Ihr Konto ein langes, zufälliges Passwort verwendet, um sicher zu sein, und aktualisieren Sie die Profilinformationen und die E-Mail-Adresse Ihres Kontos.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="current_password" class="form-label fw-bold">{{ __('Current Password') }}</label>
            <input id="current_password" name="current_password" type="password" class="form-control mt-1 block w-full" autocomplete="current-password" />
            @if($errors->updatePassword->get('current_password'))
                <div class="text-danger mt-2">
                    @foreach($errors->updatePassword->get('current_password') as $message)
                        <p>{{ $message }}</p>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <label for="password" class="form-label fw-bold">{{ __('New Password') }}</label>
            <input id="password" name="password" type="password" class="form-control mt-1 block w-full" autocomplete="new-password" />
            @if($errors->updatePassword->get('password'))
                <div class="text-danger mt-2">
                    @foreach($errors->updatePassword->get('password') as $message)
                        <p>{{ $message }}</p>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <label for="password_confirmation" class="form-label fw-bold">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control mt-1 block w-full" autocomplete="new-password" />
            @if($errors->updatePassword->get('password_confirmation'))
                <div class="text-danger mt-2">
                    @foreach($errors->updatePassword->get('password_confirmation') as $message)
                        <p>{{ $message }}</p>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 mt-2">
            <button type="submit" class="btn btn-primary">{{ __('Speichern') }}</button>

            @if (session('status') === 'password-updated')
                <p class="text-sm text-green-600 mt-2" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                    {{ __('Gespeichert.') }}
                </p>
            @endif
        </div>
    </form>
</section>

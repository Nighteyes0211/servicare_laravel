<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Konto löschen') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Sobald Ihr Konto gelöscht ist, werden alle Ressourcen und Daten dauerhaft gelöscht. Bevor Sie Ihr Konto löschen, laden Sie bitte alle Daten oder Informationen herunter, die Sie aufbewahren möchten.') }}
        </p>
    </header>

    <!-- Button to trigger the modal -->
    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">{{ __('Account löschen') }}</button>

    <!-- Modal Component -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="{{ route('profile.destroy') }}" class="modal-content p-6">
                @csrf
                @method('delete')

                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAccountModalLabel">{{ __('Are you sure you want to delete your account?') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p>{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</p>

                    <div class="mt-6">
                        <label for="password" class="form-label fw-bold">{{ __('Password') }}</label>
                        <input id="password" name="password" type="password" class="form-control" placeholder="{{ __('Password') }}" required />
                        @if($errors->userDeletion->get('password'))
                            <div class="text-danger mt-2">
                                @foreach($errors->userDeletion->get('password') as $message)
                                    <p>{{ $message }}</p>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Account löschen') }}</button>
                </div>
            </form>
        </div>
    </div>
</section>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

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

        <div>
            <x-input-label for="profile_photo" :value="__('Foto do Perfil')" />

            <!-- Exiba a imagem atual do perfil -->
            @if(Auth::user()->profile_photo_path)
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('build/assets/' . Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}" class="h-8 w-8 rounded-full" style="width: 40px; height: 40px; object-fit: cover; max-width: 100%;">

                    <!-- Botões para trocar e remover a foto -->
                    <div style="margin: 0 15px;border: 1px solid #ccc;border-radius: 5px;padding: 6px;cursor: pointer;background-color: #ccc;">
                        <label for="change_profile_photo" class="cursor-pointer text-blue-500 hover:text-blue-700">{{ __('Trocar Foto') }}</label>
                        <input type="file" name="profile_photo" id="change_profile_photo" class="hidden" accept="image/*">
                    </div>

                    <div style="border: 1px solid #ccc;border-radius: 5px;padding: 6px;cursor: pointer;background-color: #ccc;">
                        <button type="button" id="remove_profile_photo" class="text-red-500 hover:text-red-700">{{ __('Remover Foto') }}</button>
                    </div>
                </div>
            @else
                <input type="file" name="profile_photo" id="profile_photo" class="mt-1 block w-full" accept="image/*">
            @endif

            <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
        </div>

        <div>
            <x-input-label for="cep" :value="__('CEP')" />
            <x-text-input id="cep" name="cep" type="text" class="mt-1 block w-full" :value="old('cep', $user->cep)" required autocomplete="cep" />
            <x-input-error class="mt-2" :messages="$errors->get('cep')" />
        </div>

        <div>
            <x-input-label for="logradouro" :value="__('Logradouro')" />
            <x-text-input id="logradouro" name="logradouro" type="text" class="mt-1 block w-full" :value="old('logradouro', $user->logradouro)" required autocomplete="logradouro" />
            <x-input-error class="mt-2" :messages="$errors->get('logradouro')" />
        </div>
        <div>
            <x-input-label for="bairro" :value="__('Bairro')" />
            <x-text-input id="bairro" name="bairro" type="text" class="mt-1 block w-full" :value="old('bairro', $user->bairro)" required autocomplete="bairro" />
            <x-input-error class="mt-2" :messages="$errors->get('bairro')" />
        </div>
        <div>
            <x-input-label for="cidade" :value="__('Cidade')" />
            <x-text-input id="cidade" name="cidade" type="text" class="mt-1 block w-full" :value="old('cidade', $user->cidade)" required autocomplete="cidade" />
            <x-input-error class="mt-2" :messages="$errors->get('cidade')" />
        </div>
        <div>
            <x-input-label for="estado" :value="__('Estado')" />
            <x-text-input id="estado" name="estado" type="text" class="mt-1 block w-full" :value="old('estado', $user->estado)" required autocomplete="estado" />
            <x-input-error class="mt-2" :messages="$errors->get('estado')" />
        </div>

        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>


        <script>
            const cepInput = document.getElementById('cep');
            const logradouroInput = document.getElementById('logradouro');
            const bairroInput = document.getElementById('bairro');
            const cidadeInput = document.getElementById('cidade');
            const estadoInput = document.getElementById('estado');

            cepInput.addEventListener('blur', () => {
                const cep = cepInput.value.replace(/\D/g, '');
                console.log('Linha 79 ' + cep);
                if (cep.length === 8) {
                    axios.get(`https://viacep.com.br/ws/${cep}/json/`)
                        .then((response) => {
                            const data = response.data;
                            console.log(response);
                            if (!data.erro) {
                                logradouroInput.value = data.logradouro;
                                bairroInput.value = data.bairro;
                                cidadeInput.value = data.localidade;
                                estadoInput.value = data.uf;
                            } else {
                                console.error('CEP não encontrado' + data);
                            }
                        })
                        .catch((error) => {
                            console.error('Erro ao consultar o CEP:', error);
                        });
                }
            });
        </script>

        <script>
            const changeProfilePhotoInput = document.getElementById('change_profile_photo');
            const removeProfilePhotoButton = document.getElementById('remove_profile_photo');

            // Lidar com a troca de foto do perfil
            changeProfilePhotoInput.addEventListener('change', () => {
                if (changeProfilePhotoInput.files.length > 0) {
                    const formData = new FormData();
                    formData.append('profile_photo', changeProfilePhotoInput.files[0]);

                    axios.post('{{ route("profile.photo.update") }}', formData)
                        .then((response) => {
                            if (response.status === 200) {
                                window.location.reload();
                            }
                        })
                        .catch((error) => {
                            console.error('Erro ao atualizar a foto do perfil:', error);
                        });
                }
            });

            // Lidar com a remoção da foto do perfil
            removeProfilePhotoButton.addEventListener('click', () => {
                if (confirm('{{ __("Tem certeza de que deseja remover a foto do perfil?") }}')) {
                    // Envie uma solicitação AJAX para remover a foto do perfil
                    axios.delete('{{ route("profile.photo.remove") }}')
                        .then((response) => {
                            if (response.status === 200) {
                                window.location.reload();
                            }
                        })
                        .catch((error) => {
                            console.error('Erro ao remover a foto do perfil:', error);
                        });
                }
            });
        </script>


        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

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

document.addEventListener('DOMContentLoaded', function () {
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

});

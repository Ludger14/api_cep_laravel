<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if ($request->hasFile('profile_photo')) {
            // Salve a foto do perfil e obtenha o caminho dela
            $profilePhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');

            // Defina o caminho da foto do perfil no modelo do usuário
            $user->profile_photo_path = $profilePhotoPath;
        }

        if ($request->has('cep')) {
            $cep = $request->input('cep');
            $user->cep = $cep;

            $logradouro = $request->input('logradouro');
            $user->logradouro = $logradouro;

            $bairro = $request->input('bairro');
            $user->bairro = $bairro;

            $cidade = $request->input('cidade');
            $user->cidade = $cidade;

            $estado = $request->input('estado');
            $user->estado = $estado;
        }

        // Atualize os outros campos
        $user->fill($data);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update the user's profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $user = $request->user();

        if ($request->hasFile('profile_photo')) {
            // Remove a foto de perfil atual, se existir
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // Salve a nova foto de perfil
            $profilePhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $profilePhotoPath;
            $user->save();

            return redirect()->route('profile.edit')->with('status', 'profile-photo-updated');
        }

        return back()->withErrors(['profile_photo' => 'Nenhuma imagem selecionada.'])->withInput();
    }

    /**
     * Remove a user's profile photo.
     */
    public function removePhoto(Request $request): JsonResponse
    {
        $user = $request->user();

        // Verifique se o usuário tem uma foto de perfil
        if ($user->profile_photo_path) {
            // Remova a foto do sistema de arquivos
            Storage::disk('public')->delete($user->profile_photo_path);

            // Limpe o caminho da foto de perfil no modelo do usuário
            $user->profile_photo_path = null;
            $user->save();

            return response()->json(['message' => 'Foto do perfil removida com sucesso'], 200);
        }

        return response()->json(['message' => 'Nenhuma foto de perfil encontrada'], 404);
    }

}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Country;
use App\Models\City;
use App\Models\State;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = Auth::user();
        $countries = Country::all();
        $states = State::where('country_id', $user->idcountry)->get();
        $countries = Country::all(); // Ambil semua negara
        $states = State::where('country_id', $user->idcountry)->get(); // Ambil states berdasarkan negara pengguna
        $cities = City::where('state_id', $user->idstate)->get();

        return view('profile.edit', compact('user', 'countries', 'states', 'cities'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

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

    public function updatePassword(Request $request)
    {
        $user = Auth::user(); // Ambil user yang sedang login

        // Validasi input
        $request->validate([
            'current_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Cek apakah password lama yang dimasukkan benar
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah.']);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Password berhasil diperbarui.');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user(); // Ambil user yang sedang login

        // Validasi data
        $request->validate([
            'name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'gender' => 'required|string',
            'address' => 'nullable|string|max:255',
            'number_phone' => 'nullable|string|max:15',
            'address_2' => 'nullable|string|max:255',
            'portal_code' => 'nullable|string|max:10',
        ]);

        // Update data pengguna
        $user->name = $request->name;
        $user->full_name = $request->full_name;
        $user->idcountry = $request->idcountry;
        $user->idstate = $request->idstate;
        $user->idcities = $request->idcities;
        $user->email = $request->email;
        $user->gender = $request->gender;
        $user->address = $request->address;
        $user->number_phone = $request->number_phone;
        $user->address_2 = $request->address_2;
        $user->portal_code = $request->portal_code;

        if ($request->hasFile('photo')) {
            // Jika foto dikirim sebagai file (bukan base64)
            $photo = $request->file('photo');
            $fileName = 'photo_' . uniqid() . '.' . $photo->getClientOriginalExtension(); // Nama file unik
           
            // Menyimpan foto ke disk 'public' (dalam storage/app/public)
            $photo->storeAs('uploads/photos', $fileName, 'public');
        
            $user->photo = 'uploads/photos/' . $fileName;
        }
        
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $fileName = 'upload_' . uniqid() . '.' . $file->getClientOriginalExtension(); // Nama file unik
        
            // Simpan file langsung di storage/app/public/uploads/photos/
            $file->storeAs('uploads/photos', $fileName, 'public');
        
            // Simpan path yang benar di database
            $user->upload = 'uploads/photos/' . $fileName;
        }

        // Simpan perubahan data
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }

}

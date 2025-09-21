<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\Country;
use App\Models\City;
use App\Models\State;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query  = $request->input('query');
        $status = $request->input('status'); // registered / unregistered
        $role   = $request->input('role');   // tambahan untuk filter role
    
        $users = User::with('member')
            ->when($query, function ($q) use ($query) {
                return $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('full_name', 'LIKE', "%{$query}%")
                        ->orWhere('email', 'LIKE', "%{$query}%");
                });
            })
            ->when($status === 'registered', function ($q) {
                return $q->whereHas('member');
            })
            ->when($status === 'unregistered', function ($q) {
                return $q->whereDoesntHave('member');
            })
            ->when($role, function ($q) use ($role) {
                return $q->role($role); // pakai spatie role scope
            })
            ->orderBy('name', 'asc')
            ->get();
    
        return view('role-permission.user.index', compact('users', 'query', 'status', 'role'));
    }

    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        return view('role-permission.user.create', ['roles' => $roles]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:20',
            'roles' => 'required'
        ]);

        $user = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => Hash::make($request->password),
                    ]);

        $user->syncRoles($request->roles);

        return redirect('/users')->with('status','User created successfully with roles');
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name','name')->all();
        $userRoles = $user->roles->pluck('name','name')->all();
        $countries = Country::all();
        $states = State::all();
        $cities = City::all();
        return view('role-permission.user.edit', [
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $userRoles,
            'countries' => $countries,
            'states' => $states,
            'cities' => $cities,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|max:20',
            'roles' => 'required'
        ]);

        $data = [
            'name' => $request->name,
            'full_name' => $request->full_name,
            'gender' => $request->gender,
            'idcountry' => $request->idcountry,
            'idstate' => $request->idstate,
            'idcities' => $request->idcities,
            'email' => $request->email,
            'address' => $request->address,
            'address_2' => $request->address_2,
            'portal_code' => $request->portal_code
        ];

        if(!empty($request->password)){
            $data += [
                'password' => Hash::make($request->password),
            ];
        }

        $user->update($data);
        $user->syncRoles($request->roles);

        return redirect('/users')->with('status','User Updated Successfully with roles');
    }

    public function destroy($userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();

        return redirect('/users')->with('status','User Delete Successfully');
    }
}
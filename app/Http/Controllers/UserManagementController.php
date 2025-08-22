<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        // set password default jika tidak diisi
        $password = $data['password'] ?? Str::random(10);
        $data['password'] = bcrypt($password);
        $user = User::create($data);
        $user->assignRole('Karyawan');
        return redirect()->route('users.index')->with('status', 'Karyawan dibuat');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return redirect()->route('users.index')->with('status', 'Karyawan diperbarui');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('status','Karyawan dihapus');
    }
}

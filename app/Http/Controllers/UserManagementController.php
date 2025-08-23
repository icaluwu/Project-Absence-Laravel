<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

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
        // Normalisasi nilai optional ke default DB
        $data['gaji_pokok'] = isset($data['gaji_pokok']) && $data['gaji_pokok'] !== null ? (float)$data['gaji_pokok'] : 0.0;
        $data['status_karyawan'] = $data['status_karyawan'] ?? 'aktif';

        // set password default jika tidak diisi
        $password = $data['password'] ?? Str::random(10);
        $data['password'] = bcrypt($password);

        $user = User::create($data);

        // Tentukan role akhir
        $allowed = ['Admin','HR','Karyawan'];
        $requestedRole = $request->string('role')->toString();
        $finalRole = 'Karyawan';
        if ($request->user()->hasRole('Admin') && in_array($requestedRole, $allowed, true)) {
            $finalRole = $requestedRole;
        }

        // Pastikan roles tersedia
        foreach ($allowed as $r) { Role::firstOrCreate(['name' => $r]); }
        $user->assignRole($finalRole);

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
        // Coerce gaji_pokok null ke 0 agar tidak melanggar NOT NULL decimal
        if (array_key_exists('gaji_pokok', $data)) {
            $data['gaji_pokok'] = $data['gaji_pokok'] === null ? 0.0 : (float)$data['gaji_pokok'];
        }
        $data['status_karyawan'] = $data['status_karyawan'] ?? $user->status_karyawan ?? 'aktif';

        $user->update($data);
        return redirect()->route('users.index')->with('status', 'Karyawan diperbarui');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('status','Karyawan dihapus');
    }
}

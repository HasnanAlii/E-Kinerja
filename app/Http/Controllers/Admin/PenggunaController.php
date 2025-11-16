<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class PenggunaController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('role:admin');
    // }

    public function index()
    {
        $data = User::with('roles')->paginate(20);

        return view('admin.pengguna.index', compact('data'));
    }

    public function edit($id)
    {
        $data = User::findOrFail($id);
        $roles = Role::all();

        return view('admin.pengguna.edit', compact('data', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->syncRoles([$request->role]);

        return back()->with('success', 'Role pengguna berhasil diperbarui.');
    }
}

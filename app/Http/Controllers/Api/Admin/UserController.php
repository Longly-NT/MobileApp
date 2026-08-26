<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['staff', 'chef'])->latest()->get();

        return response()->json($users->map(fn ($u) => $this->format($u)));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->role === 'admin', 403, 'Only Admin can add users.');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:staff,chef'],
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return response()->json($this->format($user), 201);
    }

    public function update(Request $request, User $user)
    {
        abort_unless($request->user()->role === 'admin', 403, 'Only Admin can edit users.');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:staff,chef'],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json($this->format($user));
    }

    public function destroy(User $user)
    {
        abort_unless(auth()->user()->role === 'admin', 403, 'Only Admin can delete users.');

        $user->delete();

        return response()->json(['message' => 'Deleted']);
    }

    private function format(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];
    }
}
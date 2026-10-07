<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSuperadminRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SuperadminUserController extends Controller
{
    public function index(): View
    {
        return view('superadmin.superadmins.index', [
            'superadmins' => User::query()
                ->where('role', 'superadmin')
                ->latest('id')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('superadmin.superadmins.create');
    }

    public function store(StoreSuperadminRequest $request): RedirectResponse
    {
        User::query()->create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role' => 'superadmin',
            'catalog_id' => null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('superadmin.superadmins.index')
            ->with('success', 'Akun Superadmin berhasil dibuat.');
    }
}

<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCatalogAdminRequest;
use App\Http\Requests\UpdateCatalogAdminRequest;
use App\Models\Catalog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogAdminController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('manageAdmins', Catalog::class);

        $query = User::query()
            ->where('role', 'catalog_admin')
            ->with('catalog.category')
            ->latest('id');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->string('status')->toString() === 'active');
        }

        return view('superadmin.admins.index', [
            'admins' => $query->paginate(15)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('manageAdmins', Catalog::class);

        $catalogs = Catalog::query()
            ->whereDoesntHave('admin')
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('superadmin.admins.create', compact('catalogs'));
    }

    public function store(StoreCatalogAdminRequest $request): RedirectResponse
    {
        User::query()->create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role' => 'catalog_admin',
            'catalog_id' => $request->integer('catalog_id'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('superadmin.admins.index')
            ->with('success', 'Akun Catalog Admin berhasil dibuat.');
    }

    public function edit(User $admin): View
    {
        $this->authorize('manageAdmins', Catalog::class);
        abort_unless($admin->isCatalogAdmin(), 404);

        $catalogs = Catalog::query()
            ->where(function ($query) use ($admin): void {
                $query->whereDoesntHave('admin')
                    ->orWhere('id', $admin->catalog_id);
            })
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('superadmin.admins.edit', compact('admin', 'catalogs'));
    }

    public function update(UpdateCatalogAdminRequest $request, User $admin): RedirectResponse
    {
        abort_unless($admin->isCatalogAdmin(), 404);

        $data = [
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'catalog_id' => $request->integer('catalog_id'),
            'is_active' => $request->boolean('is_active', $admin->is_active),
        ];

        if (filled($request->validated('password'))) {
            $data['password'] = $request->validated('password');
        }

        $admin->update($data);

        return redirect()
            ->route('superadmin.admins.index')
            ->with('success', 'Akun Catalog Admin berhasil diperbarui.');
    }

    public function toggleActive(User $admin): RedirectResponse
    {
        $this->authorize('manageAdmins', Catalog::class);
        abort_unless($admin->isCatalogAdmin(), 404);

        $admin->update(['is_active' => ! $admin->is_active]);

        $status = $admin->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Akun {$admin->name} berhasil {$status}.");
    }

    public function destroy(User $admin): RedirectResponse
    {
        $this->authorize('manageAdmins', Catalog::class);
        abort_unless($admin->isCatalogAdmin(), 404);

        $admin->delete();

        return redirect()
            ->route('superadmin.admins.index')
            ->with('success', 'Akun Catalog Admin berhasil dihapus.');
    }
}

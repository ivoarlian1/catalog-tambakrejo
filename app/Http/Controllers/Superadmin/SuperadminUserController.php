<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSuperadminRequest;
use App\Http\Requests\UpdateSuperadminRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
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

    public function edit(User $superadmin): View
    {
        abort_unless($superadmin->isSuperadmin(), 404);

        return view('superadmin.superadmins.edit', compact('superadmin'));
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

    public function update(UpdateSuperadminRequest $request, User $superadmin): RedirectResponse
    {
        abort_unless($superadmin->isSuperadmin(), 404);

        $data = [
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
        ];

        if (filled($request->validated('password'))) {
            $data['password'] = $request->validated('password');
        }

        $result = DB::transaction(function () use ($request, $superadmin, $data): ?string {
            $superadmins = User::query()
                ->where('role', 'superadmin')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();
            $lockedSuperadmin = $superadmins->firstWhere('id', $superadmin->id);

            abort_if($lockedSuperadmin === null, 404);

            $isActive = $request->boolean('is_active', $lockedSuperadmin->is_active);
            if (! $isActive && $lockedSuperadmin->is(auth()->user())) {
                return 'Anda tidak dapat menonaktifkan akun sendiri.';
            }

            if (! $isActive && $lockedSuperadmin->is_active) {
                if ($superadmins->where('is_active', true)->where('id', '!=', $lockedSuperadmin->id)->isEmpty()) {
                    return 'Akun Superadmin aktif terakhir tidak dapat dinonaktifkan.';
                }
            }

            $lockedSuperadmin->update([...$data, 'is_active' => $isActive]);

            return null;
        });

        if ($result !== null) {
            return back()->withInput()->with('error', $result);
        }

        return redirect()
            ->route('superadmin.superadmins.index')
            ->with('success', 'Akun Superadmin berhasil diperbarui.');
    }

    public function toggleActive(User $superadmin): RedirectResponse
    {
        abort_unless($superadmin->isSuperadmin(), 404);

        if ($superadmin->is(auth()->user())) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $result = DB::transaction(function () use ($superadmin): ?string {
            $superadmins = User::query()
                ->where('role', 'superadmin')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();
            $lockedSuperadmin = $superadmins->firstWhere('id', $superadmin->id);

            abort_if($lockedSuperadmin === null, 404);

            if ($lockedSuperadmin->is_active && $superadmins->where('is_active', true)->where('id', '!=', $lockedSuperadmin->id)->isEmpty()) {
                return 'Akun Superadmin aktif terakhir tidak dapat dinonaktifkan.';
            }

            $lockedSuperadmin->update(['is_active' => ! $lockedSuperadmin->is_active]);

            return null;
        });

        if ($result !== null) {
            return back()->with('error', $result);
        }

        $superadmin->refresh();
        $status = $superadmin->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Akun {$superadmin->name} berhasil {$status}.");
    }

    public function destroy(User $superadmin): RedirectResponse
    {
        abort_unless($superadmin->isSuperadmin(), 404);

        if ($superadmin->is(auth()->user())) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $result = DB::transaction(function () use ($superadmin): ?string {
            $superadmins = User::query()
                ->where('role', 'superadmin')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();
            $lockedSuperadmin = $superadmins->firstWhere('id', $superadmin->id);

            abort_if($lockedSuperadmin === null, 404);

            if ($superadmins->count() <= 1) {
                return 'Akun Superadmin terakhir tidak dapat dihapus.';
            }

            if ($lockedSuperadmin->is_active && $superadmins->where('is_active', true)->where('id', '!=', $lockedSuperadmin->id)->isEmpty()) {
                return 'Akun Superadmin aktif terakhir tidak dapat dihapus.';
            }

            $lockedSuperadmin->delete();

            return null;
        });

        if ($result !== null) {
            return back()->with('error', $result);
        }

        return redirect()
            ->route('superadmin.superadmins.index')
            ->with('success', 'Akun Superadmin berhasil dihapus.');
    }
}

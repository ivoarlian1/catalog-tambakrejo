@extends('layouts.admin')

@section('title', 'Akun Superadmin')

@section('content')
<div class="flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Akun Superadmin</h1>
        <p class="mt-1 text-sm text-sea">{{ $superadmins->total() }} akun</p>
    </div>
    <a href="{{ route('superadmin.superadmins.create') }}" class="inline-flex min-h-11 items-center rounded-md bg-sea px-4 text-white">Tambah Superadmin</a>
</div>

<div class="mt-5 overflow-x-auto rounded-md border border-sky bg-white">
    <table class="w-full min-w-[720px] text-left text-sm">
        <thead class="bg-sky">
            <tr><th class="p-3">Nama</th><th class="p-3">Email</th><th class="p-3">Status</th><th class="p-3">Tanggal dibuat</th><th class="p-3">Aksi</th></tr>
        </thead>
        <tbody>
            @forelse ($superadmins as $superadmin)
                <tr class="border-t border-sky">
                    <td class="p-3">{{ $superadmin->name }}</td>
                    <td class="p-3">{{ $superadmin->email }}</td>
                    <td class="p-3">{{ $superadmin->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                    <td class="p-3">{{ $superadmin->created_at->format('d/m/Y') }}</td>
                    <td class="p-3">
                        <div class="flex gap-3">
                            <a href="{{ route('superadmin.superadmins.edit', $superadmin) }}" class="text-sea underline">Edit</a>
                            @if (! $superadmin->is(auth()->user()))
                                <form method="POST" action="{{ route('superadmin.superadmins.toggle-active', $superadmin) }}">
                                    @csrf
                                    <button class="text-sea underline">{{ $superadmin->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                </form>
                                <form method="POST" action="{{ route('superadmin.superadmins.destroy', $superadmin) }}" onsubmit="return confirm('Hapus akun Superadmin ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-800 underline">Hapus</button>
                                </form>
                            @else
                                <span class="text-sea">Akun Anda</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-6 text-center text-sea">Belum ada akun Superadmin.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $superadmins->links() }}</div>
@endsection

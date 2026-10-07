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
    <table class="w-full min-w-[480px] text-left text-sm">
        <thead class="bg-sky">
            <tr><th class="p-3">Nama</th><th class="p-3">Email</th><th class="p-3">Tanggal dibuat</th></tr>
        </thead>
        <tbody>
            @forelse ($superadmins as $superadmin)
                <tr class="border-t border-sky">
                    <td class="p-3">{{ $superadmin->name }}</td>
                    <td class="p-3">{{ $superadmin->email }}</td>
                    <td class="p-3">{{ $superadmin->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="p-6 text-center text-sea">Belum ada akun Superadmin.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $superadmins->links() }}</div>
@endsection

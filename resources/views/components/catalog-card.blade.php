<article class="group overflow-hidden rounded-2xl border border-white/80 bg-white/75 shadow-lg shadow-navy/5 backdrop-blur-xl transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-sea/10">
    <img src="{{ $catalog->photo_url }}" alt="{{ $catalog->name }}" class="aspect-[16/10] w-full object-cover transition duration-700 group-hover:scale-[1.03]" width="800" height="500" loading="lazy">
    <div class="flex flex-col gap-3 p-4">
        <div aria-label="Nomor urut katalog {{ $catalog->sort_order }}" class="inline-flex w-fit items-center gap-2 rounded-lg bg-navy px-3 py-1.5 text-white shadow-sm">
            <span class="text-lg font-bold leading-none">{{ $catalog->sort_order }}</span>
        </div>
        <div class="flex flex-wrap gap-2 text-xs">
            <span class="rounded-full bg-sky px-3 py-1 text-sea">{{ $catalog->type_label }}</span>
            @if ($catalog->sub_type)
                <span class="rounded-full bg-sand px-3 py-1">{{ $catalog->sub_type }}</span>
            @endif
        </div>
        <h3 class="text-lg font-semibold">{{ $catalog->name }}</h3>
        <p class="text-sm text-sea">{{ \Illuminate\Support\Str::limit($catalog->address, 80) }}</p>
        <a href="{{ $catalog->detail_url }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-sea px-4 py-2 font-semibold text-white transition hover:-translate-y-0.5 hover:bg-navy">Lihat detail <span aria-hidden="true">→</span></a>
    </div>
</article>

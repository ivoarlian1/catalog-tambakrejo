<article class="overflow-hidden rounded-md border border-sky bg-white">
    <img src="{{ $catalog->photo_url }}" alt="{{ $catalog->name }}" class="aspect-[16/10] w-full object-cover" width="800" height="500" loading="lazy">
    <div class="flex flex-col gap-3 p-4">
        <div class="flex flex-wrap gap-2 text-xs">
            <span class="rounded-full bg-sky px-3 py-1">{{ $catalog->type_label }}</span>
            @if ($catalog->sub_type)
                <span class="rounded-full bg-sand px-3 py-1">{{ $catalog->sub_type }}</span>
            @endif
        </div>
        <h3 class="text-lg font-semibold">{{ $catalog->name }}</h3>
        <p class="text-sm text-sea">{{ \Illuminate\Support\Str::limit($catalog->address, 80) }}</p>
        <a href="{{ $catalog->detail_url }}" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-sea px-4 py-2 text-white">Lihat Detail</a>
    </div>
</article>

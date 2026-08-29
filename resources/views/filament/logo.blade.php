<div class="flex items-center gap-x-3">
    @if ($logo)
        <img src="{{ $logo }}" alt="{{ $name }}" class="h-8 max-w-[160px] object-contain rounded" style="image-rendering: -webkit-optimize-contrast;" />
    @endif
    <span class="text-lg font-bold tracking-tight text-gray-950 dark:text-white">
        {{ $name }}
    </span>
</div>

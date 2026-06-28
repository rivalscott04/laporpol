<div
    class="-mx-6 -mb-6 flex min-h-0 flex-col overflow-hidden"
    style="height: calc(100dvh - 7.5rem);"
>
    <div class="flex shrink-0 gap-3 border-b border-gray-200 px-6 py-3 dark:border-white/10">
        <a
            href="{{ $openUrl }}"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex flex-1 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-950 shadow-sm transition hover:bg-gray-50 dark:border-white/20 dark:bg-gray-900 dark:text-white dark:hover:bg-white/5"
        >
            Buka di Tab Baru
        </a>

        <a
            href="{{ $downloadUrl }}"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex flex-1 items-center justify-center rounded-lg border border-primary-600 bg-white px-4 py-2.5 text-sm font-medium text-primary-600 shadow-sm transition hover:bg-primary-50 dark:border-primary-500 dark:bg-gray-900 dark:text-primary-400 dark:hover:bg-primary-500/10"
        >
            Unduh PDF
        </a>
    </div>

    <div wire:ignore class="min-h-0 flex-1 overflow-hidden bg-gray-800">
        <iframe
            src="{{ $viewerUrl }}"
            title="Pratinjau PDF"
            class="h-full w-full border-0"
            allow="fullscreen"
        ></iframe>
    </div>
</div>

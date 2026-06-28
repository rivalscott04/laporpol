<style>
    .fi-modal-pdf-preview.fi-modal-window {
        display: flex !important;
        flex-direction: column !important;
        height: 100dvh !important;
        max-height: 100dvh !important;
        overflow: hidden !important;
    }

    .fi-modal-pdf-preview .fi-modal-header {
        flex-shrink: 0;
        border-bottom: 1px solid color-mix(in srgb, currentColor 10%, transparent);
    }

    .fi-modal-pdf-preview .fi-modal-content {
        display: flex !important;
        flex: 1 1 0% !important;
        flex-direction: column !important;
        gap: 0 !important;
        min-height: 0 !important;
        overflow: hidden !important;
        padding: 0 !important;
    }

    .fi-pdf-preview-root {
        display: flex;
        flex: 1 1 0%;
        flex-direction: column;
        min-height: 0;
        overflow: hidden;
    }

    .fi-pdf-preview-actions {
        flex-shrink: 0;
    }

    .fi-pdf-preview-viewer {
        flex: 1 1 0%;
        min-height: 0;
        overflow: hidden;
    }

    .fi-pdf-preview-viewer iframe {
        display: block;
        width: 100%;
        height: 100%;
        border: 0;
    }
</style>

<div class="fi-pdf-preview-root">
    <div class="fi-pdf-preview-actions flex gap-3 border-b border-gray-200 px-6 py-3 dark:border-white/10">
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

    <div wire:ignore class="fi-pdf-preview-viewer">
        <iframe
            src="{{ $viewerUrl }}"
            title="Pratinjau PDF"
            allow="fullscreen"
        ></iframe>
    </div>
</div>

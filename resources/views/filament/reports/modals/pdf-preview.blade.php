<style>
    .fi-modal-pdf-preview-overlay {
        position: fixed !important;
        inset: 0 !important;
        width: 100vw !important;
        min-height: 100vh !important;
        min-height: 100dvh !important;
        min-height: 100svh !important;
        -webkit-backdrop-filter: blur(12px);
        backdrop-filter: blur(12px);
    }

    .fi-modal-pdf-preview.fi-modal-window {
        display: flex !important;
        flex-direction: column !important;
        width: 100% !important;
        max-width: min(96vw, 80rem) !important;
        height: min(92dvh, 56rem) !important;
        max-height: 92dvh !important;
        overflow: hidden !important;
    }

    .fi-modal-pdf-preview .fi-modal-header {
        flex-shrink: 0;
        border-bottom: 1px solid color-mix(in srgb, currentColor 10%, transparent);
    }

    .fi-modal-pdf-preview .fi-modal-heading {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
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
        background-color: #525252;
    }

    .fi-pdf-preview-viewer iframe {
        display: block;
        width: 100%;
        height: 100%;
        border: 0;
    }

    @media (max-width: 639px) {
        .fi-modal-pdf-preview.fi-modal-window {
            max-width: calc(100vw - 1rem) !important;
            height: calc(100dvh - 1.5rem) !important;
            max-height: calc(100dvh - 1.5rem) !important;
        }
    }

    @media (min-width: 640px) and (max-width: 1023px) {
        .fi-modal-pdf-preview.fi-modal-window {
            max-width: min(94vw, 48rem) !important;
            height: min(90dvh, 52rem) !important;
            max-height: 90dvh !important;
        }
    }
</style>

<div class="fi-pdf-preview-root">
    <div class="fi-pdf-preview-actions grid grid-cols-1 gap-2 px-4 py-3 sm:grid-cols-2 sm:gap-3 sm:px-6 sm:py-4">
        <x-filament::button
            tag="a"
            :href="$openUrl"
            target="_blank"
            rel="noopener noreferrer"
            color="gray"
            outlined
            class="w-full justify-center"
        >
            Buka di Tab Baru
        </x-filament::button>

        <x-filament::button
            tag="a"
            :href="$downloadUrl"
            target="_blank"
            rel="noopener noreferrer"
            color="success"
            outlined
            class="w-full justify-center"
        >
            Unduh PDF
        </x-filament::button>
    </div>

    <div wire:ignore class="fi-pdf-preview-viewer">
        <iframe
            src="{{ $viewerUrl }}"
            title="Pratinjau PDF"
            class="h-full w-full"
            allow="fullscreen"
        ></iframe>
    </div>
</div>

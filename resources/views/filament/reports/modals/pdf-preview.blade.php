<div
    wire:ignore
    x-data="{
        url: @js($url),
        loading: true,
        error: null,
        async init() {
            const container = this.$refs.container;

            const waitForWidth = () => new Promise((resolve) => {
                if (container.clientWidth >= 100) {
                    resolve(container.clientWidth);

                    return;
                }

                const observer = new ResizeObserver(() => {
                    if (container.clientWidth >= 100) {
                        observer.disconnect();
                        resolve(container.clientWidth);
                    }
                });

                observer.observe(container);
            });

            try {
                const containerWidth = await waitForWidth();
                const pdfjsLib = await import('https://cdn.jsdelivr.net/npm/pdfjs-dist@4.10.38/build/pdf.min.mjs');

                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@4.10.38/build/pdf.worker.min.mjs';

                const pdf = await pdfjsLib.getDocument(this.url).promise;

                for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
                    const page = await pdf.getPage(pageNumber);
                    const baseViewport = page.getViewport({ scale: 1 });
                    const scale = containerWidth / baseViewport.width;
                    const viewport = page.getViewport({ scale });
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');

                    canvas.width = viewport.width;
                    canvas.height = viewport.height;
                    canvas.className = 'block w-full bg-white shadow-sm';

                    await page.render({ canvasContext: context, viewport }).promise;
                    container.appendChild(canvas);

                    if (pageNumber < pdf.numPages) {
                        const spacer = document.createElement('div');
                        spacer.className = 'h-3';
                        container.appendChild(spacer);
                    }
                }
            } catch (exception) {
                console.error(exception);
                this.error = 'Gagal memuat pratinjau PDF.';

                const iframe = document.createElement('iframe');
                iframe.src = `${this.url}#view=FitH&zoom=page-width`;
                iframe.title = 'Pratinjau PDF';
                iframe.className = 'min-h-0 w-full flex-1 border-0';
                container.replaceChildren(iframe);
                this.error = null;
            } finally {
                this.loading = false;
            }
        },
    }"
    x-init="init()"
    class="-mx-6 -mb-6 flex min-h-0 flex-1 flex-col overflow-hidden"
>
    <div
        x-show="loading"
        x-cloak
        class="flex flex-1 items-center justify-center text-sm text-gray-500 dark:text-gray-400"
    >
        Memuat PDF...
    </div>

    <div
        x-show="error"
        x-cloak
        x-text="error"
        class="flex flex-1 items-center justify-center text-sm text-danger-600 dark:text-danger-400"
    ></div>

    <div
        x-ref="container"
        class="min-h-0 flex-1 overflow-y-auto bg-gray-100 dark:bg-gray-950"
    ></div>
</div>

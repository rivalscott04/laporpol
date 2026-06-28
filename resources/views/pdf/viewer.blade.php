<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>Pratinjau PDF</title>
        <link rel="stylesheet" href="{{ asset('pdfjs/pdf_viewer.css') }}">
        <style>
            html,
            body {
                margin: 0;
                height: 100%;
                background: #525252;
            }

            #viewerContainer {
                position: absolute;
                inset: 0;
                overflow: auto;
            }

            #viewer {
                margin: 0 auto;
            }
        </style>
    </head>
    <body>
        <div id="viewerContainer">
            <div id="viewer" class="pdfViewer"></div>
        </div>

        <script src="{{ asset('pdfjs/build/pdf.min.js') }}"></script>
        <script src="{{ asset('pdfjs/pdf_viewer.js') }}"></script>
        <script>
            pdfjsLib.GlobalWorkerOptions.workerSrc = @json(asset('pdfjs/build/pdf.worker.min.js'));

            const file = @json($file);
            const eventBus = new pdfjsViewer.EventBus();
            const linkService = new pdfjsViewer.PDFLinkService({ eventBus });
            const container = document.getElementById('viewerContainer');
            const pdfViewer = new pdfjsViewer.PDFViewer({
                container,
                viewer: document.getElementById('viewer'),
                eventBus,
                linkService,
                textLayerMode: 0,
            });

            linkService.setViewer(pdfViewer);

            eventBus.on('pagesinit', () => {
                pdfViewer.currentScaleValue = 'page-width';
            });

            pdfjsLib.getDocument(file).promise.then((pdfDocument) => {
                pdfViewer.setDocument(pdfDocument);
                linkService.setDocument(pdfDocument, null);
            }).catch((error) => {
                console.error(error);
                document.body.innerHTML = '<p style="color:white;text-align:center;padding:2rem;">Gagal memuat PDF.</p>';
            });
        </script>
    </body>
</html>

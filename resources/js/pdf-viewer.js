import * as pdfjsLib from 'pdfjs-dist/build/pdf';
import pdfjsWorker from 'pdfjs-dist/build/pdf.worker?url';

// Configure worker using Vite-loaded URL
pdfjsLib.GlobalWorkerOptions.workerSrc = pdfjsWorker;

function initPdfViewer(container) {
    const pdfUrl = container.getAttribute('data-pdf-url');
    if (!pdfUrl) {
        console.error('PDF URL is missing for viewer', container);
        return;
    }

    const canvas = container.querySelector('.pdfjs-canvas');
    const ctx = canvas.getContext('2d');
    const loadingEl = container.querySelector('.pdfjs-loading');
    const errorEl = container.querySelector('.pdfjs-error');
    const pageNumEl = container.querySelector('.pdfjs-page-num');
    const pageCountEl = container.querySelector('.pdfjs-page-count');
    const zoomPercentEl = container.querySelector('.pdfjs-zoom-percent');

    const btnPrev = container.querySelector('.pdfjs-btn-prev');
    const btnNext = container.querySelector('.pdfjs-btn-next');
    const btnZoomIn = container.querySelector('.pdfjs-btn-zoom-in');
    const btnZoomOut = container.querySelector('.pdfjs-btn-zoom-out');

    let pdfDoc = null;
    let currentPage = 1;
    let scale = 1.1; // default zoom
    let isRendering = false;
    let pendingPage = null;

    function showLoading(show) {
        if (loadingEl) {
            loadingEl.classList.toggle('d-none', !show);
        }
    }

    function showError(message) {
        if (errorEl) {
            if (message) {
                errorEl.textContent = message;
            }
            errorEl.classList.remove('d-none');
        }
    }

    function updateZoomLabel() {
        if (zoomPercentEl) {
            zoomPercentEl.textContent = Math.round(scale * 100);
        }
    }

    function renderPage(num) {
        isRendering = true;
        showLoading(true);

        pdfDoc.getPage(num).then(function (page) {
            const viewport = page.getViewport({ scale });
            const outputScale = window.devicePixelRatio || 1;

            canvas.width = viewport.width * outputScale;
            canvas.height = viewport.height * outputScale;
            canvas.style.width = viewport.width + 'px';
            canvas.style.height = viewport.height + 'px';

            const renderContext = {
                canvasContext: ctx,
                transform:
                    outputScale !== 1 ? [outputScale, 0, 0, outputScale, 0, 0] : null,
                viewport
            };

            const renderTask = page.render(renderContext);
            renderTask.promise
                .then(function () {
                    isRendering = false;
                    showLoading(false);

                    if (pendingPage !== null) {
                        const next = pendingPage;
                        pendingPage = null;
                        renderPage(next);
                    }
                })
                .catch(function (err) {
                    console.error(err);
                    isRendering = false;
                    showLoading(false);
                    showError(err.message || 'Failed to render PDF page.');
                });
        });

        if (pageNumEl) {
            pageNumEl.textContent = num;
        }
    }

    function queueRenderPage(num) {
        if (isRendering) {
            pendingPage = num;
        } else {
            renderPage(num);
        }
    }

    function onPrevPage() {
        if (currentPage <= 1) return;
        currentPage--;
        queueRenderPage(currentPage);
    }

    function onNextPage() {
        if (!pdfDoc || currentPage >= pdfDoc.numPages) return;
        currentPage++;
        queueRenderPage(currentPage);
    }

    function onZoom(delta) {
        const newScale = scale + delta;
        if (newScale < 0.5 || newScale > 3) return;
        scale = newScale;
        updateZoomLabel();
        queueRenderPage(currentPage);
    }

    if (btnPrev) btnPrev.addEventListener('click', onPrevPage);
    if (btnNext) btnNext.addEventListener('click', onNextPage);
    if (btnZoomIn) btnZoomIn.addEventListener('click', () => onZoom(0.2));
    if (btnZoomOut) btnZoomOut.addEventListener('click', () => onZoom(-0.2));

    updateZoomLabel();
    showLoading(true);

    pdfjsLib
        .getDocument(pdfUrl)
        .promise.then(function (pdf) {
            pdfDoc = pdf;
            if (pageCountEl) {
                pageCountEl.textContent = pdf.numPages;
            }
            renderPage(currentPage);
        })
        .catch(function (err) {
            console.error('Error loading PDF:', err);
            showLoading(false);
            showError(err.message || 'Failed to load PDF document.');
        });
}

export function initPdfViewers() {
    document
        .querySelectorAll('.pdfjs-viewer-container')
        .forEach(initPdfViewer);
}

// Auto-init on DOM ready for all pages that load this bundle
document.addEventListener('DOMContentLoaded', () => {
    initPdfViewers();
});



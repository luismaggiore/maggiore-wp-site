/**
 * Maggiore — Portafolio PDF Flipbook
 * Usa PDF.js (3.11) + StPageFlip (2.0.7)
 */
(function () {
    'use strict';

    // ── Referencias DOM ─────────────────────────────────────────────────────
    var wrapper = document.getElementById('mg-flipbook-wrapper');
    if (!wrapper) return;

    var pdfUrl = wrapper.dataset.pdfUrl;
    if (!pdfUrl) return;

    var loadingEl   = document.getElementById('mg-flipbook-loading');
    var progressEl  = document.getElementById('mg-flipbook-progress');
    var container   = document.getElementById('mg-flipbook-container');
    var currentEl   = document.getElementById('mg-flipbook-current');
    var totalEl     = document.getElementById('mg-flipbook-total');
    var prevBtn     = document.getElementById('mg-flipbook-prev');
    var nextBtn     = document.getElementById('mg-flipbook-next');
    var fsBtn       = document.getElementById('mg-flipbook-fullscreen');
    var section     = document.getElementById('mg-flipbook-section');

    // ── Verificar que las librerías estén disponibles ───────────────────────
    if (typeof pdfjsLib === 'undefined') {
        showError('No se pudo cargar PDF.js. Verifica tu conexión a internet.');
        console.error('[Flipbook] pdfjsLib no está disponible.');
        return;
    }

    // StPageFlip puede estar en window.St.PageFlip o window.PageFlip
    var PageFlipClass = null;
    if (window.St && window.St.PageFlip) {
        PageFlipClass = window.St.PageFlip;
    } else if (window.PageFlip) {
        PageFlipClass = window.PageFlip;
    } else {
        showError('No se pudo cargar StPageFlip. Verifica tu conexión a internet.');
        console.error('[Flipbook] StPageFlip no está disponible. window.St:', window.St, 'window.PageFlip:', window.PageFlip);
        return;
    }

    // ── Configurar worker de PDF.js ─────────────────────────────────────────
    pdfjsLib.GlobalWorkerOptions.workerSrc =
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    var pageFlip  = null;
    var totalPages = 0;

    // ── Cargar y renderizar el PDF ──────────────────────────────────────────
    pdfjsLib.getDocument({ url: pdfUrl })
        .promise
        .then(function (pdf) {

            totalPages = pdf.numPages;
            totalEl.textContent = totalPages;

            var renderChain = Promise.resolve();
            var pageWidth   = 0;
            var pageHeight  = 0;

            for (var n = 1; n <= totalPages; n++) {
                (function (pageNum) {
                    renderChain = renderChain.then(function () {
                        return pdf.getPage(pageNum).then(function (page) {

                            // Escala adaptativa: apunta a 750px de ancho
                            var baseViewport = page.getViewport({ scale: 1 });
                            var scale        = 750 / baseViewport.width;
                            var viewport     = page.getViewport({ scale: scale });

                            if (pageNum === 1) {
                                pageWidth  = Math.floor(viewport.width);
                                pageHeight = Math.floor(viewport.height);
                            }

                            var canvas    = document.createElement('canvas');
                            canvas.width  = viewport.width;
                            canvas.height = viewport.height;

                            return page.render({
                                canvasContext: canvas.getContext('2d'),
                                viewport: viewport,
                            }).promise.then(function () {

                                // Actualizar progreso
                                var pct = Math.round((pageNum / totalPages) * 100);
                                progressEl.textContent = 'Procesando página ' + pageNum + ' de ' + totalPages + '…';
                                var bar = document.getElementById('mg-flipbook-bar');
                                if (bar) bar.style.width = pct + '%';

                                // Crear página para StPageFlip
                                var pageDiv = document.createElement('div');
                                pageDiv.className = 'mg-flip-page';

                                var img = document.createElement('img');
                                img.src = canvas.toDataURL('image/jpeg', 0.88);
                                img.alt = 'Página ' + pageNum;
                                img.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;';

                                pageDiv.appendChild(img);
                                container.appendChild(pageDiv);
                            });
                        });
                    });
                })(n);
            }

            return renderChain.then(function () {
                initFlipbook(pageWidth, pageHeight, PageFlipClass);
            });

        })
        .catch(function (err) {
            showError('No se pudo cargar el PDF: ' + err.message);
            console.error('[Flipbook] Error al cargar PDF:', err);
        });

    // ── Inicializar StPageFlip ──────────────────────────────────────────────
    function initFlipbook(pageWidth, pageHeight, FlipClass) {

        loadingEl.style.display = 'none';
        container.style.display = 'block';
        section.classList.add('mg-flipbook-ready');

        pageFlip = new FlipClass(container, {
            width:               pageWidth,
            height:              pageHeight,
            size:                'stretch',
            minWidth:            200,
            maxWidth:            800,
            minHeight:           260,
            maxHeight:           1400,
            maxShadowOpacity:    0.6,
            showCover:           true,
            mobileScrollSupport: false,
            useMouseEvents:      true,
            swipeDistance:       30,
            clickEventForward:   true,
        });

        pageFlip.loadFromHTML(container.querySelectorAll('.mg-flip-page'));

        // StPageFlip setea width en el container vía JS después de init.
        // Esperamos un frame para que aplique y luego centramos.
        pageFlip.on('init', function () {
            container.style.margin = '0 auto';
        });

        pageFlip.on('flip', function (e) {
            currentEl.textContent = Math.min(e.data + 1, totalPages);
        });

        currentEl.textContent = '1';

        // ── Controles ──────────────────────────────────────────────────────
        prevBtn.addEventListener('click', function () { pageFlip.flipPrev('bottom'); });
        nextBtn.addEventListener('click', function () { pageFlip.flipNext('bottom'); });

        document.addEventListener('keydown', function (e) {
            if (!isVisible(wrapper)) return;
            if (e.key === 'ArrowLeft'  || e.key === 'ArrowUp')   pageFlip.flipPrev();
            if (e.key === 'ArrowRight' || e.key === 'ArrowDown')  pageFlip.flipNext();
        });

        if (fsBtn) {
            fsBtn.addEventListener('click', function () {
                if (!document.fullscreenElement) {
                    section.requestFullscreen && section.requestFullscreen();
                } else {
                    document.exitFullscreen && document.exitFullscreen();
                }
            });

            document.addEventListener('fullscreenchange', function () {
                var icon = fsBtn.querySelector('.mg-fs-icon');
                if (icon) icon.textContent = document.fullscreenElement ? '⤓' : '⛶';
            });
        }
    }

    // ── Helpers ─────────────────────────────────────────────────────────────
    function showError(msg) {
        if (loadingEl) {
            loadingEl.innerHTML =
                '<p style="color:#c00;padding:20px;text-align:center;">⚠️ ' + msg + '</p>';
        }
    }

    function isVisible(el) {
        var rect = el.getBoundingClientRect();
        return rect.top < window.innerHeight && rect.bottom > 0;
    }

})();

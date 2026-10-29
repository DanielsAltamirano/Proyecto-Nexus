@props(['targetModel' => 'scanQuery', 'onSuccess' => null])

<div x-data="barcodeScannerComponent('{{ $targetModel }}', {{ $onSuccess ? "'".$onSuccess."'" : 'null' }})"
     x-on:open-scanner-{{ strtolower($targetModel) }}.window="startScanner"
     x-show="isScanning"
     x-cloak
     class="fixed inset-0 z-[9999] overflow-y-auto"
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <!-- Background overlay -->
    <div x-show="isScanning" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" 
         @click="stopScanner"></div>

    <!-- Modal panel -->
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0 pointer-events-none">
        <div x-show="isScanning"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="pointer-events-auto relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
            
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-green-600" viewBox="0 0 24 24" fill="currentColor">
                            <rect x="3" y="5" width="2" height="14"></rect>
                            <rect x="7" y="5" width="1" height="14"></rect>
                            <rect x="10" y="5" width="3" height="14"></rect>
                            <rect x="15" y="5" width="1" height="14"></rect>
                            <rect x="18" y="5" width="2" height="14"></rect>
                            <rect x="22" y="5" width="1" height="14"></rect>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                        <h3 class="text-xl font-bold leading-6 text-gray-900" id="modal-title">Escanear Código de Barras</h3>
                        <div class="mt-2 w-full">
                            <p class="text-sm text-gray-500 mb-4">Apunta la cámara al código de barras del producto. La lectura será automática.</p>
                            
                            <!-- Scanner container -->
                            <div id="reader-{{ $targetModel }}" class="w-full bg-black rounded-lg overflow-hidden border-2 border-gray-200 sm:aspect-video aspect-square relative flex items-center justify-center">
                                <div x-show="isLoading" class="absolute inset-0 flex items-center justify-center bg-gray-900 bg-opacity-75 z-10">
                                    <svg class="animate-spin h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                <button type="button" @click="stopScanner" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">Cancelar</button>
            </div>
        </div>
    </div>
</div>

@once
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('barcodeScannerComponent', (targetModel, onSuccess) => ({
            isScanning: false,
            isLoading: false,
            html5Qrcode: null,
            targetModel: targetModel,
            onSuccess: onSuccess,

            startScanner() {
                this.isScanning = true;
                this.isLoading = true;
                this.error = null;
                
                // Unlock audio context on user gesture (the click that opened the modal)
                try {
                    if (!window.globalAudioCtx) {
                        window.globalAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    }
                    if (window.globalAudioCtx.state === 'suspended') {
                        window.globalAudioCtx.resume();
                    }
                } catch(e) {}
                
                // Ensure Html5Qrcode is loaded
                if (typeof window.Html5Qrcode === 'undefined') {
                    console.error('Html5Qrcode not loaded');
                    this.isLoading = false;
                    alert("El escáner aún se está cargando. Intenta de nuevo en unos segundos.");
                    return;
                }

                // Initialize if not already
                if (!this.html5Qrcode) {
                    this.html5Qrcode = new window.Html5Qrcode("reader-" + this.targetModel);
                }

                // Config to focus on 1D barcodes
                const config = {
                    fps: 10,
                    qrbox: { width: 300, height: 150 }, 
                    aspectRatio: 1.0,
                    // Optionally restrict formats to 1D only, but auto-detect is usually fine.
                };

                this.html5Qrcode.start(
                    { facingMode: "environment" },
                    config,
                    (decodedText, decodedResult) => {
                        this.onScanSuccess(decodedText);
                    },
                    (errorMessage) => {
                        // Ignore continuous errors
                    }
                ).then(() => {
                    this.isLoading = false;
                }).catch((err) => {
                    console.error("Error starting scanner", err);
                    this.isLoading = false;
                    this.isScanning = false;
                    alert("No se pudo iniciar la cámara. Asegúrate de dar permisos de cámara en tu navegador.");
                });
            },

            stopScanner() {
                this.isScanning = false;
                if (this.html5Qrcode && this.html5Qrcode.isScanning) {
                    this.html5Qrcode.stop().catch(err => console.error("Error stopping scanner", err));
                }
            },

            onScanSuccess(decodedText) {
                // Beep sound using Web Audio API
                try {
                    const ctx = window.globalAudioCtx || new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = ctx.createOscillator();
                    const gainNode = ctx.createGain();
                    oscillator.type = 'sine';
                    oscillator.frequency.setValueAtTime(800, ctx.currentTime); // 800Hz beep
                    gainNode.gain.setValueAtTime(0.5, ctx.currentTime);
                    oscillator.connect(gainNode);
                    gainNode.connect(ctx.destination);
                    oscillator.start();
                    setTimeout(() => { oscillator.stop(); }, 150);
                } catch (e) {
                    console.error("Audio beep failed", e);
                }
                
                this.stopScanner();
                
                // Emit event to update the model in parent scope
                this.$dispatch('scanner-result', { target: this.targetModel, value: decodedText });
                
                // If there's an onSuccess callback in parent scope, call it
                if (this.onSuccess) {
                    setTimeout(() => {
                        this.$dispatch(this.onSuccess);
                    }, 100);
                }
            }
        }));
    });
</script>
<style>
    /* Styling to hide the default html5-qrcode UI elements that are ugly */
    #reader-*__dashboard_section_csr span {
        color: white !important;
    }
    #reader-* video {
        object-fit: cover !important;
    }
</style>
@endonce

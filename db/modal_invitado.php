<?php
/**
 * Sistema de Control de Acceso para Usuarios Invitados
 * Versión optimizada y profesional
 */

require_once 'conexion.php';

class GuestAccessControl {
    private $conexion;
    private $isGuest;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
        $this->isGuest = $this->checkGuestStatus();
    }
    
    /**
     * Verifica si el usuario actual es invitado
     */
    private function checkGuestStatus(): bool {
        if (!isset($_SESSION['id_usuario']) || empty($_SESSION['id_usuario'])) {
            return true;
        }
        
        $stmt = $this->conexion->prepare("SELECT id_usuario FROM usuarios WHERE id_usuario = ? LIMIT 1");
        $stmt->bind_param("i", $_SESSION['id_usuario']);
        $stmt->execute();
        
        return $stmt->get_result()->num_rows === 0;
    }
    
    /**
     * Retorna el estado del invitado para JavaScript
     */
    public function getGuestStatus(): bool {
        return $this->isGuest;
    }
    
    /**
     * Genera el modal HTML
     */
    public function renderModal(): string {
        return '
        <link rel="stylesheet" href="modal_invitado.css">
        
        <div class="modal fade" id="guestModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content modal-acupro">
                    <div class="modal-header border-0">
                        <div class="countdown-container">
                            <span class="countdown-text">Redirigiendo en <span id="countdown">5</span> segundos...</span>
                        </div>
                    </div>
                    <div class="modal-body text-center p-4">
                        <div class="modal-icon-container mb-4">
                            <div class="modal-icon">
                                <i class="bi bi-person-plus-fill"></i>
                            </div>
                        </div>
                        <h4 class="modal-title-custom mb-3">¡Registro Requerido!</h4>
                        <p class="modal-text mb-4">
                            Para acceder a esta función debes tener una cuenta registrada en <strong>ACUPRO</strong>
                        </p>
                        <div class="modal-features mb-4">
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Gestiona tus citas fácilmente</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Acceso completo al panel</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span>Historial de actividades</span>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-acupro-primary" onclick="GuestControl.redirectToRegister()">
                                <i class="bi bi-person-plus me-2"></i>Crear Cuenta
                            </button>
                            <button type="button" class="btn btn-acupro-secondary" onclick="GuestControl.cancel()">
                                <i class="bi bi-x-circle me-2"></i>Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="blockOverlay" class="overlay-bloqueo" style="display: none;">
            <div class="overlay-content">
                <i class="bi bi-lock-fill"></i>
                <p>Función bloqueada para invitados</p>
            </div>
        </div>';
    }
    
    /**
     * Genera el JavaScript optimizado
     */
    public function renderScript(): string {
        $isGuest = $this->isGuest ? 'true' : 'false';
        
        return "
        <script>
        class GuestAccessController {
            constructor(isGuest) {
                this.isGuest = isGuest;
                this.countdownTimer = null;
                this.modal = null;
                this.init();
            }
            
            init() {
                if (!this.isGuest) return;
                
                document.addEventListener('DOMContentLoaded', () => {
                    this.setupModal();
                    this.blockElements();
                    this.setupObserver();
                    this.showInitialBlocking();
                });
            }
            
            setupModal() {
                this.modal = new bootstrap.Modal(document.getElementById('guestModal'));
            }
            
            blockElements() {
                const elements = document.querySelectorAll('.requiere-registro, [data-requiere-registro=\"true\"]');
                elements.forEach(el => this.blockElement(el));
            }
            
            blockElement(element) {
                element.classList.add('elemento-bloqueado');
                
                // Prevenir eventos
                ['click', 'submit'].forEach(event => {
                    element.addEventListener(event, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this.showModal();
                    }, true);
                });
                
                // Deshabilitar según tipo
                if (element.tagName === 'A') element.removeAttribute('href');
                if (['BUTTON', 'INPUT'].includes(element.tagName)) element.disabled = true;
            }
            
            showModal() {
                if (!this.modal || !this.isGuest) return;
                
                this.modal.show();
                this.startCountdown();
            }
            
            startCountdown() {
                let count = 5;
                const countEl = document.getElementById('countdown');
                
                this.countdownTimer = setInterval(() => {
                    count--;
                    if (countEl) countEl.textContent = count;
                    
                    if (count <= 0) {
                        this.cleanup();
                        this.redirectToHome();
                    }
                }, 1000);
            }
            
            cleanup() {
                if (this.countdownTimer) {
                    clearInterval(this.countdownTimer);
                    this.countdownTimer = null;
                }
            }
            
            redirectToRegister() {
                this.cleanup();
                if (this.modal) this.modal.hide();
                window.location.href = 'index.php';
            }
            
            cancel() {
                this.cleanup();
                if (this.modal) this.modal.hide();
                setTimeout(() => window.location.href = 'index.php', 300);
            }
            
            redirectToHome() {
                window.location.href = 'index.php';
            }
            
            showInitialBlocking() {
                const overlay = document.getElementById('blockOverlay');
                if (overlay) {
                    overlay.style.display = 'flex';
                    setTimeout(() => {
                        overlay.style.display = 'none';
                        this.showModal();
                    }, 1500);
                }
            }
            
            setupObserver() {
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach(mutation => {
                        if (mutation.type === 'childList') {
                            mutation.addedNodes.forEach(node => {
                                if (node.nodeType === 1) {
                                    const newElements = node.querySelectorAll('.requiere-registro, [data-requiere-registro=\"true\"]');
                                    newElements.forEach(el => this.blockElement(el));
                                }
                            });
                        }
                    });
                });
                
                observer.observe(document.body, { childList: true, subtree: true });
            }
            
            // API pública
            checkAccess() {
                if (this.isGuest) {
                    this.showModal();
                    return false;
                }
                return true;
            }
        }
        
        // Inicializar sistema
        const GuestControl = new GuestAccessController($isGuest);
        
        // API global para verificación manual
        window.verificarAccesoInvitado = () => GuestControl.checkAccess();
        
        // Cleanup al salir
        window.addEventListener('beforeunload', () => GuestControl.cleanup());
        </script>";
    }
}

// Uso del sistema
$guestControl = new GuestAccessControl($conexion);

// Para incluir en las páginas que necesiten control de acceso:
echo $guestControl->renderModal();
echo $guestControl->renderScript();

// Para verificar estado desde PHP:
// $esInvitado = $guestControl->getGuestStatus();
?>
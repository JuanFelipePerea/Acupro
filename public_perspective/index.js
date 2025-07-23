// AcuPro - Sistema de Efectos Visuales Profesionales
class AcuProEffects {
    constructor() {
        this.init();
        this.setupEventListeners();
        this.createParticleSystem();
        this.initScrollAnimations();
        this.setupInteractiveElements();
    }

    init() {
        // Configuración inicial
        this.isReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        this.isMobile = window.innerWidth <= 768;
        
        // Elementos principales
        this.hero = document.querySelector('.hero');
        this.header = document.querySelector('header');
        this.sections = document.querySelectorAll('.section');
        
        // Variables para efectos
        this.scrollY = 0;
        this.ticking = false;
        
        // Inicializar efectos básicos
        this.setupCSS();
    }

    setupCSS() {
        const style = document.createElement('style');
        style.textContent = `
            /* Efectos de partículas y gradientes animados */
            .hero-particles {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                overflow: hidden;
            }

            .particle {
                position: absolute;
                background: rgba(255, 255, 255, 0.6);
                border-radius: 50%;
                pointer-events: none;
                animation: float 6s ease-in-out infinite;
            }

            @keyframes float {
                0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0; }
                10% { opacity: 1; }
                90% { opacity: 1; }
                50% { transform: translateY(-20px) rotate(180deg); }
            }

            /* Efectos de hover mejorados */
            .enhanced-hover {
                position: relative;
                overflow: hidden;
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }

            .enhanced-hover::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                transition: left 0.5s;
            }

            .enhanced-hover:hover::before {
                left: 100%;
            }

            /* Efecto de typing para títulos */
            .typing-effect {
                overflow: hidden;
                border-right: 2px solid #48bb78;
                white-space: nowrap;
                animation: typing 3s steps(40, end), blink-caret 0.75s step-end infinite;
            }

            @keyframes typing {
                from { width: 0 }
                to { width: 100% }
            }

            @keyframes blink-caret {
                from, to { border-color: transparent }
                50% { border-color: #48bb78 }
            }

            /* Efectos de glassmorphism */
            .glass-effect {
                background: rgba(255, 255, 255, 0.25);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.18);
                box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            }

            /* Animaciones de entrada */
            .slide-in-left {
                transform: translateX(-100px);
                opacity: 0;
                transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            }

            .slide-in-right {
                transform: translateX(100px);
                opacity: 0;
                transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            }

            .slide-in-up {
                transform: translateY(50px);
                opacity: 0;
                transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            }

            .scale-in {
                transform: scale(0.8);
                opacity: 0;
                transition: all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            }

            .animate-visible {
                transform: translateX(0) translateY(0) scale(1);
                opacity: 1;
            }

            /* Efectos de hover para iconos */
            .icon-bounce {
                transition: transform 0.3s ease;
            }

            .icon-bounce:hover {
                transform: scale(1.1) rotateY(180deg);
                animation: bounce 0.6s ease;
            }

            @keyframes bounce {
                0%, 20%, 53%, 80%, 100% { transform: scale(1.1) rotateY(180deg) translateZ(0); }
                40%, 43% { transform: scale(1.1) rotateY(180deg) translateZ(-30px); }
            }

            /* Gradiente animado para hero */
            .animated-gradient {
                background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #f5576c);
                background-size: 400% 400%;
                animation: gradientShift 15s ease infinite;
            }

            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }

            /* Efectos de loading y transiciones */
            .loading-dots::after {
                content: '';
                display: inline-block;
                animation: ellipsis 1.25s infinite;
            }

            @keyframes ellipsis {
                0% { content: '.'; }
                33% { content: '..'; }
                66% { content: '...'; }
            }

            /* Micro-interacciones */
            .micro-interaction {
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .micro-interaction:active {
                transform: scale(0.98);
            }

            /* Efecto parallax suave */
            .parallax-element {
                transform: translateZ(0);
                will-change: transform;
            }
        `;
        document.head.appendChild(style);
    }

    createParticleSystem() {
        if (this.isReduced || this.isMobile) return;

        const particleContainer = document.createElement('div');
        particleContainer.className = 'hero-particles';
        this.hero.appendChild(particleContainer);

        // Crear partículas
        for (let i = 0; i < 50; i++) {
            this.createParticle(particleContainer);
        }
    }

    createParticle(container) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        const size = Math.random() * 4 + 2;
        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        particle.style.left = `${Math.random() * 100}%`;
        particle.style.top = `${Math.random() * 100}%`;
        particle.style.animationDelay = `${Math.random() * 6}s`;
        particle.style.animationDuration = `${6 + Math.random() * 4}s`;
        
        container.appendChild(particle);
    }

    initScrollAnimations() {
        // Configurar intersection observer para animaciones
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '-50px 0px -50px 0px'
        };

        this.animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateElement(entry.target);
                }
            });
        }, observerOptions);

        // Observar elementos animables
        this.setupAnimatableElements();
    }

    setupAnimatableElements() {
        // Configurar animaciones específicas para diferentes elementos
        const elements = [
            { selector: '.section-title', animation: 'slide-in-up' },
            { selector: '.section-subtitle', animation: 'slide-in-up' },
            { selector: '.about-text', animation: 'slide-in-left' },
            { selector: '.about-visual', animation: 'slide-in-right' },
            { selector: '.step-card:nth-child(odd)', animation: 'slide-in-left' },
            { selector: '.step-card:nth-child(even)', animation: 'slide-in-right' },
            { selector: '.benefit-item', animation: 'scale-in' }
        ];

        elements.forEach(({ selector, animation }) => {
            document.querySelectorAll(selector).forEach((el, index) => {
                el.classList.add(animation);
                el.style.transitionDelay = `${index * 0.1}s`;
                this.animationObserver.observe(el);
            });
        });
    }

    animateElement(element) {
        element.classList.add('animate-visible');
    }

    setupEventListeners() {
        // Scroll optimizado
        window.addEventListener('scroll', () => {
            this.scrollY = window.pageYOffset;
            if (!this.ticking) {
                requestAnimationFrame(() => {
                    this.updateScrollEffects();
                    this.ticking = false;
                });
                this.ticking = true;
            }
        });

        // Resize handler
        window.addEventListener('resize', this.debounce(() => {
            this.isMobile = window.innerWidth <= 768;
        }, 250));

        // Mouse tracking para efectos parallax sutiles
        if (!this.isMobile) {
            document.addEventListener('mousemove', this.throttle((e) => {
                this.updateMouseEffects(e);
            }, 16));
        }
    }

    updateScrollEffects() {
        // Parallax sutil para el hero
        if (this.hero && this.scrollY < window.innerHeight) {
            const parallaxSpeed = this.scrollY * 0.5;
            this.hero.style.transform = `translateY(${parallaxSpeed}px)`;
        }

        // Header backdrop blur dinámico
        if (this.header) {
            const opacity = Math.min(this.scrollY / 100, 0.98);
            this.header.style.background = `rgba(255, 255, 255, ${opacity})`;
            
            if (this.scrollY > 50) {
                this.header.style.backdropFilter = 'blur(20px)';
                this.header.style.boxShadow = '0 4px 30px rgba(0, 0, 0, 0.1)';
            } else {
                this.header.style.backdropFilter = 'blur(10px)';
                this.header.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
            }
        }
    }

    updateMouseEffects(event) {
        const mouseX = (event.clientX / window.innerWidth) * 2 - 1;
        const mouseY = (event.clientY / window.innerHeight) * 2 - 1;

        // Efecto parallax sutil en iconos
        document.querySelectorAll('.step-icon, .benefit-icon').forEach(icon => {
            const speed = 10;
            const x = mouseX * speed;
            const y = mouseY * speed;
            icon.style.transform = `translate(${x}px, ${y}px)`;
        });
    }

    setupInteractiveElements() {
        // Mejorar botones CTA
        this.enhanceCTAButtons();
        
        // Mejorar cards interactivas
        this.enhanceCards();
        
        // Agregar efectos de hover a iconos
        this.enhanceIcons();
        
        // Efecto de typing en el título principal
        this.addTypingEffect();
    }

    enhanceCTAButtons() {
        document.querySelectorAll('.cta-button, .cta-button-white').forEach(button => {
            button.classList.add('enhanced-hover', 'micro-interaction');
            
            button.addEventListener('mouseenter', () => {
                if (!this.isReduced) {
                    button.style.transform = 'translateY(-3px) scale(1.02)';
                }
            });

            button.addEventListener('mouseleave', () => {
                button.style.transform = 'translateY(0) scale(1)';
            });

            button.addEventListener('click', (e) => {
                this.createRippleEffect(e, button);
            });
        });
    }

    enhanceCards() {
        document.querySelectorAll('.step-card, .benefit-item').forEach(card => {
            card.classList.add('enhanced-hover');
            
            card.addEventListener('mouseenter', () => {
                if (!this.isReduced) {
                    const icon = card.querySelector('.step-icon, .benefit-icon');
                    if (icon) {
                        icon.style.transform = 'scale(1.1) rotateY(10deg)';
                    }
                }
            });

            card.addEventListener('mouseleave', () => {
                const icon = card.querySelector('.step-icon, .benefit-icon');
                if (icon) {
                    icon.style.transform = 'scale(1) rotateY(0deg)';
                }
            });
        });
    }

    enhanceIcons() {
        document.querySelectorAll('.step-icon i, .benefit-icon i, .about-visual i').forEach(icon => {
            icon.classList.add('icon-bounce');
        });
    }

    addTypingEffect() {
        const heroTitle = document.querySelector('.hero h1');
        if (heroTitle && !this.isMobile) {
            // Aplicar efecto solo en desktop
            heroTitle.style.overflow = 'hidden';
            heroTitle.style.borderRight = '2px solid #48bb78';
            heroTitle.style.whiteSpace = 'nowrap';
            heroTitle.style.animation = 'typing 3s steps(40, end), blink-caret 0.75s step-end infinite';
            
            // Remover el cursor después de la animación
            setTimeout(() => {
                heroTitle.style.borderRight = 'none';
            }, 4000);
        }
    }

    createRippleEffect(event, element) {
        const ripple = document.createElement('span');
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;

        ripple.style.cssText = `
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: ripple 0.6s ease-out;
            pointer-events: none;
        `;

        // Asegurar posición relativa
        element.style.position = 'relative';
        element.style.overflow = 'hidden';
        element.appendChild(ripple);

        // Limpiar después de la animación
        setTimeout(() => ripple.remove(), 600);
    }

    // Utilities
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // Método público para agregar nuevos efectos
    addCustomEffect(selector, effectFunction) {
        document.querySelectorAll(selector).forEach(effectFunction);
    }

    // Método para limpiar efectos (útil para SPAs)
    destroy() {
        if (this.animationObserver) {
            this.animationObserver.disconnect();
        }
        // Limpiar event listeners
        window.removeEventListener('scroll', this.updateScrollEffects);
        window.removeEventListener('resize', this.handleResize);
    }
}

// Agregar estilos de animación para ripple
const rippleStyle = document.createElement('style');
rippleStyle.textContent = `
    @keyframes ripple {
        0% {
            transform: scale(0);
            opacity: 1;
        }
        100% {
            transform: scale(2);
            opacity: 0;
        }
    }
`;
document.head.appendChild(rippleStyle);

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    // Crear instancia global
    window.acuProEffects = new AcuProEffects();
    
    // Agregar algunos efectos adicionales específicos
    setTimeout(() => {
        // Gradiente animado para hero
        const hero = document.querySelector('.hero');
        if (hero) {
            hero.classList.add('animated-gradient');
        }
        
        // Efecto glass en header
        const header = document.querySelector('header');
        if (header) {
            header.classList.add('glass-effect');
        }
    }, 100);
});

// Exportar para uso en módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AcuProEffects;
}
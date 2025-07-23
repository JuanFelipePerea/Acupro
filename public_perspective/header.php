<?php
// navbar.php - Componente de navegación profesional para AcuPro
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Variables CSS para el navbar */
        :root {
            --nav-primary-blue: #667eea;
            --nav-primary-purple: #764ba2;
            --nav-accent-green: #48bb78;
            --nav-gradient-primary: linear-gradient(135deg, var(--nav-primary-blue) 0%, var(--nav-primary-purple) 100%);
            --nav-text-primary: #2d3748;
            --nav-text-secondary: #4a5568;
            --nav-bg-glass: rgba(255, 255, 255, 0.85);
            --nav-bg-mobile: rgba(255, 255, 255, 0.98);
            --nav-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            --nav-shadow-mobile: 0 4px 24px rgba(0, 0, 0, 0.15);
            --nav-border: rgba(255, 255, 255, 0.18);
            --nav-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --nav-transition-bounce: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            --nav-z-index: 1050;
            --nav-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        /* Reset específico para navbar */
        .navbar-reset {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Navbar principal */
        .acupro-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: var(--nav-z-index);
            background: var(--nav-bg-glass);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid var(--nav-border);
            box-shadow: var(--nav-shadow);
            transition: var(--nav-transition);
            transform: translateY(0);
            font-family: var(--nav-font);
        }

        /* Estados del navbar */
        .acupro-navbar.scrolled {
            background: var(--nav-bg-mobile);
            box-shadow: var(--nav-shadow-mobile);
            backdrop-filter: blur(24px) saturate(200%);
        }

        .acupro-navbar.hidden {
            transform: translateY(-100%);
        }

        /* Container del navbar */
        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
            position: relative;
        }

        /* Logo mejorado */
        .navbar-logo {
            font-size: 1.875rem;
            font-weight: 800;
            background: var(--nav-gradient-primary);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
            position: relative;
            padding: 0.5rem 0;
            transition: var(--nav-transition-bounce);
            z-index: 2;
        }

        .navbar-logo::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background: var(--nav-gradient-primary);
            border-radius: 2px;
            transition: width 0.4s ease;
        }

        .navbar-logo:hover::before {
            width: 100%;
        }

        .navbar-logo:hover {
            transform: scale(1.05);
        }

        /* Navegación principal */
        .navbar-nav {
            display: flex;
            list-style: none;
            gap: 2.5rem;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .navbar-nav li {
            position: relative;
        }

        .navbar-nav a {
            text-decoration: none;
            color: var(--nav-text-secondary);
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.75rem 0;
            position: relative;
            transition: var(--nav-transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Efecto underline animado */
        .navbar-nav a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--nav-gradient-primary);
            border-radius: 1px;
            transform: translateX(-50%);
            transition: width 0.3s ease;
        }

        .navbar-nav a:hover {
            color: var(--nav-primary-blue);
            transform: translateY(-2px);
        }

        .navbar-nav a:hover::after {
            width: 100%;
        }

        /* Botón CTA del navbar */
        .navbar-cta {
            background: var(--nav-gradient-primary);
            color: white !important;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
            border: none;
            cursor: pointer;
            transition: var(--nav-transition-bounce);
        }

        .navbar-cta::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .navbar-cta:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.4);
        }

        .navbar-cta:hover::before {
            left: 100%;
        }

        .navbar-cta:hover::after {
            width: 0;
        }

        /* Hamburger menu */
        .navbar-toggle {
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 30px;
            height: 30px;
            cursor: pointer;
            z-index: 3;
            position: relative;
        }

        .hamburger-line {
            width: 25px;
            height: 3px;
            background: var(--nav-text-primary);
            margin: 2px 0;
            transition: var(--nav-transition);
            border-radius: 2px;
            transform-origin: center;
        }

        /* Animación del hamburger */
        .navbar-toggle.active .hamburger-line:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
            background: var(--nav-primary-blue);
        }

        .navbar-toggle.active .hamburger-line:nth-child(2) {
            opacity: 0;
            transform: scale(0);
        }

        .navbar-toggle.active .hamburger-line:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
            background: var(--nav-primary-blue);
        }

        /* Menu mobile */
        .navbar-mobile {
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            background: var(--nav-bg-mobile);
            backdrop-filter: blur(24px) saturate(200%);
            border-bottom: 1px solid var(--nav-border);
            box-shadow: var(--nav-shadow-mobile);
            transform: translateY(-100%);
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            z-index: var(--nav-z-index);
        }

        .navbar-mobile.active {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
        }

        .mobile-nav-list {
            list-style: none;
            padding: 2rem;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .mobile-nav-list li {
            opacity: 0;
            transform: translateX(-20px);
            animation: slideInMobile 0.4s ease forwards;
        }

        .mobile-nav-list li:nth-child(1) { animation-delay: 0.1s; }
        .mobile-nav-list li:nth-child(2) { animation-delay: 0.2s; }
        .mobile-nav-list li:nth-child(3) { animation-delay: 0.3s; }
        .mobile-nav-list li:nth-child(4) { animation-delay: 0.4s; }
        .mobile-nav-list li:nth-child(5) { animation-delay: 0.5s; }
        .mobile-nav-list li:nth-child(6) { animation-delay: 0.6s; }

        .mobile-nav-list a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            color: var(--nav-text-primary);
            text-decoration: none;
            font-weight: 500;
            font-size: 1.1rem;
            border-radius: 12px;
            transition: var(--nav-transition);
            position: relative;
            overflow: hidden;
        }

        .mobile-nav-list a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--nav-gradient-primary);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .mobile-nav-list a:hover {
            background: rgba(102, 126, 234, 0.1);
            transform: translateX(8px);
        }

        .mobile-nav-list a:hover::before {
            transform: scaleY(1);
        }

        .mobile-nav-list i {
            font-size: 1.2rem;
            color: var(--nav-primary-blue);
        }

        /* Indicador de página activa */
        .navbar-nav a.active {
            color: var(--nav-primary-blue);
            font-weight: 600;
        }

        .navbar-nav a.active::after {
            width: 100%;
        }

        /* Animaciones */
        @keyframes slideInMobile {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Ripple effect */
        .ripple {
            position: relative;
            overflow: hidden;
        }

        .ripple::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .ripple:active::after {
            width: 300px;
            height: 300px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar-nav {
                display: none;
            }

            .navbar-toggle {
                display: flex;
            }

            .navbar-container {
                padding: 0 1rem;
                height: 60px;
            }

            .navbar-logo {
                font-size: 1.5rem;
            }

            .navbar-mobile {
                top: 60px;
            }
        }

        @media (max-width: 480px) {
            .navbar-container {
                padding: 0 0.75rem;
            }

            .mobile-nav-list {
                padding: 1.5rem;
            }

            .mobile-nav-list a {
                padding: 0.75rem;
                font-size: 1rem;
            }
        }

        /* Modo reducido de movimiento */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Mejoras de accesibilidad */
        .navbar-nav a:focus,
        .navbar-cta:focus,
        .mobile-nav-list a:focus {
            outline: 2px solid var(--nav-primary-blue);
            outline-offset: 2px;
            border-radius: 4px;
        }

        /* Scroll indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: var(--nav-gradient-primary);
            transition: width 0.2s ease;
            border-radius: 0 2px 2px 0;
        }
    </style>
</head>

<body class="navbar-reset">
    <!-- Navbar Principal -->
    <nav class="acupro-navbar" id="navbar">
        <div class="navbar-container">
            <!-- Logo -->
            <a href="#inicio" class="navbar-logo ripple" id="logo">
                AcuPro
            </a>

            <!-- Navegación Desktop -->
            <ul class="navbar-nav" id="navLinks">
                <li><a href="#inicio" class="nav-link" data-section="inicio">
                    <i class="fas fa-home"></i>Inicio
                </a></li>
                <li><a href="#que-es" class="nav-link" data-section="que-es">
                    <i class="fas fa-question-circle"></i>¿Qué es?
                </a></li>
                <li><a href="#como-funciona" class="nav-link" data-section="como-funciona">
                    <i class="fas fa-cogs"></i>¿Cómo funciona?
                </a></li>
                <li><a href="#beneficios" class="nav-link" data-section="beneficios">
                    <i class="fas fa-star"></i>Beneficios
                </a></li>
                <li><a href="#contacto" class="nav-link" data-section="contacto">
                    <i class="fas fa-envelope"></i>Contacto
                </a></li>
                <li><a href="../db/login-registro/login.php" class="navbar-cta ripple">
                    <i class="fas fa-sign-in-alt"></i>Iniciar Sesión
                </a></li>
            </ul>

            <!-- Botón Hamburger -->
            <button class="navbar-toggle" id="navToggle" aria-label="Menú de navegación">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>
        </div>

        <!-- Indicador de scroll -->
        <div class="scroll-indicator" id="scrollIndicator"></div>
    </nav>

    <!-- Menu Mobile -->
    <div class="navbar-mobile" id="mobileMenu">
        <ul class="mobile-nav-list">
            <li><a href="#inicio" class="mobile-nav-link" data-section="inicio">
                <i class="fas fa-home"></i>Inicio
            </a></li>
            <li><a href="#que-es" class="mobile-nav-link" data-section="que-es">
                <i class="fas fa-question-circle"></i>¿Qué es AcuPro?
            </a></li>
            <li><a href="#como-funciona" class="mobile-nav-link" data-section="como-funciona">
                <i class="fas fa-cogs"></i>¿Cómo funciona?
            </a></li>
            <li><a href="#beneficios" class="mobile-nav-link" data-section="beneficios">
                <i class="fas fa-star"></i>Beneficios
            </a></li>
            <li><a href="#contacto" class="mobile-nav-link" data-section="contacto">
                <i class="fas fa-envelope"></i>Contacto
            </a></li>
            <li><a href="../db/login-registro/login.php" class="mobile-nav-link">
                <i class="fas fa-sign-in-alt"></i>Iniciar Sesión
            </a></li>
        </ul>
    </div>

    <script>
        // Navbar JavaScript Mejorado
        class AcuProNavbar {
            constructor() {
                this.navbar = document.getElementById('navbar');
                this.navToggle = document.getElementById('navToggle');
                this.mobileMenu = document.getElementById('mobileMenu');
                this.navLinks = document.querySelectorAll('.nav-link, .mobile-nav-link');
                this.scrollIndicator = document.getElementById('scrollIndicator');
                
                this.lastScrollTop = 0;
                this.scrollThreshold = 10;
                this.isMenuOpen = false;
                
                this.init();
            }

            init() {
                this.bindEvents();
                this.updateActiveLink();
                this.animateNavbarOnLoad();
            }

            bindEvents() {
                // Scroll events
                let ticking = false;
                window.addEventListener('scroll', () => {
                    if (!ticking) {
                        requestAnimationFrame(() => {
                            this.handleScroll();
                            this.updateScrollIndicator();
                            ticking = false;
                        });
                        ticking = true;
                    }
                });

                // Mobile menu toggle
                this.navToggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.toggleMobileMenu();
                });

                // Close mobile menu when clicking links
                this.navLinks.forEach(link => {
                    link.addEventListener('click', () => {
                        if (this.isMenuOpen) {
                            this.closeMobileMenu();
                        }
                        this.updateActiveLink(link);
                    });
                });

                // Close mobile menu when clicking outside
                document.addEventListener('click', (e) => {
                    if (this.isMenuOpen && 
                        !this.mobileMenu.contains(e.target) && 
                        !this.navToggle.contains(e.target)) {
                        this.closeMobileMenu();
                    }
                });

                // Smooth scroll for anchor links
                this.navLinks.forEach(link => {
                    link.addEventListener('click', (e) => {
                        const href = link.getAttribute('href');
                        if (href.startsWith('#')) {
                            e.preventDefault();
                            this.smoothScrollTo(href);
                        }
                    });
                });

                // Keyboard navigation
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.isMenuOpen) {
                        this.closeMobileMenu();
                    }
                });

                // Resize handler
                window.addEventListener('resize', () => {
                    if (window.innerWidth > 768 && this.isMenuOpen) {
                        this.closeMobileMenu();
                    }
                });
            }

            handleScroll() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                // Add scrolled class
                if (scrollTop > 50) {
                    this.navbar.classList.add('scrolled');
                } else {
                    this.navbar.classList.remove('scrolled');
                }

                // Hide/show navbar on scroll
                if (Math.abs(scrollTop - this.lastScrollTop) < this.scrollThreshold) {
                    return;
                }

                if (scrollTop > this.lastScrollTop && scrollTop > 100) {
                    // Scrolling down
                    this.navbar.classList.add('hidden');
                } else {
                    // Scrolling up
                    this.navbar.classList.remove('hidden');
                }

                this.lastScrollTop = scrollTop;
            }

            updateScrollIndicator() {
                const scrollTop = window.pageYOffset;
                const docHeight = document.documentElement.scrollHeight - window.innerHeight;
                const scrollPercent = (scrollTop / docHeight) * 100;
                
                this.scrollIndicator.style.width = `${scrollPercent}%`;
            }

            toggleMobileMenu() {
                if (this.isMenuOpen) {
                    this.closeMobileMenu();
                } else {
                    this.openMobileMenu();
                }
            }

            openMobileMenu() {
                this.isMenuOpen = true;
                this.navToggle.classList.add('active');
                this.mobileMenu.classList.add('active');
                document.body.style.overflow = 'hidden';
                
                // Animate menu items
                const menuItems = this.mobileMenu.querySelectorAll('li');
                menuItems.forEach((item, index) => {
                    item.style.animation = `slideInMobile 0.4s ease forwards`;
                    item.style.animationDelay = `${index * 0.1}s`;
                });
            }

            closeMobileMenu() {
                this.isMenuOpen = false;
                this.navToggle.classList.remove('active');
                this.mobileMenu.classList.remove('active');
                document.body.style.overflow = '';
            }

            updateActiveLink(activeLink = null) {
                // Remove active class from all links
                this.navLinks.forEach(link => link.classList.remove('active'));
                
                if (activeLink) {
                    activeLink.classList.add('active');
                    return;
                }

                // Auto-detect active section based on scroll position
                const sections = document.querySelectorAll('section[id]');
                const scrollPos = window.scrollY + 100;

                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.offsetHeight;
                    const sectionId = section.getAttribute('id');

                    if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                        const correspondingLink = document.querySelector(`[data-section="${sectionId}"]`);
                        if (correspondingLink) {
                            correspondingLink.classList.add('active');
                        }
                    }
                });
            }

            smoothScrollTo(target) {
                const targetElement = document.querySelector(target);
                if (targetElement) {
                    const offsetTop = targetElement.offsetTop - 80;
                    
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            }

            animateNavbarOnLoad() {
                // Animate navbar entrance
                this.navbar.style.transform = 'translateY(-100%)';
                this.navbar.style.opacity = '0';
                
                setTimeout(() => {
                    this.navbar.style.transition = 'all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                    this.navbar.style.transform = 'translateY(0)';
                    this.navbar.style.opacity = '1';
                }, 100);

                // Animate nav items
                const navItems = document.querySelectorAll('.navbar-nav li');
                navItems.forEach((item, index) => {
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(-20px)';
                    
                    setTimeout(() => {
                        item.style.transition = 'all 0.4s ease';
                        item.style.opacity = '1';
                        item.style.transform = 'translateY(0)';
                    }, 200 + (index * 100));
                });
            }

            // Public methods
            setActiveSection(sectionId) {
                this.navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.dataset.section === sectionId) {
                        link.classList.add('active');
                    }
                });
            }

            hideNavbar() {
                this.navbar.classList.add('hidden');
            }

            showNavbar() {
                this.navbar.classList.remove('hidden');
            }
        }

        // Initialize navbar when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            window.acuproNavbar = new AcuProNavbar();
        });

        // Intersection Observer for better section detection
        document.addEventListener('DOMContentLoaded', () => {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-link, .mobile-nav-link');

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && entry.intersectionRatio > 0.5) {
                        const sectionId = entry.target.getAttribute('id');
                        navLinks.forEach(link => {
                            link.classList.remove('active');
                            if (link.dataset.section === sectionId) {
                                link.classList.add('active');
                            }
                        });
                    }
                });
            }, {
                threshold: 0.5,
                rootMargin: '-80px 0px -80px 0px'
            });

            sections.forEach(section => {
                observer.observe(section);
            });
        });
    </script>
</body>
</html>
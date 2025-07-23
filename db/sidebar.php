<?php

/**
 * Componente de Barra Lateral (Sidebar) - Versión Optimizada
 * @version 1.1
 */
class Sidebar
{
    private $conexion;
    private $nombreUsuario;

    public function __construct($conexion, $nombreUsuario)
    {
        $this->conexion = $conexion;
        $this->nombreUsuario = $nombreUsuario;
    }

    private function obtenerEstadisticas()
    {
        $queries = [
            'totalCitas' => "SELECT COUNT(*) as total FROM citas",
            'citasSemana' => "SELECT COUNT(*) as total FROM citas WHERE fecha BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)",
            'citasCompletadas' => "SELECT COUNT(*) as total FROM citas WHERE fecha < CURDATE()"
        ];

        $stats = [];
        foreach ($queries as $key => $sql) {
            $result = $this->conexion->query($sql);
            $stats[$key] = $result->fetch_assoc()['total'];
        }
        
        return $stats;
    }

    private function getStyles()
    {
        return '<style>
        :root {
            --primary-color: #4a235a;
            --accent-color: #9370DB;
            --sidebar-width: 260px;
            --transition: all 0.3s ease;
        }
        
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #4a235a 0%, #3f1e5f 100%);
            color: white;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            padding-top: 9rem;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            z-index: 50;
            transition: var(--transition);
            overflow-y: auto;
        }
        
        .sidebar-user {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .avatar {
            width: 45px;
            height: 45px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .user-info h4 {
            font-size: 1rem;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 170px;
        }
        
        .user-status {
            font-size: 0.8rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .user-status i { color: #4CD964; font-size: 0.6rem; }
        
        .sidebar-menu {
            padding: 1.5rem 0;
            flex: 1;
        }
        
        .sidebar-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }
        
        .sidebar-menu li a {
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: var(--transition);
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu li a:hover, .sidebar-menu li.active a {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--accent-color);
        }
        
        .sidebar-stats {
            padding: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-stats h3 {
            font-size: 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .stat-item {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 0.8rem;
            text-align: center;
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }
        
        .stat-label {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        .sidebar-footer {
            padding: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .sidebar-footer a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: var(--transition);
        }
        
        .sidebar-footer a:hover { color: white; }
        
        .sidebar.collapsed {
            width: 70px;
            overflow: hidden;
        }
        
        .sidebar.collapsed .user-info,
        .sidebar.collapsed .user-status,
        .sidebar.collapsed .menu-text,
        .sidebar.collapsed .stats-title,
        .sidebar.collapsed .footer-text,
        .sidebar.collapsed .stat-label {
            display: none;
        }
        
        .sidebar.collapsed .sidebar-user,
        .sidebar.collapsed .sidebar-stats,
        .sidebar.collapsed .sidebar-footer {
            padding: 1rem 0.5rem;
            justify-content: center;
        }
        
        .sidebar.collapsed .sidebar-menu li a {
            justify-content: center;
            padding: 0.8rem;
        }
        
        .sidebar.collapsed .avatar { margin: 0 auto; }
        .sidebar.collapsed .stat-item { padding: 0.7rem; }
        
        .main-content {
            transition: var(--transition);
            margin-left: var(--sidebar-width);
        }
        
        .main-content.expanded { margin-left: 70px; }
        
        .estadistic{
            color: white;
            margin-bottom: 10vh;
        }

        .estadistic:hover{
        color: #e9d5ff;
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                overflow: hidden;
            }
            
            .sidebar-user, .sidebar-stats, .sidebar-footer {
                padding: 1rem 0.5rem;
                justify-content: center;
            }
            
            .user-info, .user-status, .stat-item, .sidebar-stats h3, .sidebar-footer a span {
                display: none;
            }
            
            .sidebar-menu li a {
                justify-content: center;
                padding: 0.8rem;
            }
            
            .avatar { margin: 0 auto; }
            .main-content { margin-left: 70px; }
        }
        </style>';
    }

    private function getScript()
    {
        return '<script>
        class SidebarManager {
            constructor() {
                this.sidebar = document.getElementById("main-sidebar");
                this.mainContent = document.querySelector(".main-content");
                this.toggleBtn = document.getElementById("sidebar-toggle");
                this.header = document.querySelector("header");
                this.storageKey = "sidebarState";
                this.init();
            }
            
            init() {
                this.adjustSidebarPosition();
                this.handleResponsiveDisplay();
                
                if (this.toggleBtn) {
                    this.toggleBtn.addEventListener("click", () => this.toggleSidebar());
                }
                
                this.restoreSavedState();
                
                window.addEventListener("resize", () => {
                    this.adjustSidebarPosition();
                    this.handleResponsiveDisplay();
                });
                
                if (this.header) this.observeHeaderChanges();
            }
            
            adjustSidebarPosition() {
                if (!this.header || !this.sidebar) return;
                
                const headerHeight = this.header.offsetHeight;
                this.sidebar.style.paddingTop = `${headerHeight}px`;
                
                if (this.toggleBtn) {
                    this.toggleBtn.style.top = `${headerHeight + 15}px`;
                }
            }
            
            handleResponsiveDisplay() {
                if (this.sidebar && window.innerWidth < 992) {
                    this.collapseSidebar();
                }
            }
            
            toggleSidebar() {
                if (!this.sidebar) return;
                
                this.sidebar.classList.toggle("collapsed");
                
                if (this.mainContent) {
                    this.mainContent.classList.toggle("expanded");
                }
                
                this.saveState();
            }
            
            collapseSidebar() {
                if (!this.sidebar) return;
                
                this.sidebar.classList.add("collapsed");
                if (this.mainContent) this.mainContent.classList.add("expanded");
                this.saveState();
            }
            
            expandSidebar() {
                if (!this.sidebar) return;
                
                this.sidebar.classList.remove("collapsed");
                if (this.mainContent) this.mainContent.classList.remove("expanded");
                this.saveState();
            }
            
            saveState() {
                if (!this.sidebar) return;
                
                const isCollapsed = this.sidebar.classList.contains("collapsed");
                localStorage.setItem(this.storageKey, JSON.stringify({
                    collapsed: isCollapsed,
                    timestamp: Date.now()
                }));
            }
            
            restoreSavedState() {
                if (!this.sidebar) return;
                
                try {
                    const savedState = localStorage.getItem(this.storageKey);
                    
                    if (savedState) {
                        const state = JSON.parse(savedState);
                        state.collapsed ? this.collapseSidebar() : this.expandSidebar();
                    }
                } catch (error) {
                    console.error("Error restoring sidebar state:", error);
                    localStorage.removeItem(this.storageKey);
                }
            }
            
            observeHeaderChanges() {
                const observer = new MutationObserver(() => this.adjustSidebarPosition());
                observer.observe(this.header, { 
                    attributes: true,
                    childList: true,
                    subtree: true
                });
            }
        }
        
        document.addEventListener("DOMContentLoaded", () => new SidebarManager());
        </script>';
    }

    public function render()
    {
        $stats = $this->obtenerEstadisticas();
        $currentPage = basename($_SERVER['PHP_SELF']);

        echo $this->getStyles();

        echo '
        <aside class="sidebar" id="main-sidebar">
            <div class="sidebar-user">
                <div class="avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="user-info">
                    <h4>' . htmlspecialchars($this->nombreUsuario) . '</h4>
                    <p class="user-status"><i class="bi bi-circle-fill"></i> En línea</p>
                </div>
            </div>
            <nav class="sidebar-menu">
                <ul>
                    <li' . ($currentPage == 'index.php' ? ' class="active"' : '') . '><a href="index.php"><i class="bi bi-speedometer2"></i> <span class="menu-text">Dashboard</span></a></li>
                    <li' . ($currentPage == 'read.php?openModal=crearCita' ? ' class="active"' : '') . '><a href="read.php?openModal=crearCita"><i class="bi bi-plus-circle"></i> <span class="menu-text">Nueva Cita</span></a></li>
                    <li' . ($currentPage == 'read.php' ? ' class="active"' : '') . '><a href="read.php"><i class="bi bi-calendar-week"></i> <span class="menu-text">Todas las Citas</span></a></li>
                    <li' . ($currentPage == 'correos.php' ? ' class="active"' : '') . '><a href="correos.php"><i class="bi bi-envelope-at"></i> <span class="menu-text">Genera un correo</span></a></li>
                </ul>
            </nav>
            
            <div class="sidebar-stats">
                <h3><i class="bi bi-graph-up"></i> <span class="stats-title">Estadísticas</span></h3> <a href="estadisticas.php" class="estadistic">Ver estadísticas</a>
                
                <div class="stat-item">
                    <div class="stat-value">' . $stats['totalCitas'] . '</div>
                    <div class="stat-label">Total de Citas</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-value">' . $stats['citasSemana'] . '</div>
                    <div class="stat-label">Esta semana</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-value">' . $stats['citasCompletadas'] . '</div>
                    <div class="stat-label">Completadas</div>
                </div>
            </div>
            
            <div class="sidebar-footer">
                <a href="../db/login-registro/logout.php"><i class="bi bi-box-arrow-right"></i> <span class="footer-text">Cerrar Sesión</span></a>
            </div>
        </aside>';

        echo $this->getScript();
    }
}
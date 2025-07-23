<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join to us - Acceso Seguro</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', Arial, sans-serif;
            height: 100vh;
            display: flex;
            background-image: url('styles-login/flores (1).png');
            overflow: hidden;
            position: relative;
        }

        /* Fondo animado con partículas */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(255,255,255,0.08) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(255,255,255,0.05) 0%, transparent 50%);
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        /* Botón volver al inicio */
        .back-button {
            position: absolute;
            top: 30px;
            left: 30px;
            z-index: 1000;
            background:rgba(156, 156, 156, 0.06);
            border: 1px solid rgba(0, 0, 0, 0.48);
            padding: 12px 20px;
            border-radius: 50px;
            color: rgba(0, 0, 0, 0.48);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-button:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
            text-decoration: none;
        }

        /* Sección izquierda con imagen de fondo */
        .left-section {
            width: 50%;
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            position: relative;
            overflow: hidden;
        }

        .left-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        /* Sección derecha con el formulario */
        .right-section {
            width: 50%;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(20px);
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            position: relative;
        }

        /* Contenedor del formulario */
        .login-container {
            width: 85%;
            max-width: 420px;
            text-align: center;
            background: rgba(255, 255, 255, 0.08);
            padding: 40px 35px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .login-header {
            margin-bottom: 35px;
        }

        .login-container h2 {
            font-size: 28px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #ffffff 0%, #e0e7ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .login-subtitle {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 400;
        }

        /* Grupos de input mejorados */
        .input-group {
            position: relative;
            margin-bottom: 25px;
            text-align: left;
        }

        .input-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.9);
        }

        .input-wrapper {
            position: relative;
        }

        .input-group input {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            outline: none;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 16px;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }

        .input-group input:focus {
            border-color: #a855f7;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 4px rgba(168, 85, 247, 0.1);
        }

        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        /* Iconos mejorados */
        .input-group i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .input-group input:focus + i {
            color: #a855f7;
        }

        /* Botón mejorado */
        .login-button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .login-button:hover::before {
            left: 100%;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(168, 85, 247, 0.3);
        }

        .login-button:active {
            transform: translateY(0);
        }

        /* Texto de registro mejorado */
        .register-text {
            margin-top: 25px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
        }

        .register-text a {
            color: #c4b5fd;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .register-text a:hover {
            color: #a855f7;
            text-decoration: underline;
        }

        /* Dropdown mejorado */
        .dropdown {
            margin-top: 25px;
            text-align: left;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .dropdown:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .dropdown-header {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .dropdown .arrow {
            font-size: 12px;
            color: #a855f7;
            transition: transform 0.3s ease;
        }

        .dropdown .arrow.rotated {
            transform: rotate(180deg);
        }

        .dropdown p {
            flex: 1;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.4;
        }

        .dropdown-content {
            display: none;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .dropdown-content.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .email-container {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.1);
            padding: 12px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .email {
            flex: 1;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.9);
            font-family: 'Courier New', monospace;
        }

        .copy-btn {
            padding: 8px 12px;
            background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }

        .copy-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(168, 85, 247, 0.3);
        }

        /* Efectos de error */
        .error-message {
            color: #fca5a5;
            font-size: 12px;
            margin-top: 5px;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .error-message.show {
            display: block;
            opacity: 1;
        }

        /* Animaciones de entrada */
        .login-container {
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            
            .left-section {
                display: none;
            }
            
            .right-section {
                width: 100%;
                height: 100vh;
            }
            
            .back-button {
                top: 20px;
                left: 20px;
                padding: 10px 16px;
                font-size: 13px;
            }
            
            .login-container {
                width: 90%;
                padding: 30px 25px;
            }
        }
    </style>
</head>
<body>
    <!-- Botón volver al inicio -->
    <a href="../../public_perspective/index.php" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Volver al Inicio
    </a>

    <div class="left-section"></div>
    
    <div class="right-section">
        <div class="login-container">
            <div class="login-header">
                <h2>Únete a Nosotros <i class="fas fa-user-plus"></i></h2>
                <p class="login-subtitle">Solicita acceso como invitado</p>
            </div>
            
            <form id="loginForm" action="login-guest.php" method="post">
                <div class="input-group">
                    <label for="name">Nombre Completo</label>
                    <div class="input-wrapper">
                        <input type="text" id="name" name="name" placeholder="Escribe tu nombre completo" required>
                        <i class="fas fa-user"></i>
                    </div>
                    <p class="error-message" id="name-error">Por favor ingresa un nombre válido</p>
                </div>
                
                <button type="submit" class="login-button">
                    Acceder como Invitado
                </button>
            </form>
            
            <p class="register-text">
                ¿Ya tienes una cuenta? 
                <a href="login.php">Inicia sesión aquí</a>
            </p>
            
            <div class="dropdown" id="dropdown-toggle">
                <div class="dropdown-header">
                    <div class="arrow" id="dropdown-arrow">▼</div>
                    <p>Si deseas unirte y tener una cuenta como empleado, comunícate al correo electrónico</p>
                </div>
                
                <div class="dropdown-content" id="dropdown-content">
                    <div class="email-container">
                        <div class="email">Acuproteam@gmail.com</div>
                        <button type="button" class="copy-btn" id="copyBtn">copiar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Validación del formulario
        const form = document.getElementById('loginForm');
        const nameInput = document.getElementById('name');
        const nameError = document.getElementById('name-error');

        // Función para validar nombre
        function validateName(name) {
            return name.trim().length >= 2 && /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(name.trim());
        }

        // Validación en tiempo real
        nameInput.addEventListener('blur', function() {
            if (!validateName(this.value) && this.value !== '') {
                nameError.classList.add('show');
                this.style.borderColor = '#fca5a5';
            } else {
                nameError.classList.remove('show');
                this.style.borderColor = 'rgba(255, 255, 255, 0.2)';
            }
        });

        // Validación al enviar
        form.addEventListener('submit', function(e) {
            if (!validateName(nameInput.value)) {
                nameError.classList.add('show');
                nameInput.style.borderColor = '#fca5a5';
                e.preventDefault();
            }
        });

        // Limpiar errores al escribir
        nameInput.addEventListener('input', function() {
            this.style.borderColor = 'rgba(255, 255, 255, 0.2)';
            nameError.classList.remove('show');
        });

        // Función para copiar el correo al portapapeles
        document.getElementById('copyBtn').addEventListener('click', function() {
            const emailText = document.querySelector('.email').textContent;
            navigator.clipboard.writeText(emailText)
                .then(() => {
                    this.textContent = 'copiado';
                    setTimeout(() => {
                        this.textContent = 'copiar';
                    }, 2000);
                })
                .catch(err => {
                    console.error('Error al copiar: ', err);
                    // Fallback para navegadores más antiguos
                    const textArea = document.createElement('textarea');
                    textArea.value = emailText;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    this.textContent = 'copiado';
                    setTimeout(() => {
                        this.textContent = 'copiar';
                    }, 2000);
                });
        });

        // Funcionalidad para el dropdown
        const dropdownToggle = document.getElementById('dropdown-toggle');
        const dropdownContent = document.getElementById('dropdown-content');
        const dropdownArrow = document.getElementById('dropdown-arrow');
        
        dropdownToggle.addEventListener('click', function() {
            dropdownContent.classList.toggle('active');
            dropdownArrow.classList.toggle('rotated');
            dropdownArrow.textContent = dropdownContent.classList.contains('active') ? '▲' : '▼';
        });
    </script>
</body>
</html>
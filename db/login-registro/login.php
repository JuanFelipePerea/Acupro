<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Acceso Seguro</title>
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
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 12px 20px;
            border-radius: 50px;
            color: white;
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

        /* Sección izquierda con el login */
        .left-section {
            width: 50%;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(20px);
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            position: relative;
        }

        /* Sección derecha con imagen de fondo */
        .right-section {
            width: 50%;
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            position: relative;
            overflow: hidden;
        }

        .right-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
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
            
            .left-section, .right-section {
                width: 100%;
                height: 100vh;
            }
            
            .right-section {
                display: none;
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
    <a href="#" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Volver al Inicio
    </a>

    <div class="left-section">
        <div class="login-container">
            <div class="login-header">
                <h2>Bienvenido</h2>
                <p class="login-subtitle">Accede a tu cuenta de forma segura</p>
            </div>
            
            <form id="loginForm" action="codigo_login.php" method="post">
                <div class="input-group">
                    <label for="email">Correo Electrónico</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" placeholder="nombre@ejemplo.com" required>
                        <i class="fas fa-envelope"></i>
                    </div>
                    <p class="error-message" id="email-error">Por favor ingresa un email válido</p>
                </div>
                
                <div class="input-group">
                    <label for="password">Contraseña</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" placeholder="Tu contraseña segura" required>
                        <i class="fas fa-lock"></i>
                    </div>
                    <p class="error-message" id="password-error">La contraseña es requerida</p>
                </div>
                
                <button type="submit" class="login-button">
                    Iniciar Sesión
                </button>
            </form>
            
            <p class="register-text">
                ¿No tienes una cuenta? 
                <a href="joinus.php">Solicita acceso aquí</a>
            </p>
        </div>
    </div>
    
    <div class="right-section"></div>

    <script>
        // Validación mejorada del formulario
        const form = document.getElementById('loginForm');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const emailError = document.getElementById('email-error');
        const passwordError = document.getElementById('password-error');

        // Función para validar email
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        // Validación en tiempo real
        emailInput.addEventListener('blur', function() {
            if (!validateEmail(this.value) && this.value !== '') {
                emailError.classList.add('show');
                this.style.borderColor = '#fca5a5';
            } else {
                emailError.classList.remove('show');
                this.style.borderColor = 'rgba(255, 255, 255, 0.2)';
            }
        });

        passwordInput.addEventListener('blur', function() {
            if (this.value.length < 1) {
                passwordError.classList.add('show');
                this.style.borderColor = '#fca5a5';
            } else {
                passwordError.classList.remove('show');
                this.style.borderColor = 'rgba(255, 255, 255, 0.2)';
            }
        });

        // Validación al enviar
        form.addEventListener('submit', function(e) {
            let hasErrors = false;

            if (!validateEmail(emailInput.value)) {
                emailError.classList.add('show');
                emailInput.style.borderColor = '#fca5a5';
                hasErrors = true;
            }

            if (passwordInput.value.length < 1) {
                passwordError.classList.add('show');
                passwordInput.style.borderColor = '#fca5a5';
                hasErrors = true;
            }

            if (hasErrors) {
                e.preventDefault();
            }
        });

        // Limpiar errores al escribir
        [emailInput, passwordInput].forEach(input => {
            input.addEventListener('input', function() {
                this.style.borderColor = 'rgba(255, 255, 255, 0.2)';
                const errorElement = document.getElementById(this.id + '-error');
                errorElement.classList.remove('show');
            });
        });
    </script>
</body>
</html>
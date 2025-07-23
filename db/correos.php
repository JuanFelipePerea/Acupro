<?php
require_once "layout.php";
require_once "PHPMailer/src/PHPMailer.php";
require_once "PHPMailer/src/SMTP.php";
require_once "PHPMailer/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 0);
error_reporting(E_ALL);

// Iniciar sesión con configuración segura
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => false, // Cambiar a true en producción con HTTPS
    'cookie_samesite' => 'Strict'
]);

// Clase para gestionar estudiantes
class EstudianteManager {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    
    public function obtenerTodos() {
        $sql = "SELECT cod_estudiante, CONCAT(nombres, ' ', apellidos) as nombre_completo 
                FROM estudiantes 
                WHERE correo_estudiante IS NOT NULL AND correo_estudiante != ''
                ORDER BY nombres";
        
        $result = mysqli_query($this->conexion, $sql);
        if (!$result) {
            error_log("Error en consulta obtenerTodos: " . mysqli_error($this->conexion));
            return [];
        }
        
        $estudiantes = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $estudiantes[] = $row;
        }
        return $estudiantes;
    }
    
    public function buscarPorNombre($nombre) {
        $nombreSanitizado = mysqli_real_escape_string($this->conexion, trim($nombre));
        
        $sql = "SELECT cod_estudiante, nombres, apellidos, correo_estudiante 
                FROM estudiantes 
                WHERE CONCAT(nombres, ' ', apellidos) LIKE '%$nombreSanitizado%' 
                AND correo_estudiante IS NOT NULL AND correo_estudiante != ''
                LIMIT 1";
        
        $result = mysqli_query($this->conexion, $sql);
        if (!$result) {
            error_log("Error en consulta buscarPorNombre: " . mysqli_error($this->conexion));
            return null;
        }
        
        return mysqli_fetch_assoc($result);
    }
    
    public function obtenerPorId($id) {
        $idSanitizado = (int) filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        
        $sql = "SELECT cod_estudiante, nombres, apellidos, correo_estudiante 
                FROM estudiantes 
                WHERE cod_estudiante = $idSanitizado
                AND correo_estudiante IS NOT NULL AND correo_estudiante != ''
                LIMIT 1";
        
        $result = mysqli_query($this->conexion, $sql);
        if (!$result) {
            error_log("Error en consulta obtenerPorId: " . mysqli_error($this->conexion));
            return null;
        }
        
        return mysqli_fetch_assoc($result);
    }
}

// Clase para gestionar plantillas de correo
class PlantillaManager {
    private $plantillasPredefinidas = [
        "Recordatorio de Cita" => [
            'name' => 'Recordatorio de Cita',
            'subject' => 'Recordatorio de Cita - ACUPRO',
            'body' => '<p>Estimado/a <strong>{NOMBRE}</strong>,</p>
                <p>Te recordamos que tienes una cita programada para el <strong>{FECHA}</strong> a las <strong>{HORA}</strong> en <strong>{LUGAR}</strong>.</p>
                <p>Por favor, confirma tu asistencia respondiendo este correo.</p>
                <p>Saludos cordiales,<br><strong>Equipo ACUPRO</strong></p>'
        ],
        "Confirmación de Cita" => [
            'name' => 'Confirmación de Cita',
            'subject' => 'Confirmación de Cita - ACUPRO',
            'body' => '<p>Estimado/a <strong>{NOMBRE}</strong>,</p>
                <p>Confirmamos tu cita programada para el <strong>{FECHA}</strong> a las <strong>{HORA}</strong> en <strong>{LUGAR}</strong>.</p>
                <p>Te recomendamos llegar con al menos 10 minutos de anticipación.</p>
                <p>Saludos cordiales,<br><strong>Equipo ACUPRO</strong></p>'
        ],
        "Seguimiento de Cita" => [
            'name' => 'Seguimiento de Cita',
            'subject' => 'Seguimiento de Cita - ACUPRO',
            'body' => '<p>Estimado/a <strong>{NOMBRE}</strong>,</p>
                <p>Esperamos que hayas tenido una buena experiencia en tu cita del <strong>{FECHA}</strong>.</p>
                <p>Nos gustaría conocer tu retroalimentación para seguir mejorando nuestros servicios.</p>
                <p>Saludos cordiales,<br><strong>Equipo ACUPRO</strong></p>'
        ]
    ];
    
    public function obtenerPlantilla($nombre) {
        return isset($this->plantillasPredefinidas[$nombre]) ? $this->plantillasPredefinidas[$nombre] : null;
    }
    
    public function procesarPlantilla($plantilla, $datos) {
        if (!$plantilla) return null;
        
        $subject = $plantilla['subject'];
        $body = $plantilla['body'];
        
        foreach ($datos as $key => $value) {
            $body = str_replace('{' . strtoupper($key) . '}', htmlspecialchars($value, ENT_QUOTES, 'UTF-8'), $body);
        }
        
        return ['subject' => $subject, 'body' => $body];
    }
}

// Clase para envío de correos
class MailManager {
    private $mail;
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->configurarSMTP();
    }
    
    private function configurarSMTP() {
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'jfpereacu.est@cosfacali.edu.co';
        $this->mail->Password = 'rekhywdvsdsfrkyl'; // Reemplazar con la contraseña de aplicación
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;
        $this->mail->CharSet = 'UTF-8';
    }
    
    public function enviarCorreo($destinatario, $nombreDestinatario, $asunto, $cuerpo) {
        try {
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            
            $this->mail->setFrom('jfpereacu.est@cosfacali.edu.co', 'Sistema ACUPRO');
            
            // Para pruebas: enviar siempre al mismo correo
            $this->mail->addAddress('jfpereacu.est@cosfacali.edu.co', $nombreDestinatario);
            
            // Para producción: descomentar la siguiente línea y comentar la anterior
            // $this->mail->addAddress($destinatario, $nombreDestinatario);
            
            $this->mail->Subject = $asunto;
            $this->mail->isHTML(true);
            $this->mail->Body = $cuerpo;
            
            $this->mail->send();
            return ['success' => true, 'message' => 'Correo enviado exitosamente'];
        } catch (Exception $e) {
            error_log("Error al enviar correo: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al enviar correo: ' . $e->getMessage()];
        }
    }
}

// Clase helper para formateo
class FormatHelper {
    public static function formatearFecha($fecha) {
        if (empty($fecha)) return '';
        
        try {
            $fechaObj = new DateTime($fecha);
            $dias = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
            $meses = ["enero", "febrero", "marzo", "abril", "mayo", "junio", 
                     "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
            
            $diaSemana = $dias[$fechaObj->format('w')];
            $dia = $fechaObj->format('d');
            $mes = $meses[$fechaObj->format('n') - 1];
            $anio = $fechaObj->format('Y');
            
            return "$diaSemana $dia de $mes de $anio";
        } catch (Exception $e) {
            error_log("Error al formatear fecha: " . $e->getMessage());
            return $fecha;
        }
    }
    
    public static function formatearHora($hora) {
        if (empty($hora)) return '';
        
        try {
            $horaPartes = explode(':', $hora);
            return sprintf("%02d:%02d", $horaPartes[0], $horaPartes[1]);
        } catch (Exception $e) {
            return $hora;
        }
    }
}

// Verificar conexión
if (!isset($conexion) || !$conexion) {
    die("Error: No se pudo establecer la conexión a la base de datos");
}

// Inicializar clases
$estudianteManager = new EstudianteManager($conexion);
$plantillaManager = new PlantillaManager();
$mailManager = new MailManager();

// Variables para el formulario
$mensaje = '';
$tipoMensaje = '';
$datos = [];
$previewMode = false;

// Generar token CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $mensaje = "Error de seguridad: Token inválido";
        $tipoMensaje = "danger";
    } else {
        // Obtener y validar datos
        $datos = [
            'estudiante_id' => filter_var($_POST['estudiante_id'] ?? '', FILTER_SANITIZE_NUMBER_INT),
            'estudiante_nombre' => trim($_POST['estudiante_nombre'] ?? ''),
            'plantilla' => trim($_POST['plantilla'] ?? ''),
            'fecha' => $_POST['fecha'] ?? '',
            'hora' => $_POST['hora'] ?? '',
            'lugar' => trim($_POST['lugar'] ?? '')
        ];
        
        // Validar campos requeridos
        if (empty($datos['estudiante_nombre']) || empty($datos['plantilla']) || 
            empty($datos['fecha']) || empty($datos['hora']) || empty($datos['lugar'])) {
            $mensaje = "Error: Todos los campos son obligatorios";
            $tipoMensaje = "danger";
        }
        // Validar formato de fecha
        elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $datos['fecha'])) {
            $mensaje = "Error: Formato de fecha inválido";
            $tipoMensaje = "danger";
        }
        // Validar formato de hora
        elseif (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $datos['hora'])) {
            $mensaje = "Error: Formato de hora inválido";
            $tipoMensaje = "danger";
        }
        else {
            // Obtener información del estudiante
            $infoEstudiante = empty($datos['estudiante_id']) ? 
                            $estudianteManager->buscarPorNombre($datos['estudiante_nombre']) : 
                            $estudianteManager->obtenerPorId($datos['estudiante_id']);
            
            if (!$infoEstudiante) {
                $mensaje = "Error: No se encontró información del estudiante o no tiene correo registrado";
                $tipoMensaje = "danger";
            } else {
                $nombreCompleto = trim($infoEstudiante['nombres'] . ' ' . $infoEstudiante['apellidos']);
                $correoEstudiante = $infoEstudiante['correo_estudiante'];
                
                // Formatear fecha y hora
                $fechaFormateada = FormatHelper::formatearFecha($datos['fecha']);
                $horaFormateada = FormatHelper::formatearHora($datos['hora']);
                
                // Obtener plantilla
                $plantilla = $plantillaManager->obtenerPlantilla($datos['plantilla']);
                
                if (!$plantilla) {
                    $mensaje = "Error: Plantilla no válida";
                    $tipoMensaje = "danger";
                } else {
                    // Procesar plantilla
                    $datosPlantilla = [
                        'nombre' => $nombreCompleto,
                        'fecha' => $fechaFormateada,
                        'hora' => $horaFormateada,
                        'lugar' => $datos['lugar']
                    ];
                    
                    $correoProcecsado = $plantillaManager->procesarPlantilla($plantilla, $datosPlantilla);
                    
                    // Modo preview
                    if (isset($_POST['preview'])) {
                        $previewMode = true;
                        $_SESSION['preview_data'] = [
                            'asunto' => $correoProcecsado['subject'],
                            'cuerpo' => $correoProcecsado['body'],
                            'destinatario' => $correoEstudiante,
                            'nombre' => $nombreCompleto
                        ];
                    } else {
                        // Enviar correo
                        $resultado = $mailManager->enviarCorreo(
                            $correoEstudiante, 
                            $nombreCompleto, 
                            $correoProcecsado['subject'], 
                            $correoProcecsado['body']
                        );
                        
                        if ($resultado['success']) {
                            $mensaje = "Correo enviado exitosamente a " . $nombreCompleto;
                            $tipoMensaje = "success";
                            $datos = []; // Limpiar formulario
                        } else {
                            $mensaje = $resultado['message'];
                            $tipoMensaje = "danger";
                        }
                    }
                }
            }
        }
    }
}

// Obtener lista de estudiantes
$estudiantes = $estudianteManager->obtenerTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Correos - ACUPRO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .main-content { padding: 2rem 0; }
        .form-container { background: white; border-radius: 15px; padding: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .title h1 { color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .title p { color: rgba(255,255,255,0.9); }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { font-weight: 600; color: #333; margin-bottom: 0.5rem; display: block; }
        .form-control { border-radius: 8px; border: 2px solid #e0e0e0; padding: 0.75rem; }
        .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); }
        .btn-custom { background: linear-gradient(45deg, #667eea, #764ba2); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; margin: 0.25rem; transition: all 0.3s; }
        .btn-custom:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); color: white; }
        .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal-content { background: white; border-radius: 15px; padding: 2rem; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto; }
        .preview-container { border: 1px solid #ddd; padding: 1rem; border-radius: 8px; background: #f9f9f9; margin: 1rem 0; }
        .modal-actions { display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem; }
    </style>
</head>
<body>
    <div class="container main-content">
        <div class="title text-center mb-4">
            <h1><strong>Sistema de Correos ACUPRO</strong></h1>
            <p>Envía correos personalizados a tus estudiantes</p>
        </div>

        <?php if(!empty($mensaje)): ?>
        <div class="alert alert-<?php echo $tipoMensaje; ?> alert-dismissible fade show" role="alert">
            <i class="bi <?php echo ($tipoMensaje == 'danger') ? 'bi-exclamation-circle-fill' : 'bi-check-circle-fill'; ?>"></i>
            <?php echo htmlspecialchars($mensaje); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="form-container">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-group">
                    <label for="estudiante_nombre"><i class="bi bi-person"></i> Estudiante:</label>
                    <select id="estudiante_nombre" name="estudiante_nombre" class="form-control" required>
                        <option value="">Seleccione un estudiante</option>
                        <?php foreach($estudiantes as $estudiante): ?>
                            <option value="<?php echo htmlspecialchars($estudiante['nombre_completo']); ?>" 
                                    data-id="<?php echo $estudiante['cod_estudiante']; ?>"
                                    <?php echo (isset($datos['estudiante_nombre']) && $datos['estudiante_nombre'] == $estudiante['nombre_completo']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($estudiante['nombre_completo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" id="estudiante_id" name="estudiante_id" value="<?php echo $datos['estudiante_id'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="plantilla"><i class="bi bi-file-text"></i> Plantilla de correo:</label>
                    <select id="plantilla" name="plantilla" class="form-control" required>
                        <option value="">Seleccione una plantilla</option>
                        <option value="Recordatorio de Cita" <?php echo (isset($datos['plantilla']) && $datos['plantilla'] == 'Recordatorio de Cita') ? 'selected' : ''; ?>>Recordatorio de Cita</option>
                        <option value="Confirmación de Cita" <?php echo (isset($datos['plantilla']) && $datos['plantilla'] == 'Confirmación de Cita') ? 'selected' : ''; ?>>Confirmación de Cita</option>
                        <option value="Seguimiento de Cita" <?php echo (isset($datos['plantilla']) && $datos['plantilla'] == 'Seguimiento de Cita') ? 'selected' : ''; ?>>Seguimiento de Cita</option>
                    </select>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha"><i class="bi bi-calendar"></i> Fecha de la cita:</label>
                            <input type="date" id="fecha" name="fecha" class="form-control" 
                                   value="<?php echo $datos['fecha'] ?? date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="hora"><i class="bi bi-clock"></i> Hora de la cita:</label>
                            <input type="time" id="hora" name="hora" class="form-control" 
                                   value="<?php echo $datos['hora'] ?? '09:00'; ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="lugar"><i class="bi bi-geo-alt"></i> Lugar de la cita:</label>
                    <input type="text" id="lugar" name="lugar" class="form-control" 
                           placeholder="Ej: Sala de Asesorías" 
                           value="<?php echo htmlspecialchars($datos['lugar'] ?? ''); ?>" required>
                </div>
                
                <div class="text-center">
                    <button type="submit" name="preview" value="1" class="btn btn-custom">
                        <i class="bi bi-eye"></i> Previsualizar
                    </button>
                    <button type="submit" class="btn btn-custom">
                        <i class="bi bi-send"></i> Enviar Correo
                    </button>
                </div>
            </form>
        </div>
        
        <?php if($previewMode && isset($_SESSION['preview_data'])): ?>
        <div id="previewModal" class="modal" style="display: flex;">
            <div class="modal-content">
                <h3><i class="bi bi-envelope"></i> Vista Previa del Correo</h3>
                <div class="preview-container">
                    <p><strong>Para:</strong> <?php echo htmlspecialchars($_SESSION['preview_data']['nombre']); ?> 
                       &lt;<?php echo htmlspecialchars($_SESSION['preview_data']['destinatario']); ?>&gt;</p>
                    <p><strong>Asunto:</strong> <?php echo htmlspecialchars($_SESSION['preview_data']['asunto']); ?></p>
                    <hr>
                    <div><?php echo $_SESSION['preview_data']['cuerpo']; ?></div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('previewModal').style.display='none'">
                        <i class="bi bi-x-circle"></i> Cerrar
                    </button>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <?php foreach($datos as $key => $value): ?>
                        <input type="hidden" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars($value); ?>">
                        <?php endforeach; ?>
                        <button type="submit" class="btn btn-custom">
                            <i class="bi bi-send"></i> Enviar Correo
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['preview_data']); endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Actualizar campo hidden con ID del estudiante
        document.getElementById('estudiante_nombre').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const estudianteId = selectedOption.getAttribute('data-id') || '';
            document.getElementById('estudiante_id').value = estudianteId;
        });
        
        // Cerrar modal al hacer clic fuera
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('previewModal');
            if (modal && e.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</body>
</html>
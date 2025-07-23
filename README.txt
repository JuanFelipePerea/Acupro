Presentado por la isntitución educativa COSFA
Proyecto AcuPro realizado con tecnologías php, html, mysql, css.

Grupo de proyecto:
Samuel David Orejuela García
Juan Felipe Perea Cuaran
Ángel Tomás Amaya Correa

<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>


Reseña de Acupro


(Presentación):El sitio web de presentación de ACUPRO, donde se introduce la plataforma como una solución integral para la organización y gestión de citas psicológicas dirigida específicamente a estudiantes de instituciones educativas, mostrando las características principales del sistema, sus beneficios y funcionalidades que ofrece para optimizar el servicio de psicología escolar.


(Login): Esta página constituye el portal de autenticación del sistema, donde los usuarios registrados (psicólogos, administradores o personal autorizado) pueden ingresar sus credenciales para acceder a todas las funcionalidades completas de la plataforma ACUPRO y gestionar las citas psicológicas de manera segura.


(Acceso Invitado): La sexta página ofrece una opción de ingreso como invitado que permite a usuarios no registrados acceder temporalmente al sistema con funcionalidades limitadas, ideal para demostraciones, consultas rápidas o acceso ocasional sin comprometer la seguridad del sistema principal.


(Dashboard/Index): El dashboard principal presenta una vista general del sistema mediante tarjetas informativas que muestran un resumen organizado de las citas programadas, ofreciendo una visión rápida del estado actual de las citas psicológicas y permitiendo una navegación intuitiva hacia las diferentes secciones del sistema.


(Gestión de Citas): Esta sección constituye el núcleo operativo del sistema, donde se centraliza toda la gestión de citas psicológicas incluyendo la creación de nuevos appointments, visualización detallada de citas existentes, edición de información cuando sea necesario y eliminación de citas, proporcionando un control completo sobre la agenda psicológica.


(Historial): El módulo de historial mantiene un archivo completo de todas las citas psicológicas que han pasado respecto al día actual, permitiendo llevar un registro histórico detallado del proceso terapéutico de cada estudiante para facilitar el seguimiento longitudinal y la continuidad del tratamiento psicológico.


(Calendario): La vista de calendario presenta una interfaz visual e interactiva donde las citas se muestran organizadas por fechas, permitiendo no solo visualizar la programación de manera cronológica sino también crear nuevas citas directamente desde el calendario, así como editar, ver detalles y eliminar citas existentes de forma intuitiva.


(Estudiantes y Acudientes): Este módulo de administración de usuarios proporciona un sistema completo de gestión de información personal que incluye el registro de nuevos estudiantes, mantenimiento de datos de acudientes o padres de familia, y funcionalidades CRUD completas (crear, leer, actualizar y eliminar) para mantener actualizada la base de datos de usuarios.


(Sistema de Correos): La herramienta de comunicación integrada permite visualizar y gestionar plantillas predefinidas de correos electrónicos, así como enviar notificaciones automatizadas y comunicaciones directas a estudiantes y acudientes, facilitando la coordinación y seguimiento de las citas psicológicas a través de canales digitales eficientes.


(Perfil de Usuario): Esta sección personal permite a cada usuario del sistema (psicólogos y administradores) gestionar y actualizar su información personal, configuraciones de cuenta y preferencias del sistema, proporcionando un espacio personalizado para la administración de su perfil profesional dentro de la plataforma.


(Estadísticas): El dashboard analítico presenta métricas detalladas y estadísticas comprehensivas sobre el rendimiento del servicio psicológico, incluyendo datos segmentados por grados académicos, número de estudiantes atendidos, frecuencia de citas y otros indicadores clave que permiten evaluar la efectividad y alcance del programa de apoyo psicológico institucional.




---------------------------------------------------- 1. Arquitectura general del proyecto --------------------------------------------------------------------

Nuestro proyecto sigue una estructura modular en PHP, con:

- Separación de roles o vistas (e.g., login, modal_invitado, public_perspective)
- Uso de modales para CRUD de citas (m_crear_cita.php, m_editar_cita.php, etc.)
- Gestión de sesiones y autenticación (login.php, sessionCheck.php)
- Archivos separados por funcionalidades específicas: calendario, estadísticas, perfil, historial, etc.

---------------------------------------------------- 2. Carpetas clave y lo que indican --------------------------------------------------------------------

Carpeta / Archivo	Función inferida
login-registro/		Todo lo relacionado con autenticación de usuarios.
modales/		Interfaces emergentes para acciones rápidas sobre citas.
phpmailer/		Envío de correos automáticos (notificaciones, confirmaciones).
public_perspective/	Vistas o interfaz pública (probablemente para invitados o externos).
db/			Conexión a base de datos y archivos auxiliares tipo conexion.php.

---------------------------------------------------- 3. Comunicación entre archivos --------------------------------------------------------------------

Esto es lo que tenemos del flujo de comunicación:

Inicio de sesión / Registro:

- Usuario entra por login.php o joinus.php.
- Validación de errores en login_errors.php.
- Se crea la sesión y se redirige al dashboard o index.php.

Dashboard / Inicio:

- index.php carga partes con include, probablemente de header.php, sidebar.php, footer.php.
- Dependiendo del rol (psicóloga, secretaria, invitado) se carga diferente contenido (modal_invitado.php, modales_perfil.php, etc.).
- Se importa el modal_invitado para las pestañas, que identifica la calidad del usuario para prohibirlo o permitirlo

Modales para acciones:

Modales como m_crear_cita.php permiten hacer acciones sin recargar página (Igual para reutilizarlos y mantener la coherencia, también para evitar rebundancia en ello).
Se comunican con funciones de functions.php o funcitas.php (lo mismo, evitar rebundancia).

Base de datos:

Todas las operaciones van conectadas a través de conexion.php.

Estadísticas / Citas / Usuarios:

Archivos como estadisticas.php, historial.php y estudiantes.php muestran tablas y dashboards.
Se apoyan de sus .css y posiblemente peticiones internas o modales.

Correo:
mailer_test.php y correos.php usan phpmailer para enviar notificaciones, probablemente al crear o editar citas.

---------------------------------------------------- 4. Frontend --------------------------------------------------------------------

CSS organizado por página (config_perfil.css, sidebar.css, modal_invitado.css, etc.)
Uso de modales para UX rápida.
script.js probablemente maneja los eventos y validaciones.

---------------------------------------------------- 5. Observaciones retrospectivas --------------------------------------------------------------------

Hay redundancia: functions.php y funcitas.php suenan a lo mismo, podrías unificarlos.
Podrías mover los CSS a una carpeta central tipo assets/css.
¿Hay separación por roles? Quizá podrías tener carpetas por usuario: secretaria/, psicologa/, invitado/.
No parece haber un MVC real. Todo está muy junto en PHP plano, aunque modular.
Este proyecto está pensado como un sistema de agendamiento de citas, enfocado en un contexto educativo con psicología y gestión por parte de una secretaria. Tiene:

Control de usuarios
Roles diferenciados
Panel de estadísticas
Registro de acciones e historial
Una posible expansión a red de doctores
Plantillas de correos automáticos




<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

-------------------------------------------------  N O T A S   D E   D E S A R R O L L A D O R E S  ----------------------------------------

>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>



La estructura de carpetas inicia en el index.php de la raiz, que junto al router y el .htaccess redirigen a la ruta /public_perspective/
index.php, que incluye una primera vista del concepto general de AcuPro;

Al llegar al login se inicia un sistema conectado a la tabla usuarios de la BD (Base de datos, hecha con xampp y lampp, al fin y al cabo
son lo mismo. La base de datos se llama acupro) y valida los correos y contraseñas encriptadas, en el joinus.php se anexa una cuenta para 
invitados que tiene el sistema de modales mencionado anteriormente, por lo que tendrá funciones limitadas en comparación con las funciones
de la cuenta de administrador

Cabe recalcar que, para iniciar correctamente, hay varios registros ya hechos, las credenciales que generalmente usamos son

Usuario: USER
Correo: 12345@gmail.com
Contraseña: 12345

Al entrar se puede apreciar el dashboard, un dashboard que muestra tarjetas con las citas, similares al tablero que nuestra turora nos mostró 
de referencia.

Puntos a tener en cuenta:

1. El sidebar se incluye con POO
2. Algunos archivos, por colisión, tienen conexiones propias por PDO o mysqli para que sean sostenibles por su cuenta
3. En algunos momentos los css colisionan, como al acceder al historial con el header, esto se solucionaría con clases más específicas en el css
y boostrap, pero lo obviamos por ahora para priorizar la lógica de phpmailer y la funcionalidad
4. Puede que la estética no agrade a todos, pero está hecho de la mano de la tutora y a su gusto.
 
Para servicio técnico de los desarrolladores, o contacto a los mismos, contactar al correo:

pipecuaran09@gmail.com

NO cuenta con una API, NO cuenta con una division entre backend y frontend, resalto, es el primer proyecto desarrollado a una escala medianamente 
considerable. Este proyecto NO sera sostenible (al menos en la version main) a largo plazo, tiene muchos errores y fallas, que esperamos nos ayuden a mejorar en proximos trabajos
Soy Juan Felipe Perea
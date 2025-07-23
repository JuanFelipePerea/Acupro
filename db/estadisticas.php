<?php
// Estadisticas.php
include_once "layout.php";
$conexion->set_charset("utf8mb4");

// Función helper para ejecutar consultas
function ejecutarConsulta($conexion, $query) {
    $result = $conexion->query($query);
    return $result ? $result : false;
}

// 1. Estadísticas generales (en una sola consulta)
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM citas) as total_citas,
    (SELECT COUNT(*) FROM estudiantes) as total_estudiantes,
    (SELECT COUNT(*) FROM acudientes) as total_acudientes,
    (SELECT COUNT(*) FROM citas WHERE MONTH(fecha) = MONTH(NOW()) AND YEAR(fecha) = YEAR(NOW())) as citas_mes_actual";
$stats = ejecutarConsulta($conexion, $stats_query)->fetch_assoc();

// 2. Citas por grado
$grados_data = [];
$result = ejecutarConsulta($conexion, "SELECT e.n_grado, COUNT(*) as total FROM citas c JOIN estudiantes e ON c.cod_estudiante = e.cod_estudiante WHERE c.cod_estudiante IS NOT NULL GROUP BY e.n_grado ORDER BY e.n_grado");
while($row = $result->fetch_assoc()) {
    $grados_data['labels'][] = $row['n_grado'];
    $grados_data['data'][] = (int)$row['total'];
}

// 3. Tendencia semanal (últimas 8 semanas)
$semanas_data = [];
$semanas_query = "SELECT 
    CONCAT('Sem ', WEEK(fecha, 1) - WEEK(DATE_SUB(fecha, INTERVAL DAYOFMONTH(fecha)-1 DAY), 1) + 1, '/', YEAR(fecha)) as semana_label,
    YEARWEEK(fecha, 1) as semana_num,
    COUNT(*) as total 
    FROM citas 
    WHERE fecha >= DATE_SUB(NOW(), INTERVAL 8 WEEK)
    GROUP BY YEARWEEK(fecha, 1) 
    ORDER BY semana_num";
$result = ejecutarConsulta($conexion, $semanas_query);
while($row = $result->fetch_assoc()) {
    $semanas_data['labels'][] = $row['semana_label'];
    $semanas_data['data'][] = (int)$row['total'];
}

// 4. Motivos más frecuentes
$motivos_data = [];
$result = ejecutarConsulta($conexion, "SELECT motivo, COUNT(*) as total FROM citas GROUP BY motivo ORDER BY total DESC LIMIT 6");
while($row = $result->fetch_assoc()) {
    $motivos_data['labels'][] = $row['motivo'];
    $motivos_data['data'][] = (int)$row['total'];
}

// 5. Tipos de cita
$tipos_data = [];
$result = ejecutarConsulta($conexion, "SELECT tipo, COUNT(*) as total FROM citas GROUP BY tipo");
while($row = $result->fetch_assoc()) {
    $tipos_data['labels'][] = ucfirst($row['tipo']);
    $tipos_data['data'][] = (int)$row['total'];
}

// 6. Distribución por horas
$horas_data = [];
$result = ejecutarConsulta($conexion, "SELECT HOUR(hora_inicio) as hora, COUNT(*) as total FROM citas GROUP BY HOUR(hora_inicio) ORDER BY hora");
while($row = $result->fetch_assoc()) {
    $horas_data['labels'][] = $row['hora'] . ':00';
    $horas_data['data'][] = (int)$row['total'];
}

// 7. Top 5 estudiantes
$top_estudiantes = [];
$result = ejecutarConsulta($conexion, "SELECT CONCAT(e.nombres, ' ', e.apellidos) as nombre, e.n_grado, COUNT(*) as total FROM citas c JOIN estudiantes e ON c.cod_estudiante = e.cod_estudiante WHERE c.cod_estudiante IS NOT NULL GROUP BY c.cod_estudiante ORDER BY total DESC LIMIT 5");
while($row = $result->fetch_assoc()) {
    $top_estudiantes[] = $row;
}

$conexion->close();
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="estadisticas.css">

<main>
<div class="stats-container">
    <div class="stats-header">
        <h1 class="stats-title"><i class="fas fa-chart-line"></i> Estadísticas del Servicio</h1>
        <p class="stats-subtitle">Dashboard completo con métricas y análisis del servicio psicológico</p>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
        <?php 
        $cards = [
            ['total_citas', 'Total Citas', 'calendar-check'],
            ['total_estudiantes', 'Estudiantes', 'users'],
            ['total_acudientes', 'Acudientes', 'user-friends'],
            ['citas_mes_actual', 'Citas Este Mes', 'calendar-alt']
        ];
        foreach($cards as $card): ?>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="stat-number"><?php echo $stats[$card[0]]; ?></div>
                            <div class="stat-label"><?php echo $card[1]; ?></div>
                        </div>
                        <div class="col-4 text-end">
                            <i class="fas fa-<?php echo $card[2]; ?> icon-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Gráficos principales -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Citas por Grado</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="citasPorGrado"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Tendencia Semanal</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="citasPorSemana"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Motivos de Consulta</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="motivosConsulta"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-pie-chart me-2"></i>Tipos de Cita</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="tiposCita"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Distribución por Hora</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="citasPorHora"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <div class="card top-students-table">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top 5 Estudiantes</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr><th>Pos</th><th>Estudiante</th><th>Grado</th><th>Citas</th></tr>
                            </thead>
                            <tbody>
                                <?php 
                                $badges = ['primary', 'success', 'warning', 'info', 'secondary'];
                                foreach($top_estudiantes as $i => $est): ?>
                                <tr>
                                    <td><span class="badge bg-<?php echo $badges[$i]; ?>">#<?php echo $i+1; ?></span></td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($est['nombre']); ?></td>
                                    <td><?php echo $est['n_grado']; ?></td>
                                    <td><span class="badge bg-light text-dark"><?php echo $est['total']; ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<script>
const colors = ['#4a235a', '#6e3a8a', '#9370DB', '#8A2BE2', '#a78bfa', '#34d399', '#fb7185', '#60a5fa'];
Chart.defaults.font.family = "'Segoe UI', sans-serif";
Chart.defaults.color = '#6c757d';

// Configuración común para gráficos
const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } }
};

// 1. Citas por Grado
new Chart(document.getElementById('citasPorGrado'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($grados_data['labels'] ?? []); ?>,
        datasets: [{
            data: <?php echo json_encode($grados_data['data'] ?? []); ?>,
            backgroundColor: colors[0],
            borderRadius: 5
        }]
    },
    options: { ...commonOptions, scales: { y: { beginAtZero: true } } }
});

// 2. Tendencia Semanal
new Chart(document.getElementById('citasPorSemana'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($semanas_data['labels'] ?? []); ?>,
        datasets: [{
            data: <?php echo json_encode($semanas_data['data'] ?? []); ?>,
            borderColor: colors[2],
            backgroundColor: 'rgba(147, 112, 219, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 5
        }]
    },
    options: { ...commonOptions, scales: { y: { beginAtZero: true } } }
});

// 3. Motivos de Consulta
new Chart(document.getElementById('motivosConsulta'), {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($motivos_data['labels'] ?? []); ?>,
        datasets: [{
            data: <?php echo json_encode($motivos_data['data'] ?? []); ?>,
            backgroundColor: colors
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'right' } }
    }
});

// 4. Tipos de Cita
new Chart(document.getElementById('tiposCita'), {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($tipos_data['labels'] ?? []); ?>,
        datasets: [{
            data: <?php echo json_encode($tipos_data['data'] ?? []); ?>,
            backgroundColor: [colors[0], colors[2]]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } }
    }
});

// 5. Citas por Hora
new Chart(document.getElementById('citasPorHora'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($horas_data['labels'] ?? []); ?>,
        datasets: [{
            data: <?php echo json_encode($horas_data['data'] ?? []); ?>,
            backgroundColor: 'rgba(147, 112, 219, 0.6)',
            borderRadius: 5
        }]
    },
    options: { ...commonOptions, scales: { y: { beginAtZero: true } } }
});
</script>
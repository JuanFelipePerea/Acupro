<?php
require_once 'header.php';
require_once 'sidebar.php';
?>

<div class="error-container">
    <div class="error-content">
        <div class="error-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <h1 class="error-title">Error 404</h1>
        <h2 class="error-subtitle">Página no encontrada</h2>
        <p class="error-message">Lo sentimos, la página que estás buscando no existe o ha sido movida.</p>
        <div class="error-actions">
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home"></i> Volver al inicio
            </a>
            <a href="javascript:history.back()" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Regresar
            </a>
        </div>
    </div>
</div>

<style>
.error-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 180px);
    padding: 20px;
}

.error-content {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    padding: 40px;
    text-align: center;
    max-width: 600px;
    width: 100%;
}

.error-icon {
    font-size: 80px;
    color: #6b46c1;
    margin-bottom: 20px;
}

.error-title {
    font-size: 48px;
    color: #6b46c1;
    margin: 0 0 10px;
    font-weight: 700;
}

.error-subtitle {
    font-size: 24px;
    color: #555;
    margin: 0 0 20px;
    font-weight: 500;
}

.error-message {
    font-size: 16px;
    color: #666;
    margin-bottom: 30px;
}

.error-actions {
    display: flex;
    justify-content: center;
    gap: 15px;
}

.btn {
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: all 0.3s ease;
}

.btn i {
    margin-right: 8px;
}

.btn-primary {
    background-color: #6b46c1;
    color: white;
    border: none;
}

.btn-primary:hover {
    background-color: #5a32b0;
    transform: translateY(-2px);
}

.btn-outline {
    background-color: transparent;
    color: #6b46c1;
    border: 1px solid #6b46c1;
}

.btn-outline:hover {
    background-color: #f8f5ff;
    transform: translateY(-2px);
}
</style>

<?php
require_once 'footer.php';
?>
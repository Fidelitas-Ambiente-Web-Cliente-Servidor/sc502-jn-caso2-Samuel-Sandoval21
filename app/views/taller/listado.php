<?php
// ELIMINA esta línea: session_start();
// Solo verifica la sesión
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'usuario') {
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Listado Talleres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="public/js/jquery-4.0.0.min.js"></script>
    <script src="public/js/taller.js"></script>
</head>
<body class="container mt-5">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary rounded mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Sistema de Talleres</a>
            <div class="navbar-nav ms-auto">
                <span class="nav-link text-white">Bienvenido, <?= htmlspecialchars($_SESSION['user'] ?? 'Usuario') ?></span>
                <button id="btnLogout" class="btn btn-danger btn-sm ms-2">Cerrar sesión</button>
            </div>
        </div>
    </nav>
    
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="talleres-tab" data-bs-toggle="tab" data-bs-target="#talleres" type="button" role="tab">Talleres Disponibles</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="solicitudes-tab" data-bs-toggle="tab" data-bs-target="#solicitudes" type="button" role="tab">Mis Solicitudes</button>
        </li>
    </ul>
    
    <div class="tab-content mt-3" id="myTabContent">
        <div class="tab-pane fade show active" id="talleres" role="tabpanel">
            <div id="talleresList" class="row"></div>
        </div>
        <div class="tab-pane fade" id="solicitudes" role="tabpanel">
            <div id="solicitudesList"></div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
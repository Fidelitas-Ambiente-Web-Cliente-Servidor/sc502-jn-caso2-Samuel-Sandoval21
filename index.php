<?php
session_start();

require_once './app/controllers/UserController.php';
require_once './app/controllers/TallerController.php';
require_once './app/controllers/AdminController.php';
require_once './app/models/Taller.php';
require_once './app/models/Solicitud.php';
require_once './app/models/User.php';

$page = $_GET['page'] ?? 'login';

// ========== RUTAS GET OBTENER DATOS ==========
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // Obtener listado de talleres disponibles
    if ($_GET['option'] ?? "" == "talleres_json") {
        $taller = new TallerController();
        $taller->getTalleresJson();
        exit;
    }

    // Obtener solicitudes pendientes (para admin)
    if ($_GET['option'] ?? "" == "solicitudes_json") {
        $admin = new AdminController();
        $admin->getSolicitudesJson();
        exit;
    }
    
    // Obtener mis solicitudes (para usuario)
    if ($_GET['option'] ?? "" == "mis_solicitudes_json") {
        $taller = new TallerController();
        $taller->getMisSolicitudesJson();
        exit;
    }
}

// ========== RUTAS FORMULARIO POST ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_POST['option'] == "login") {
        $auth = new UserController();
        $auth->login();
        exit;
    }

    if ($_POST['option'] == "register") {
        $auth = new UserController();
        $auth->registro();
        exit;
    }

    if ($_POST['option'] == "logout") {
        $auth = new UserController();
        $auth->logout();
        exit;
    }

    if ($_POST['option'] == "solicitar") {
        $taller = new TallerController();
        $taller->solicitar();
        exit;
    }

    if ($_POST['option'] == "aprobar") {
        $admin = new AdminController();
        $admin->aprobar();
        exit;
    }

    if ($_POST['option'] == "rechazar") {
        $admin = new AdminController();
        $admin->rechazar();
        exit;
    }
}

// ========== RUTAS DE VISTAS ==========
switch ($page) {

    case "talleres":
        $taller = new TallerController();
        $taller->index();
        break;

    case "admin":
        $admin = new AdminController();
        $admin->solicitudes();
        break;

    case "logout":
        $auth = new UserController();
        $auth->logout();
        break;
        
    case "registro":
        $auth = new UserController();
        $auth->showRegistro();
        break;
        
    case "login":
    default:
        $auth = new UserController();
        $auth->showLogin();
        break;
}
?>
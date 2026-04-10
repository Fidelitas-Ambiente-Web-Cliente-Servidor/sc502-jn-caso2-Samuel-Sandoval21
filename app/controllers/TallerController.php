<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Taller.php';
require_once __DIR__ . '/../models/Solicitud.php';

class TallerController
{
    private $tallerModel;
    private $solicitudModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->connect();
        $this->tallerModel = new Taller($db);
        $this->solicitudModel = new Solicitud($db);
    }

    public function index()
    {
        if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'usuario') {
            header('Location: index.php?page=login');
            exit();
        }
        require __DIR__ . '/../views/taller/listado.php';
    }
    
    public function getTalleresJson()
    {
        if (!isset($_SESSION['id'])) {
            echo json_encode([]);
            return;
        }
        
        $talleres = $this->tallerModel->getAllDisponibles();
        header('Content-Type: application/json');
        echo json_encode($talleres);
    }
    
    public function getMisSolicitudesJson()
    {
        if (!isset($_SESSION['id'])) {
            echo json_encode([]);
            return;
        }
        
        $usuarioId = $_SESSION['id'];
        $solicitudes = $this->solicitudModel->obtenerPorUsuario($usuarioId);
        
        header('Content-Type: application/json');
        echo json_encode($solicitudes);
    }
    
    public function solicitar()
    {
        if (!isset($_SESSION['id'])) {
            echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
            return;
        }
        
        $tallerId = $_POST['taller_id'] ?? 0;
        $usuarioId = $_SESSION['id'];
        
        if ($this->solicitudModel->crear($usuarioId, $tallerId)) {
            echo json_encode(['success' => true, 'message' => 'Solicitud enviada correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ya has solicitado este taller']);
        }
    }
}
?>
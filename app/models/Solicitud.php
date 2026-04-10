<?php
require_once __DIR__ . '/Taller.php';

class Solicitud
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function crear($usuario_id, $taller_id)
    {
        if ($this->usuarioYaSolicito($usuario_id, $taller_id)) {
            return false;
        }
        
        $query = "INSERT INTO solicitudes (usuario_id, taller_id, estado) 
                  VALUES (?, ?, 'pendiente')";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $usuario_id, $taller_id);
        
        return $stmt->execute();
    }
    
    public function usuarioYaSolicito($usuario_id, $taller_id)
    {
        $query = "SELECT COUNT(*) as total FROM solicitudes 
                  WHERE usuario_id = ? AND taller_id = ? 
                  AND estado IN ('pendiente', 'aprobada')";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $usuario_id, $taller_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'] > 0;
    }
    
    public function obtenerPendientes()
    {
        $query = "SELECT s.*, u.username, t.nombre as taller_nombre, t.cupo_disponible 
                  FROM solicitudes s
                  JOIN usuarios u ON s.usuario_id = u.id
                  JOIN talleres t ON s.taller_id = t.id
                  WHERE s.estado = 'pendiente'
                  ORDER BY s.fecha_solicitud ASC";
        $result = $this->conn->query($query);
        $solicitudes = [];
        while ($row = $result->fetch_assoc()) {
            $solicitudes[] = $row;
        }
        return $solicitudes;
    }
    
    public function obtenerPorUsuario($usuario_id)
    {
        $query = "SELECT s.*, t.nombre as taller_nombre, s.estado, s.fecha_solicitud
                  FROM solicitudes s
                  JOIN talleres t ON s.taller_id = t.id
                  WHERE s.usuario_id = ?
                  ORDER BY s.fecha_solicitud DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $solicitudes = [];
        while ($row = $result->fetch_assoc()) {
            $solicitudes[] = $row;
        }
        return $solicitudes;
    }
    
    public function aprobar($solicitud_id)
    {
        $query = "SELECT taller_id FROM solicitudes WHERE id = ? AND estado = 'pendiente'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $solicitud_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $solicitud = $result->fetch_assoc();
        
        if (!$solicitud) {
            return false;
        }
        
        $this->conn->begin_transaction();
        
        try {
            $tallerModel = new Taller($this->conn);
            if ($tallerModel->descontarCupo($solicitud['taller_id'])) {
                $query = "UPDATE solicitudes SET estado = 'aprobada' WHERE id = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("i", $solicitud_id);
                $stmt->execute();
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollback();
                return false;
            }
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
    
    public function rechazar($solicitud_id)
    {
        $query = "UPDATE solicitudes SET estado = 'rechazada' 
                  WHERE id = ? AND estado = 'pendiente'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $solicitud_id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
}
?>
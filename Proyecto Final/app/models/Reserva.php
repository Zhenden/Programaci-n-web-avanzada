<?php
require_once __DIR__ . '/../config.php';
class Reserva
{
    // Tabla `reservas` (id, miembro_id, clase_id, fecha_hora)
    public static function all()
    {
        $sql = 'SELECT r.id, r.miembro_id, m.nombre AS miembro_nombre, r.clase_id, c.nombre AS clase_nombre, r.fecha_hora FROM reservas r LEFT JOIN miembros m ON r.miembro_id = m.id LEFT JOIN clases c ON r.clase_id = c.id ORDER BY r.fecha_hora';
        $stmt = getPDO()->query($sql);
        return $stmt->fetchAll();
    }

    public static function create($miembro_id, $clase_id, $fecha_hora)
    {
        $stmt = getPDO()->prepare('INSERT INTO reservas (miembro_id, clase_id, fecha_hora) VALUES (?,?,?)');
        return $stmt->execute([$miembro_id, $clase_id, $fecha_hora]);
    }

    public static function findByMember($miembro_id)
    {
        $stmt = getPDO()->prepare('SELECT r.*, c.nombre AS clase_nombre FROM reservas r JOIN clases c ON r.clase_id = c.id WHERE r.miembro_id = ? ORDER BY r.fecha_hora');
        $stmt->execute([$miembro_id]);
        return $stmt->fetchAll();
    }

    public static function delete($id)
    {
        $stmt = getPDO()->prepare('DELETE FROM reservas WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public static function find($id)
    {
        $stmt = getPDO()->prepare('SELECT * FROM reservas WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function findByClass($clase_id)
    {
        $sql = 'SELECT r.id, r.miembro_id, m.nombre AS miembro_nombre, m.correo AS miembro_correo, r.clase_id, r.fecha_hora FROM reservas r LEFT JOIN miembros m ON r.miembro_id = m.id WHERE r.clase_id = ? ORDER BY r.fecha_hora';
        $stmt = getPDO()->prepare($sql);
        $stmt->execute([$clase_id]);
        return $stmt->fetchAll();
    }
}

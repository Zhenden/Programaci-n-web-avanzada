<?php
require_once __DIR__ . '/../config.php';
class GymClass
{
    // Tabla `clases` (id, nombre, tipo, instructor_id, fecha_hora)
    public static function all()
    {
        $sql = 'SELECT c.id, c.nombre, c.tipo, c.instructor_id, c.fecha_hora, i.nombre AS instructor_nombre FROM clases c LEFT JOIN instructores i ON c.instructor_id = i.id ORDER BY c.fecha_hora';
        $stmt = getPDO()->query($sql);
        return $stmt->fetchAll();
    }

    public static function find($id)
    {
        $stmt = getPDO()->prepare('SELECT * FROM clases WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($data)
    {
        $stmt = getPDO()->prepare('INSERT INTO clases (nombre,tipo,instructor_id,fecha_hora) VALUES (?,?,?,?)');
        return $stmt->execute([$data['nombre'],$data['tipo'],$data['instructor_id'],$data['fecha_hora']]);
    }

    public static function update($id, $data)
    {
        $fields = [];
        $params = [];
        if (isset($data['nombre'])) { $fields[] = 'nombre = ?'; $params[] = $data['nombre']; }
        if (isset($data['tipo'])) { $fields[] = 'tipo = ?'; $params[] = $data['tipo']; }
        if (isset($data['instructor_id'])) { $fields[] = 'instructor_id = ?'; $params[] = $data['instructor_id']; }
        if (isset($data['fecha_hora'])) { $fields[] = 'fecha_hora = ?'; $params[] = $data['fecha_hora']; }
        if (empty($fields)) return false;
        $params[] = $id;
        $sql = 'UPDATE clases SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = getPDO()->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete($id)
    {
        $stmt = getPDO()->prepare('DELETE FROM clases WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public static function findByInstructor($instructor_id)
    {
        $stmt = getPDO()->prepare('SELECT * FROM clases WHERE instructor_id = ? ORDER BY fecha_hora');
        $stmt->execute([$instructor_id]);
        return $stmt->fetchAll();
    }
}

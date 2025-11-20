<?php
require_once __DIR__ . '/../config.php';
class Facility
{
    // Tabla `instalaciones` (id, nombre, tipo, capacidad)
    public static function all()
    {
        $stmt = getPDO()->query('SELECT id,nombre,tipo,capacidad FROM instalaciones');
        return $stmt->fetchAll();
    }

    public static function find($id)
    {
        $stmt = getPDO()->prepare('SELECT * FROM instalaciones WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($data)
    {
        $stmt = getPDO()->prepare('INSERT INTO instalaciones (nombre,tipo,capacidad) VALUES (?,?,?)');
        return $stmt->execute([$data['nombre'],$data['tipo'],$data['capacidad']]);
    }

    public static function update($id, $data)
    {
        $fields = [];
        $params = [];
        if (isset($data['nombre'])) { $fields[] = 'nombre = ?'; $params[] = $data['nombre']; }
        if (isset($data['tipo'])) { $fields[] = 'tipo = ?'; $params[] = $data['tipo']; }
        if (isset($data['capacidad'])) { $fields[] = 'capacidad = ?'; $params[] = $data['capacidad']; }
        if (empty($fields)) return false;
        $params[] = $id;
        $sql = 'UPDATE instalaciones SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = getPDO()->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete($id)
    {
        $stmt = getPDO()->prepare('DELETE FROM instalaciones WHERE id = ?');
        return $stmt->execute([$id]);
    }
}

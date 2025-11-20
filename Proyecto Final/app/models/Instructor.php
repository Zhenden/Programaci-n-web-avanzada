<?php
require_once __DIR__ . '/../config.php';
class Instructor
{
    // Tabla `instructores` (id, nombre, correo, contraseña, especialidad)
    public static function all()
    {
        $stmt = getPDO()->query('SELECT id,nombre,correo,especialidad FROM instructores');
        return $stmt->fetchAll();
    }

    public static function find($id)
    {
        $stmt = getPDO()->prepare('SELECT * FROM instructores WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function findByEmail($email)
    {
        $stmt = getPDO()->prepare('SELECT * FROM instructores WHERE correo = ?');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function create($data)
    {
        $stmt = getPDO()->prepare('INSERT INTO instructores (nombre,correo,contraseña,especialidad) VALUES (?,?,?,?)');
        $pass = password_hash($data['password'], PASSWORD_DEFAULT);
        return $stmt->execute([$data['nombre'],$data['correo'],$pass,$data['especialidad']]);
    }

    public static function update($id, $data)
    {
        $fields = [];
        $params = [];
        if (isset($data['nombre'])) { $fields[] = 'nombre = ?'; $params[] = $data['nombre']; }
        if (isset($data['correo'])) { $fields[] = 'correo = ?'; $params[] = $data['correo']; }
        if (isset($data['especialidad'])) { $fields[] = 'especialidad = ?'; $params[] = $data['especialidad']; }
        if (isset($data['password']) && $data['password'] !== '') { $fields[] = 'contraseña = ?'; $params[] = password_hash($data['password'], PASSWORD_DEFAULT); }
        if (empty($fields)) return false;
        $params[] = $id;
        $sql = 'UPDATE instructores SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = getPDO()->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete($id)
    {
        $stmt = getPDO()->prepare('DELETE FROM instructores WHERE id = ?');
        return $stmt->execute([$id]);
    }
}

<?php
require_once __DIR__ . '/../config.php';
class Member
{
    // Tabla `miembros` (id, nombre, correo, contraseña, fecha_nacimiento, género)
    public static function all()
    {
        $stmt = getPDO()->query('SELECT id,nombre,correo,fecha_nacimiento,`género` FROM miembros');
        return $stmt->fetchAll();
    }

    public static function find($id)
    {
        $stmt = getPDO()->prepare('SELECT * FROM miembros WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function findByEmail($email)
    {
        $stmt = getPDO()->prepare('SELECT * FROM miembros WHERE correo = ?');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function create($data)
    {
        $stmt = getPDO()->prepare('INSERT INTO miembros (nombre,correo,contraseña,fecha_nacimiento,`género`) VALUES (?,?,?,?,?)');
        $pass = password_hash($data['password'], PASSWORD_DEFAULT);
        return $stmt->execute([$data['nombre'],$data['correo'],$pass,$data['fecha_nacimiento'],$data['género']]);
    }

    public static function delete($id)
    {
        $stmt = getPDO()->prepare('DELETE FROM miembros WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public static function update($id, $data)
    {
        // Actualiza nombre, correo, fecha_nacimiento y género. Si pasa contraseña, la hashea.
        $fields = [];
        $params = [];
        if (isset($data['nombre'])) { $fields[] = 'nombre = ?'; $params[] = $data['nombre']; }
        if (isset($data['correo'])) { $fields[] = 'correo = ?'; $params[] = $data['correo']; }
        if (isset($data['fecha_nacimiento'])) { $fields[] = 'fecha_nacimiento = ?'; $params[] = $data['fecha_nacimiento']; }
        if (isset($data['género'])) { $fields[] = '`género` = ?'; $params[] = $data['género']; }
        if (isset($data['password']) && $data['password'] !== '') { $fields[] = 'contraseña = ?'; $params[] = password_hash($data['password'], PASSWORD_DEFAULT); }
        if (empty($fields)) return false;
        $params[] = $id;
        $sql = 'UPDATE miembros SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = getPDO()->prepare($sql);
        return $stmt->execute($params);
    }
}

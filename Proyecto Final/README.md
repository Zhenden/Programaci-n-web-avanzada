# Sistema de Gestión - Gimnasio

Estructura MVC básica en PHP (diseñada para ejecutarse en XAMPP).

Estructura principal creada en `Proyecto Final`.

Requisitos:
- XAMPP (Apache + MySQL)
- Importar la base de datos en `gimnasio_bd.sql` (ya se encuentra en la carpeta)

Pasos para levantar el proyecto (Windows, PowerShell):

1. Asegúrate de que Apache y MySQL estén corriendo en XAMPP.
2. Importa la base de datos desde `Proyecto Final/gimnasio_bd.sql` usando phpMyAdmin o la línea de comandos.

Usando `mysql` en PowerShell (ajusta usuario/contraseña si es necesario):

```powershell
cd "C:\xampp\mysql\bin"
./mysql.exe -u root < "C:\xampp\htdocs\Proyecto Final\gimnasio_bd.sql"
```

3. Abre en tu navegador: `http://localhost/Proyecto Final/` (o `http://localhost/Proyecto%20Final/`)

Credenciales demo:
- Admin: `admin` / `admin` (usuario demo no almacenado en la BD)
- Miembros/Instructores: usa el correo de un registro existente en la base de datos y su contraseña (las contraseñas en el volcado están hasheadas). Por ejemplo, si importas `gimnasio_bd.sql` habrá miembros e instructores de ejemplo.

Notas:
- Este es un esqueleto funcional. En producción debes usar tablas para usuarios, contraseñas hasheadas y validación.
- Todos los estilos están en `assets/css/style.css` (colores grises/azules/blancos).

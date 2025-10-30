<?php
// index.php - Contador de Visitas

// Nombre del archivo donde se almacenarán las visitas
$archivo_visitas = 'contador_visitas.txt';

// Iniciar sesión para evitar contar múltiples visitas de la misma sesión
session_start();

// Verificar si es una nueva visita (no ha visitado en esta sesión)
if (!isset($_SESSION['visitado'])) {
    
    // Verificar si el archivo existe, si no, crearlo con valor 0
    if (!file_exists($archivo_visitas)) {
        file_put_contents($archivo_visitas, '0');
    }
    
    // Leer el número actual de visitas
    $visitas = (int)file_get_contents($archivo_visitas);
    
    // Incrementar el contador
    $visitas++;
    
    // Guardar el nuevo número de visitas
    file_put_contents($archivo_visitas, $visitas);
    
    // Marcar que el usuario ya visitó en esta sesión
    $_SESSION['visitado'] = true;
    $_SESSION['primera_visita'] = true;
} else {
    // Leer el número actual de visitas sin incrementar
    $visitas = (int)file_get_contents($archivo_visitas);
    $_SESSION['primera_visita'] = false;
}

// Variable global para almacenar las visitas
$GLOBALS['numero_visitas'] = $visitas;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contador de Visitas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 50px;
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        .icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        h1 {
            font-size: 36px;
            color: #333;
            margin-bottom: 15px;
        }

        .subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 40px;
        }

        .counter-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .counter-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 18px;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .counter-number {
            color: white;
            font-size: 72px;
            font-weight: 700;
            text-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .info-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 600;
        }

        .info-value {
            color: #333;
            font-weight: 500;
        }

        .badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 15px;
        }

        .badge-new {
            background: #27ae60;
            color: white;
        }

        .badge-returning {
            background: #3498db;
            color: white;
        }

        .refresh-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .refresh-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">👥</div>
        <h1>Contador de Visitas</h1>
        <p class="subtitle">Seguimiento en tiempo real de visitantes</p>

        <div class="counter-box">
            <div class="counter-label">Total de Visitas</div>
            <div class="counter-number"><?php echo $GLOBALS['numero_visitas']; ?></div>
        </div>

        <div class="info-box">
            <div class="info-item">
                <span class="info-label">📅 Fecha:</span>
                <span class="info-value"><?php echo date('d/m/Y'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">🕐 Hora:</span>
                <span class="info-value"><?php echo date('H:i:s'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">🌐 IP del Visitante:</span>
                <span class="info-value"><?php echo $_SERVER['REMOTE_ADDR']; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">💻 Navegador:</span>
                <span class="info-value"><?php echo substr($_SERVER['HTTP_USER_AGENT'], 0, 50) . '...'; ?></span>
            </div>
        </div>

        <?php if ($_SESSION['primera_visita']): ?>
            <span class="badge badge-new">¡Nueva Visita Registrada!</span>
        <?php else: ?>
            <span class="badge badge-returning">🔄 Visita Actual (No Contada)</span>
        <?php endif; ?>

        <form method="post" style="display: inline;">
            <button type="submit" class="refresh-btn" onclick="window.location.reload()">
                🔄 Actualizar Contador
            </button>
        </form>

        <div class="footer">
            Desarrollado con PHP | Contador persistente con sesiones
        </div>
    </div>
</body>
</html>
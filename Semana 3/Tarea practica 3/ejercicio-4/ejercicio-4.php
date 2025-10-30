<?php
// index.php - Juego de Adivinanzas
session_start();

// Configuración del juego
$min = 1;
$max = 100;

// Inicializar el juego si es necesario
if (!isset($_SESSION['numero_secreto']) || isset($_POST['reiniciar'])) {
    $_SESSION['numero_secreto'] = rand($min, $max);
    $_SESSION['intentos'] = 0;
    $_SESSION['historial'] = [];
    $_SESSION['juego_ganado'] = false;
}

// Procesar el intento del usuario
$mensaje = '';
$tipo_mensaje = '';
$pista = '';

if (isset($_POST['adivinar']) && !$_SESSION['juego_ganado']) {
    $intento = (int)$_POST['numero'];
    $_SESSION['intentos']++;
    $_SESSION['historial'][] = $intento;
    
    if ($intento < $min || $intento > $max) {
        $mensaje = "Por favor, ingresa un número entre $min y $max";
        $tipo_mensaje = 'warning';
    } elseif ($intento < $_SESSION['numero_secreto']) {
        $diferencia = $_SESSION['numero_secreto'] - $intento;
        $mensaje = "¡Muy bajo! El número es mayor";
        $tipo_mensaje = 'error';
        
        if ($diferencia <= 5) {
            $pista = "¡Estás muy cerca! 🔥";
        } elseif ($diferencia <= 10) {
            $pista = "Estás cerca 🌡️";
        } else {
            $pista = "Estás lejos ❄️";
        }
    } elseif ($intento > $_SESSION['numero_secreto']) {
        $diferencia = $intento - $_SESSION['numero_secreto'];
        $mensaje = "¡Muy alto! El número es menor";
        $tipo_mensaje = 'error';
        
        if ($diferencia <= 5) {
            $pista = "¡Estás muy cerca! 🔥";
        } elseif ($diferencia <= 10) {
            $pista = "Estás cerca 🌡️";
        } else {
            $pista = "Estás lejos ❄️";
        }
    } else {
        $_SESSION['juego_ganado'] = true;
        $mensaje = "¡Felicitaciones! ¡Adivinaste el número {$_SESSION['numero_secreto']}!";
        $tipo_mensaje = 'success';
        
        if ($_SESSION['intentos'] == 1) {
            $pista = "¡Increíble! ¡Lo adivinaste en el primer intento! 🏆";
        } elseif ($_SESSION['intentos'] <= 5) {
            $pista = "¡Excelente! Lo lograste en solo {$_SESSION['intentos']} intentos 🌟";
        } else {
            $pista = "¡Bien hecho! Lo lograste en {$_SESSION['intentos']} intentos 👏";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego de Adivinanzas</title>
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

        .game-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }

        .game-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .game-icon {
            font-size: 80px;
            margin-bottom: 15px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        h1 {
            font-size: 36px;
            color: #333;
            margin-bottom: 10px;
        }

        .game-subtitle {
            color: #666;
            font-size: 16px;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            color: white;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
        }

        .message-box {
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: none;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .message-box.show {
            display: block;
        }

        .message-success {
            background: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }

        .message-error {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }

        .message-warning {
            background: #fff3cd;
            color: #856404;
            border: 2px solid #ffeeba;
        }

        .message-text {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .pista-text {
            font-size: 14px;
            margin-top: 8px;
        }

        .input-container {
            margin-bottom: 25px;
        }

        .input-label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .number-input {
            width: 100%;
            padding: 15px;
            border: 3px solid #e0e0e0;
            border-radius: 10px;
            font-size: 24px;
            text-align: center;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .number-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .button-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 15px;
        }

        .btn {
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #333;
            border: 2px solid #e0e0e0;
        }

        .btn-secondary:hover {
            background: #e9ecef;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        .historial-container {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #e0e0e0;
        }

        .historial-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .historial-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .historial-item {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 8px;
            font-weight: 600;
            color: #666;
        }

        .no-historial {
            color: #999;
            font-style: italic;
        }

        @media (max-width: 600px) {
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .button-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="game-container">
        <div class="game-header">
            <div class="game-icon">🎲</div>
            <h1>Juego de Adivinanzas</h1>
            <p class="game-subtitle">Adivina el número entre <?php echo $min; ?> y <?php echo $max; ?></p>
        </div>

        <div class="stats-container">
            <div class="stat-box">
                <div class="stat-label">Intentos</div>
                <div class="stat-value"><?php echo $_SESSION['intentos']; ?></div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Rango</div>
                <div class="stat-value"><?php echo $min; ?>-<?php echo $max; ?></div>
            </div>
        </div>

        <?php if ($mensaje): ?>
        <div class="message-box message-<?php echo $tipo_mensaje; ?> show">
            <div class="message-text"><?php echo $mensaje; ?></div>
            <?php if ($pista): ?>
                <div class="pista-text"><?php echo $pista; ?></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <form method="POST" id="gameForm">
            <div class="input-container">
                <label class="input-label" for="numero">Tu número:</label>
                <input 
                    type="number" 
                    id="numero" 
                    name="numero" 
                    class="number-input" 
                    min="<?php echo $min; ?>" 
                    max="<?php echo $max; ?>" 
                    required 
                    autofocus
                    <?php echo $_SESSION['juego_ganado'] ? 'disabled' : ''; ?>
                >
            </div>

            <div class="button-container">
                <button 
                    type="submit" 
                    name="adivinar" 
                    class="btn btn-primary"
                    <?php echo $_SESSION['juego_ganado'] ? 'disabled' : ''; ?>
                >
                    <?php echo $_SESSION['juego_ganado'] ? '✓ Juego Terminado' : '🎯 Adivinar'; ?>
                </button>
                <button type="submit" name="reiniciar" class="btn btn-secondary">
                    🔄 Nuevo
                </button>
            </div>
        </form>

        <?php if (!empty($_SESSION['historial'])): ?>
        <div class="historial-container">
            <div class="historial-title">📊 Historial de Intentos:</div>
            <div class="historial-list">
                <?php foreach ($_SESSION['historial'] as $intento): ?>
                    <div class="historial-item"><?php echo $intento; ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="historial-container">
            <div class="historial-title">📊 Historial de Intentos:</div>
            <div class="no-historial">Aún no has hecho ningún intento</div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Enfocar el input automáticamente
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('numero');
            if (!input.disabled) {
                input.focus();
            }
        });

        // Limpiar input después de cada intento
        const form = document.getElementById('gameForm');
        const juegoGanado = <?php echo $_SESSION['juego_ganado'] ? 'true' : 'false'; ?>;
        
        if (!juegoGanado) {
            form.addEventListener('submit', function(e) {
                if (e.submitter && e.submitter.name === 'adivinar') {
                    setTimeout(() => {
                        document.getElementById('numero').value = '';
                    }, 100);
                }
            });
        }

        // Validación en tiempo real
        const numeroInput = document.getElementById('numero');
        numeroInput.addEventListener('input', function() {
            const valor = parseInt(this.value);
            const min = parseInt(this.min);
            const max = parseInt(this.max);
            
            if (valor < min) {
                this.value = min;
            } else if (valor > max) {
                this.value = max;
            }
        });

        // Efecto de confeti cuando se gana
        <?php if ($_SESSION['juego_ganado'] && isset($_POST['adivinar'])): ?>
        setTimeout(() => {
            alert('🎉 ¡FELICITACIONES! 🎉\n\nAdivinaste el número en <?php echo $_SESSION['intentos']; ?> intentos!');
        }, 300);
        <?php endif; ?>
    </script>
</body>
</html>
<?php
// index.php - Galería de Imágenes

// Directorio donde se almacenan las imágenes
$directorio_imagenes = 'imagenes/';

// Crear el directorio si no existe
if (!file_exists($directorio_imagenes)) {
    mkdir($directorio_imagenes, 0777, true);
}

// Extensiones de archivo permitidas
$extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Función para obtener todas las imágenes del directorio
function obtenerImagenes($directorio, $extensiones) {
    $imagenes = [];
    
    if (is_dir($directorio)) {
        $archivos = scandir($directorio);
        
        foreach ($archivos as $archivo) {
            if ($archivo != '.' && $archivo != '..') {
                $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                
                if (in_array($extension, $extensiones)) {
                    $ruta_completa = $directorio . $archivo;
                    $imagenes[] = [
                        'nombre' => $archivo,
                        'ruta' => $ruta_completa,
                        'size' => filesize($ruta_completa),
                        'fecha' => filemtime($ruta_completa)
                    ];
                }
            }
        }
    }
    
    // Ordenar por fecha (más recientes primero)
    usort($imagenes, function($a, $b) {
        return $b['fecha'] - $a['fecha'];
    });
    
    return $imagenes;
}

// Obtener todas las imágenes
$imagenes = obtenerImagenes($directorio_imagenes, $extensiones_permitidas);

// Función para formatear tamaño de archivo
function formatearTamano($bytes) {
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

// Si no hay imágenes, crear algunas de ejemplo usando placeholders
$usar_placeholders = empty($imagenes);
if ($usar_placeholders) {
    // Usar imágenes estáticas de picsum.photos mediante IDs para que no cambien
    $imagenes_ejemplo = [
        ['titulo' => 'Paisaje de Montaña', 'url' => 'https://picsum.photos/id/1018/600/400'],
        ['titulo' => 'Playa Tropical', 'url' => 'https://picsum.photos/id/1015/600/400'],
        ['titulo' => 'Ciudad Nocturna', 'url' => 'https://picsum.photos/id/1003/600/400'],
        ['titulo' => 'Bosque Otoñal', 'url' => 'https://picsum.photos/id/1011/600/400'],
        ['titulo' => 'Desierto', 'url' => 'https://picsum.photos/id/1002/600/400'],
        ['titulo' => 'Cascada', 'url' => 'https://picsum.photos/id/1016/600/400'],
        ['titulo' => 'Aurora Boreal', 'url' => 'https://picsum.photos/id/1025/600/400'],
        ['titulo' => 'Valle Verde', 'url' => 'https://picsum.photos/id/1020/600/400'],
        ['titulo' => 'Lago Sereno', 'url' => 'https://picsum.photos/id/1019/600/400']
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería de Imágenes</title>
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
            padding: 40px 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 50px;
            color: white;
        }

        .header h1 {
            font-size: 48px;
            margin-bottom: 10px;
            text-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .header p {
            font-size: 18px;
            opacity: 0.9;
        }

        .stats {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
        }

        .stat-item {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            border-radius: 12px;
            color: white;
            font-weight: 600;
        }

        .stat-number {
            font-size: 32px;
            display: block;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .gallery-item {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .gallery-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .image-container {
            position: relative;
            width: 100%;
            height: 250px;
            overflow: hidden;
            background: #f0f0f0;
        }

        .gallery-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-item:hover .gallery-image {
            transform: scale(1.1);
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, transparent, rgba(0, 0, 0, 0.7));
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: flex-end;
            padding: 20px;
        }

        .gallery-item:hover .image-overlay {
            opacity: 1;
        }

        .overlay-text {
            color: white;
            font-size: 14px;
            font-weight: 600;
        }

        .image-info {
            padding: 20px;
        }

        .image-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .image-details {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #666;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .no-images {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .no-images-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        .no-images h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }

        .no-images p {
            color: #666;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .instructions {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: left;
            max-width: 500px;
            margin: 0 auto;
        }

        .instructions ol {
            margin-left: 20px;
        }

        .instructions li {
            margin-bottom: 8px;
            color: #555;
        }

        /* Modal para ver imagen completa */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal.show {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            max-width: 90%;
            max-height: 90%;
            position: relative;
            animation: zoomIn 0.3s ease;
        }

        @keyframes zoomIn {
            from { transform: scale(0.8); }
            to { transform: scale(1); }
        }

        .modal-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .modal-close {
            position: absolute;
            top: -40px;
            right: 0;
            color: white;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modal-close:hover {
            transform: rotate(90deg);
        }

        .modal-info {
            background: white;
            padding: 20px;
            border-radius: 0 0 8px 8px;
            margin-top: -5px;
        }

        @media (max-width: 768px) {
            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
            }

            .header h1 {
                font-size: 36px;
            }

            .stats {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>galería de Imágenes</h1>
            <p>Ejercicio 5</p>
            
            <div class="stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo $usar_placeholders ? count($imagenes_ejemplo) : count($imagenes); ?></span>
                    <span>Imágenes</span>
                </div>
                <?php if (!$usar_placeholders && !empty($imagenes)): ?>
                <div class="stat-item">
                    <span class="stat-number"><?php echo formatearTamano(array_sum(array_column($imagenes, 'size'))); ?></span>
                    <span>Tamaño Total</span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($usar_placeholders): ?>
            <!-- Galería con imágenes de ejemplo -->
            <div class="gallery-grid">
                <?php foreach ($imagenes_ejemplo as $index => $imagen): ?>
                    <div class="gallery-item" onclick="abrirModal('<?php echo $imagen['url']; ?>', '<?php echo $index + 1; ?>')">
                        <div class="image-container">
                            <img src="<?php echo $imagen['url']; ?>" alt="<?php echo $imagen['titulo']; ?>" class="gallery-image">
                            <div class="image-overlay">
                                <span class="overlay-text">Click para ampliar</span>
                            </div>
                        </div>
                        <div class="image-info">
                            <div class="image-title"><?php echo $index + 1; ?></div>
                            <div class="image-details">
                                <span class="detail-item"><?php echo $index + 1; ?></span>
                                <span class="detail-item">🔍 Ver más</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="no-images">
                <div class="no-images-icon">📁</div>
                <h2>Galería de Demostración</h2>
                <p>Estas son imágenes de ejemplo. Para usar tus propias imágenes:</p>
                <div class="instructions">
                    <ol>
                        <li>Crea una carpeta llamada <strong>"imagenes"</strong> en el mismo directorio que index.php</li>
                        <li>Coloca tus imágenes (JPG, PNG, GIF, WEBP) en esa carpeta</li>
                        <li>Recarga la página para ver tu galería personalizada</li>
                    </ol>
                </div>
            </div>

        <?php elseif (empty($imagenes)): ?>
            <!-- No hay imágenes -->
            <div class="no-images">
                <div class="no-images-icon">📷</div>
                <h2>No hay imágenes en la galería</h2>
                <p>Agrega imágenes a la carpeta "imagenes" para comenzar</p>
                <div class="instructions">
                    <ol>
                        <li>Crea una carpeta llamada <strong>"imagenes"</strong></li>
                        <li>Añade archivos de imagen (JPG, PNG, GIF, WEBP)</li>
                        <li>Recarga esta página</li>
                    </ol>
                </div>
            </div>

        <?php else: ?>
            <!-- Galería con imágenes del directorio -->
            <div class="gallery-grid">
                <?php foreach ($imagenes as $index => $imagen): ?>
                    <div class="gallery-item" onclick="abrirModal('<?php echo $imagen['ruta']; ?>', '<?php echo $index + 1; ?>')">
                        <div class="image-container">
                            <img src="<?php echo $imagen['ruta']; ?>" alt="<?php echo $index + 1; ?>" class="gallery-image">
                            <div class="image-overlay">
                                <span class="overlay-text">Click para ampliar</span>
                            </div>
                        </div>
                        <div class="image-info">
                            <div class="image-title"><?php echo $index + 1; ?></div>
                            <div class="image-details">
                                <span class="detail-item">📏 <?php echo formatearTamano($imagen['size']); ?></span>
                                <span class="detail-item">📅 <?php echo date('d/m/Y', $imagen['fecha']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal para ver imagen completa -->
    <div id="imageModal" class="modal" onclick="cerrarModal()">
        <div class="modal-content" onclick="event.stopPropagation()">
            <span class="modal-close" onclick="cerrarModal()">&times;</span>
            <img id="modalImage" class="modal-image" src="" alt="">
            <div class="modal-info">
                <h3 id="modalTitle"></h3>
            </div>
        </div>
    </div>

    <script>
        function abrirModal(ruta, titulo) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const modalTitle = document.getElementById('modalTitle');
            
            modalImage.src = ruta;
            modalTitle.textContent = titulo;
            modal.classList.add('show');
            
            document.body.style.overflow = 'hidden';
        }

        function cerrarModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        // Cerrar modal con tecla ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarModal();
            }
        });

        // Animación de entrada para las imágenes
        document.addEventListener('DOMContentLoaded', function() {
            const items = document.querySelectorAll('.gallery-item');
            items.forEach((item, index) => {
                setTimeout(() => {
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(20px)';
                    item.style.transition = 'all 0.5s ease';
                    
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'translateY(0)';
                    }, 50);
                }, index * 50);
            });
        });
    </script>
</body>
</html>
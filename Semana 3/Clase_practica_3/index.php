<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="diseño.css">
</head>
<body >
    <?php //crear un array con los productos y mostrarlos en una lista
        $producto1 = [
            "id" => "1",
            "nombre" => "Producto 1",
            "precio" => 100,
            "descripcion" => "Este es el primer producto"
        ]; 
        $producto2 = [
            "id" => "2",
            "nombre" => "Producto 2",
            "precio" => 200,
            "descripcion" => "Este es el segundo producto"
        ]; 
        $producto3 = [
            "id" => "3",
            "nombre" => "Producto 3",
            "precio" => 300,
            "descripcion" => "Este es el tercer producto"
        ]; 
        $producto4 = [
            "id" => "4",
            "nombre" => "Producto 4",
            "precio" => 400,
            "descripcion" => "Este es el cuarto producto"
        ]; 
        $producto5 = [
            "id" => "5",
            "nombre" => "Producto 5",
            "precio" => 500,
            "descripcion" => "Este es el quinto producto"
        ];


    ?>
    <div class="container">
        <h1>Lista de productos</h1>    
    </div>

    
    <div class="background">
        <div>
            <div class="container2">
                
                <div class="product">
                    <h2><?php echo $producto1["nombre"]; ?></h2>
                    <p>Precio <?php echo $producto1["precio"]; ?> <br></p>
                    <?php echo $producto1["descripcion"]; ?> <br>
                </div>
                <div class="product_image">
                    <img src="https://picsum.photos/id/1015/1000/1000" alt="imagen1">
                </div>
                <div>
                    <button type="button" id="boton1">Agregar al carrito</button>
                </div>
            </div>
            
            <div class="container2">
                
                <div class="product">
                    <h2><?php echo $producto2["nombre"]; ?></h2>
                    <p>Precio <?php echo $producto2["precio"]; ?> <br></p>
                    <?php echo $producto2["descripcion"]; ?> <br>
                </div>
                <div class="product_image">
                    <img src="https://picsum.photos/id/1016/1000/1000" alt="imagen2">
                </div>
                <div>
                    <button type="button" id="boton2">Agregar al carrito</button>
                </div>
            </div>
            <div class="container2">
                <div class="product">
                    <h2><?php echo $producto3["nombre"]; ?></h2>
                    <p>Precio <?php echo $producto3["precio"]; ?> <br></p>
                    <?php echo $producto3["descripcion"]; ?> <br>
                </div>
                <div class="product_image">
                    <img src="https://picsum.photos/id/1000/1000/1000" alt="imagen3">
                </div>
                <div>
                    <button type="button" id="boton3">Agregar al carrito</button>
                </div>
            </div>
            <div class="container2">
                <div class="product">
                    <h2><?php echo $producto4["nombre"];?></h2>
                    <p>Precio <?php echo $producto4["precio"]; ?> <br></p>
                    <?php echo $producto4["descripcion"]; ?> <br>
                </div>
                <div class="product_image">
                    <img src="https://picsum.photos/id/1018/1000/1000" alt="imagen4">
                </div>
                <div>
                    <button type="button" id="boton4">Agregar al carrito</button>
                </div>
            </div>
            <div class="container2">
                <div class="product">
                    <h2><?php echo $producto5["nombre"]; ?></h2>
                    <p>Precio <?php echo $producto5["precio"]; ?> <br></p>
                    <?php echo $producto5["descripcion"]; ?> <br>
                </div>
                <div class="product_image">
                    <img src="https://picsum.photos/id/1019/1000/1000" alt="imagen5">
                </div>
                <div>
                    <button type="button" id="boton5">Agregar al carrito</button>
                </div>
            </div>
            
        </div>
    </div>


        
    
    <script>
        document.getElementById("boton1").addEventListener("click", function() {
            alert("Producto 1 agregado al carrito");
        });
        document.getElementById("boton2").addEventListener("click", function() {
            alert("Producto 2 agregado al carrito");
        });
        document.getElementById("boton3").addEventListener("click", function() {
            alert("Producto 3 agregado al carrito");
        });
        document.getElementById("boton4").addEventListener("click", function() {
            alert("Producto 4 agregado al carrito");
        });
        document.getElementById("boton5").addEventListener("click", function() {
            alert("Producto 5 agregado al carrito");
        });
    </script>
</body>
</html>
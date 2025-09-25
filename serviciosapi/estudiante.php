<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar Estudiante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<fieldset class="border p-4 shadow" style="width: 50%; margin: 15px auto 0; padding: 10px;">
    <legend>Registrar Estudiante</legend>  
    <form name="frmregistrarestudiante" method="post" action="estudiante.php">
        <table align="center">
            <tr>
                <td>Código de Estudiante:</td>
                <td><input type="text" name="txtcodigo" required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); padding: 4px; border-radius: 5px; margin-bottom: 8px; margin-left: 15px; font-size: 14px;"/> </td>
            </tr>
            <tr>
                <td>DNI:</td>
                <td><input type="text" name="txtdni" required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); padding: 4px; border-radius: 5px; margin-bottom: 8px; margin-left: 15px; font-size: 14px;"/> </td>
            </tr>
            <tr>
                <td>Apellido:</td>
                <td><input type="text" name="txtapellido" required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); padding: 4px; border-radius: 5px; margin-bottom: 8px; margin-left: 15px; font-size: 14px;"/> </td>
            </tr>
            <tr>
                <td>Nombre:</td>
                <td><input type="text" name="txtnombre" required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); padding: 4px; border-radius: 5px; margin-bottom: 8px; margin-left: 15px; font-size: 14px;"/> </td>
            </tr>
            <tr>
                <td>Dirección:</td>
                <td><input type="text" name="txtdireccion" required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); padding: 4px; border-radius: 5px; margin-bottom: 8px; margin-left: 15px; font-size: 14px;"/> </td>
            </tr>
            <tr>
                <td>Teléfono:</td>
                <td><input type="text" name="txttelefono" required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); padding: 4px; border-radius: 5px; margin-bottom: 8px; margin-left: 15px; font-size: 14px;"/> </td>
            </tr>
            <tr>
                <td>Correo:</td>
                <td><input type="email" name="txtcorreo" required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); padding: 4px; border-radius: 5px; margin-bottom: 8px; margin-left: 15px; font-size: 14px;"/> </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <input type="submit" name="btnguardar" value="Registrar" style="padding: 10px 20px; border-radius: 8px; background-color: #4CAF50; color: white; border: none; box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.2);margin-top: 35px;"/> </td>
            </tr>                         
        </table>      
    </form>
</fieldset>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>

<?php
function enviar($url, $datos){
    $curl = curl_init();
    $tiempo = 300;
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $datos);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $tiempo);
    $resp = curl_exec($curl);
    curl_close($curl);
    return $resp;
}

if (isset($_POST["btnguardar"])) {
    // Leer los valores del formulario
    $codigo = $_POST["txtcodigo"];
    $dni = $_POST["txtdni"];
    $apellido = $_POST["txtapellido"];
    $nombre = $_POST["txtnombre"];
    $direccion = $_POST["txtdireccion"];
    $telefono = $_POST["txttelefono"];
    $correo = $_POST["txtcorreo"];

    
    $datos = [
        "CodigoEstudiante" => $codigo,
        "DNI" => $dni,
        "Apellido" => $apellido,
        "Nombre" => $nombre,
        "Direccion" => $direccion,
        "Telefono" => $telefono,
        "Correo" => $correo
    ];
    $json_data = json_encode($datos);

    
    $url = "http://localhost:8070/Estudiante";

  
    $respuesta = enviar($url, $json_data);
    if ($respuesta == "") {
        echo "<center>Error al registrar el Estudiante!</center>";
    } else {
        echo "<center>Estudiante registrado Correctamente!</center>";
    }
}

?>



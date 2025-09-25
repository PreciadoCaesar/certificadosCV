<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar Matrícula</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>

<fieldset class="border p-4 shadow" style="width: 50%; margin: 15px auto 0; padding: 10px;">
    <legend>Registrar Matrícula</legend>  
    <form name="frmregistrarmatricula" method="post" action="matricula.php">
        <table align="center">
            <tr>
                <td>Número:</td>
                <td><input type="text" name="txtnumero" required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); padding: 4px; border-radius: 5px; margin-bottom: 8px; margin-left: 10px; font-size: 12px;"/> </td>
            </tr>
            <tr>
                <td>Fecha:</td>
                <td><input type="date" name="txtfecha" required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); padding: 4px; border-radius: 5px; margin-bottom: 8px; margin-left: 10px; font-size: 12px;"/> </td>
            </tr>
            <tr>
                <td>Código Escuela:</td>
                <td><input type="text" name="txtescuela" required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); padding: 4px; border-radius: 5px; margin-bottom: 8px; margin-left: 10px; font-size: 12px;"/> </td>
            </tr>         
            <tr>
                <td>Código Trabajador:</td>
                <td><input type="text" name="txttrabajador" required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); padding: 4px; border-radius: 5px; margin-bottom: 8px; margin-left: 10px; font-size: 12px;"/> </td>
            </tr>         
            <tr>
                <td>Código Estudiante:</td>
                <td><input type="text" name="txtestudiante" required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); padding: 4px; border-radius: 5px; margin-bottom: 8px; margin-left: 10px; font-size: 12px;"/> </td>
            </tr>         
            <tr>
                <td>Periodo:</td>
                <td><input type="text" name="txtperiodo" required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); padding: 4px; border-radius: 5px; margin-bottom: 8px; margin-left: 10px; font-size: 12px;"/> </td>
            </tr>
            <tr>
                <td>Ciclo:</td>
                <td><input type="number" name="txtciclo" required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); padding: 4px; border-radius: 5px; margin-bottom: 8px; margin-left: 10px; font-size: 12px;"/> </td>
            </tr>
            <tr>
                <td>Turno:</td>
                <td><input type="text" name="txtturno" required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); padding: 4px; border-radius: 5px; margin-bottom: 8px; margin-left: 10px; font-size: 12px;"/> </td>
            </tr>       
            <tr>
                <td>Importe:</td>
                <td><input type="number" step="0.01" name="txtimporte" required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); padding: 4px; border-radius: 5px; margin-bottom: 8px; margin-left: 10px; font-size: 12px;"/> </td>
            </tr>
            <tr>                
                <td colspan="2" style="text-align: center;">
                    <input type="submit" name="btnguardar" value="Registrar Matrícula" style="padding: 10px 20px; border-radius: 8px; background-color: #4CAF50; color: white; border: none; box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.2);margin-top: 15px;"/> </td>
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
    
    // Obtención de datos
    $numero = $_POST["txtnumero"];
    $fecha = $_POST["txtfecha"];
    $codigoEscuela = intval($_POST["txtescuela"]);
    $codigoTrabajador = intval($_POST["txttrabajador"]);
    $codigoEstudiante = $_POST["txtestudiante"];
    $periodo = $_POST["txtperiodo"];
    $ciclo = intval($_POST["txtciclo"]);
    $turno = $_POST["txtturno"];
    $importe = floatval($_POST["txtimporte"]);

    // Validaciones
    $errores = [];
    
    if (empty($numero)) {
        $errores[] = "El número es obligatorio.";
    }
    if (empty($fecha) || !strtotime($fecha)) {
        $errores[] = "La fecha es obligatoria y debe tener un formato válido.";
    }
    if ($codigoEscuela <= 0) {
        $errores[] = "Código de escuela, inválido.";
    }
    if ($codigoTrabajador <= 0) {
        $errores[] = "Código de trabajador, inválido.";
    }
    if (empty($codigoEstudiante)) {
        $errores[] = "El código de estudiante es obligatorio.";
    }
    if (empty($periodo) || !is_numeric($periodo)) {
        $errores[] = "El período debe ser un valor numérico.";
    }
    if ($ciclo <= 0) {
        $errores[] = "El ciclo debe ser un número válido.";
    }
    if (empty($turno)) {
        $errores[] = "El turno es obligatorio.";
    }
    if ($importe <= 0) {
        $errores[] = "El importe debe ser un número mayor a 0.";
    }
    
    // Si hay errores, mostrar mensajes de error
    if (!empty($errores)) {
        echo "<center><ul>";
        foreach ($errores as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul></center>";
    } else {
        // Si no hay errores, preparar los datos y enviar la solicitud
        $datos = [
            "Numero" => $numero,
            "Fecha" => $fecha,
            "CodigoEscuela" => $codigoEscuela,
            "CodigoTrabajador" => $codigoTrabajador,
            "CodigoEstudiante" => $codigoEstudiante,
            "NumeroPeriodo" => $periodo,
            "Ciclo" => $ciclo,
            "Turno" => $turno,
            "Importe" => $importe
        ];
        $json_data = json_encode($datos);

        $url = "http://localhost:8070/Matricula";
        
        // Enviar solicitud
        $respuesta = enviar($url, $json_data);
        if ($respuesta == "") {
            echo "<center>Error al registrar la Matrícula!</center>";
        } else {
            // Aquí puedes verificar la respuesta más detalladamente
            $respuesta_json = json_decode($respuesta, true);
            if (isset($respuesta_json['error'])) {
                echo "<center>Error: " . $respuesta_json['error'] . "</center>";
            } else {
                echo "<center>Matrícula registrada correctamente!</center>";
            }
        }
    }
}
?>
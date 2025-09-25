<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lista de Matrículas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<form name="frmlistar" method="post" action="listarmatriculas.php" style="margin-top: 100px; padding: 20px; box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.2); border:  1px solid #ccc; border-radius: 8px; max-width: 600px; margin-left: auto; margin-right: auto;">
    <table align="center">
        <tr>
            <td>Periodo:</td>
            <td><input type="text" name="txtperiodo" 
            	required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.1); padding: 5px; border-radius: 8px; margin-bottom: 10px; margin-left: 10px;"/></td>
        </tr>
        <tr>
            <td>Código de Escuela:</td>
            <td><input type="text" name="txtescuela" 
            	required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.1); padding: 5px; border-radius: 8px; margin-bottom: 10px; margin-left: 10px;"/></td>
        </tr>
        <tr>
            <td>Ciclo:</td>
            <td><input type="number" name="txtciclo" 
            	required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.1); padding: 5px; border-radius: 8px; margin-bottom: 10px; margin-left: 10px;"/></td>
        </tr>
        <tr>
            <td>Turno:</td>
            <td><input type="text" name="txtturno" 
            	required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.1); padding: 5px; border-radius: 8px; margin-bottom: 10px; margin-left: 10px;"/></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">
                <input type="submit" value="Listar Matrículas" name="btnlistar" style="padding: 10px 20px; border-radius: 8px; background-color: #4CAF50; color: white; border: none; box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.2);margin-top: 15px;"/></td>
        </tr>
    </table>
</form>

<?php
if (isset($_POST["btnlistar"])){
    $NumeroPeriodo = $_POST["txtperiodo"];
    $CodigoEscuela = $_POST["txtescuela"];
    $Ciclo = $_POST["txtciclo"];
    $Turno = $_POST["txtturno"];

    // Filtrar Lista de Matrículas
    $url = "http://localhost:8070/FiltrarMatricula?NumeroPeriodo=".$NumeroPeriodo."&CodigoEscuela=".$CodigoEscuela."&Ciclo=".$Ciclo."&Turno=".$Turno;
    $res = file_get_contents($url);
    $matriculas = json_decode($res, true);
?>
<center>
    <fieldset class="border p-4 shadow" style="width: 50%; margin: 15px auto; padding: 20px; margin-top: 50px;">
        <legend>Listado de Matrículas</legend>
        <?php if (empty($matriculas)) { ?> 
        	<p>No se encontraron matrículas para los filtros ingresados.</p> 
        <?php } else { ?>
    <div class="table-responsive">
        <table align="center" cellpadding="3" cellspacing="0" border="1" class="table table-bordered table-striped table-hover">
            <tr>
                <th>Número</th>
                <th>Fecha</th>
                <th>Escuela</th>
                <th>Apellido Trabajador</th>
                <th>Nombre Trabajador</th>
                <th>Apellido Estudiante</th>
                <th>Nombre Estudiante</th>
                <th>Periodo</th>
                <th>Ciclo</th>
                <th>Turno</th>
                <th>Importe</th>
            </tr>
            <?php
                foreach($matriculas as $matricula){
                    echo("<tr>");
                    echo("<td>".$matricula['Numero']."</td>");
                    echo("<td>".$matricula['Fecha']."</td>");
                    echo("<td>".$matricula['Escuela']."</td>");
                    echo("<td>".$matricula['ApellidoTrabajador']."</td>");
                    echo("<td>".$matricula['NombreTrabajador']."</td>");
                    echo("<td>".$matricula['ApellidoEstudiante']."</td>");
                    echo("<td>".$matricula['NombreEstudiante']."</td>");
                    echo("<td>".$matricula['Periodo']."</td>");
                    echo("<td>".$matricula['Ciclo']."</td>");
                    echo("<td>".$matricula['Turno']."</td>");
                    echo("<td>".$matricula['Importe']."</td>");
                    echo("</tr>");
                }
            ?>
        </table>
    </div>
        <?php } ?>
    </fieldset>
</center>
<?php
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
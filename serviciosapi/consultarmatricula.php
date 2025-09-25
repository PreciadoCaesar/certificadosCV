<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consultar Matrícula</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<form name="frmconsultar" method="post" action="consultarmatricula.php" style="margin-top: 100px; padding: 20px; box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.2); border: 1px solid #ccc; border-radius: 8px; max-width: 600px; margin-left: auto; margin-right: auto;">
    <table align="center">
        <tr>
            <td style="padding-right: 20px; vertical-align: middle;">Número de Matrícula:</td>
            <td><input type="text" name="txtnumMatricula" required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.1); padding: 10px; border-radius: 8px; width: 100%; margin-bottom: 15px;"/></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">
                <input type="submit" value="Consultar" name="btnconsultar" style="padding: 10px 20px; border-radius: 8px; background-color: #4CAF50; color: white; border: none; box-shadow: 6px 6px 15px rgba(0, 0, 0, 0.2); margin-top: 15px;"/></td>
        </tr>
    </table>
</form>

<?php
if (isset($_POST["btnconsultar"])) {
    $numMatricula = $_POST["txtnumMatricula"];

    
    $url = "http://localhost:8070/Buscarmatricula/".$numMatricula;
    $res = file_get_contents($url);
    $matricula = json_decode($res, true);

    
?>
<center>
    <fieldset class="border p-4 shadow" style="width: 50%; margin: 15px auto; padding: 20px; margin-top: 50px;">
        <legend>Datos de la Matrícula</legend>
        <?php if (empty($matricula)) { ?>
            <p>No se encontraron los datos para el número de matrícula que se ha ingresado.</p>
        <?php } else { ?>
        <table align="center" cellpadding="3" cellspacing="0" border="1" class="table table-bordered table-striped table-hover">
            <tr>
                <th>Número</th>
                <th>Fecha</th>
                <th>Código Escuela</th>
                <th>Código Trabajador</th>
                <th>Código Estudiante</th>
                <th>Periodo</th>
                <th>Ciclo</th>
                <th>Turno</th>
                <th>Importe</th>
            </tr>
            <tr>
                <td><?php echo $matricula[0]['Numero']; ?></td>
                <td><?php echo $matricula[0]['Fecha']; ?></td>
                <td><?php echo $matricula[0]['CodigoEscuela']; ?></td>
                <td><?php echo $matricula[0]['CodigoTrabajador']; ?></td>
                <td><?php echo $matricula[0]['CodigoEstudiante']; ?></td>
                <td><?php echo $matricula[0]['NumeroPeriodo']; ?></td>
                <td><?php echo $matricula[0]['Ciclo']; ?></td>
                <td><?php echo $matricula[0]['Turno']; ?></td>
                <td><?php echo $matricula[0]['Importe']; ?></td>
            </tr>
        </table>
        <?php } ?>
    </fieldset>
</center>
<?php
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
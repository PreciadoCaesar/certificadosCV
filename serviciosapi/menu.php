<?php
session_start();
if ($_SESSION['nombre']==""){
	header("Location:login.php");
	return;
}
else{
	$usuario=$_SESSION['nombre'];
	$clave=$_SESSION['pass'];
	$rol=$_SESSION['rol'];
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>MENU REGISTRO</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<script type="text/javascript">
		function ir(dato){
			var marco=document.getElementById("marco");
			if(dato==1)
				marco.src="estudiante.php";
			if(dato==2)
				marco.src="matricula.php";
			if(dato==3)
				marco.src="consultarmatricula.php";
			if(dato==4)
				marco.src="listarmatriculas.php";
			if(dato==5)
				window.location.href="logout.php";
			
		}
	</script>

</head>
<body>
<table class="table table-bordered shadow p-3 mb-5 bg-body rounded" style="width: 80%; margin: auto; margin-top: 100px;">
    <tr>
        <td>
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn" style="background-color: #f4f4f4; box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.1); border: 1px solid #ccc; color: #333; margin-right: 10px;" name="op1" onclick="ir(1)">Estudiante</button>
                <button class="btn" style="background-color: #f4f4f4; box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.1); border: 1px solid #ccc; color: #333; margin-right: 10px;" name="op2" onclick="ir(2)">Matricula</button>
                <button class="btn" style="background-color: #f4f4f4; box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.1); border: 1px solid #ccc; color: #333; margin-right: 10px;" name="op3" onclick="ir(3)">Consultar Matricula</button>
                <button class="btn" style="background-color: #f4f4f4; box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.1); border: 1px solid #ccc; color: #333; margin-right: 10px;" name="op4" onclick="ir(4)">Listar Matriculas</button>
                <div class="text-end flex-grow-1">
                <div class="text-end flex-grow-1">
                    BIENVENIDO USUARIO [ <?php echo $usuario ?> ]
                    <button class="btn btn-danger border-0 ms-2"  style = "box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.1); border: none; margin-right: 10px;" name="op4" onclick="ir(5)">CERRAR SESIÓN</button>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <iframe id="marco" src="" class="w-100 border shadow-sm" height="500" frameborder="0"></iframe>
        </td>
    </tr>
</table>	
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
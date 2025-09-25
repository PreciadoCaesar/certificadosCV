<?php
if (isset($_POST["btnaceptar"])){
	$usuario=$_POST["txtus"];
	$clave=$_POST["txtpa"];
	$contenido=file_get_contents("http://localhost:8070/Validar/".$usuario."/".$clave);
	$reg=json_decode($contenido,true);
	foreach ($reg as $valor) {
		$rol=$valor["rolus"];

	}
	if(empty($reg)){
		echo "<center>La Identificaci&oacute;n del Usuario no es V&acute;lido....!</center>";
	}
	else{
		session_start();
		$_SESSION['nombre']=$usuario;
		$_SESSION['pass']=$clave;
		$_SESSION['rol']=$rol;

		header("Location:menu.php");
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Iniciar Sesi&oacute;n</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<script type="text/javascript">

		function desordenar(array){
			array=array.sort(function() {return Math.random() - 0.5});
			return array;
		}
		function numeros(){
			var numeros=["1","2","3","4","5","6","7","8","9","0"];
			numerosdesordenado=[];
			numerosdesordenado[0]=desordenar(numeros);
			numerosdesordenado=Array.from(numerosdesordenado[0]);
			document.getElementById("bt1").value=numerosdesordenado[0];
			document.getElementById("bt2").value=numerosdesordenado[1];
			document.getElementById("bt3").value=numerosdesordenado[2];
			document.getElementById("bt4").value=numerosdesordenado[3];
			document.getElementById("bt5").value=numerosdesordenado[4];
			document.getElementById("bt6").value=numerosdesordenado[5];
			document.getElementById("bt7").value=numerosdesordenado[6];
			document.getElementById("bt8").value=numerosdesordenado[7];
			document.getElementById("bt9").value=numerosdesordenado[8];
			document.getElementById("bt0").value=numerosdesordenado[9];
		}
		function editar (dato){
			var cadena=document.getElementById("txtpa").value;
			if (dato==-1 && cadena.length>0){
				document.getElementById("txtpa").value=cadena.substr(0,cadena.length-1);
			}
			else{
				if (dato>-1)
					document.getElementById("txtpa").value=document.getElementById("txtpa").value + dato; 
			}
		}
	</script>

</head>
<body onload="numeros()">
<div style="display: flex; justify-content: center; align-items: center; height: 100vh;">
<fieldset class="border p-4 shadow" style="width: 50%; margin: 15px auto 0; padding: 10px;">
	<legend>Iniciar Sesion</legend>
<form name="frmsesion" action="login.php" method="post">
	<table align="center">
		<tr>
			<td>Usuario:</td>
			<td><input type="text" name="txtus" maxlength="20"
				size=20 required autocomplete="off" style="border: 1px solid #ccc; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); padding: 4px; border-radius: 5px; margin-bottom: 8px; margin-left: 15px; font-size: 14px;"/></td>
		</tr>
		<tr>
			<td>Contrase&nacute;a:</td>
			<td><input type="password" name="txtpa" id="txtpa" maxlength="15"
				size=16 required autocomplete="off" readonly =true style="border: 1px solid #ccc; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); padding: 4px; border-radius: 5px; margin-bottom: 8px; margin-left: 15px; font-size: 14px;"/></td>
		</tr>
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td>
				<table style="margin: 0 auto;">
					<tr>
						<td style="display: flex; justify-content: flex-end;">
							<input type="button" name="bt1" id="bt1" onclick="editar(this.value)" style="box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); margin: 5px; padding: 8px 12px; border-radius: 5px; border: 1px solid #ccc; font-size: 18px;"/>
							<input type="button" name="bt1" id="bt2" onclick="editar(this.value)" style="box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); margin: 5px; padding: 8px 12px; border-radius: 5px; border: 1px solid #ccc; font-size: 18px;"/>
							<input type="button" name="bt1" id="bt3" onclick="editar(this.value)" style="box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); margin: 5px; padding: 8px 12px; border-radius: 5px; border: 1px solid #ccc; font-size: 18px;"/>
						</td>
					</tr>
					<tr>
						<td style="display: flex; justify-content: flex-end;">
							<input type="button" name="bt1" id="bt4" onclick="editar(this.value)" style="box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); margin: 5px; padding: 8px 12px; border-radius: 5px; border: 1px solid #ccc; font-size: 18px;"/>
							<input type="button" name="bt1" id="bt5" onclick="editar(this.value)" style="box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); margin: 5px; padding: 8px 12px; border-radius: 5px; border: 1px solid #ccc; font-size: 18px;"/>
							<input type="button" name="bt1" id="bt6" onclick="editar(this.value)" style="box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); margin: 5px; padding: 8px 12px; border-radius: 5px; border: 1px solid #ccc; font-size: 18px;"/>
						</td>
					</tr>
					<tr>
						<td style="display: flex; justify-content: flex-end;">
							<input type="button" name="bt1" id="bt7" onclick="editar(this.value)" style="box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); margin: 5px; padding: 8px 12px; border-radius: 5px; border: 1px solid #ccc; font-size: 18px;"/>
							<input type="button" name="bt1" id="bt8" onclick="editar(this.value)" style="box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); margin: 5px; padding: 8px 12px; border-radius: 5px; border: 1px solid #ccc; font-size: 18px;"/>
							<input type="button" name="bt1" id="bt9" onclick="editar(this.value)" style="box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); margin: 5px; padding: 8px 12px; border-radius: 5px; border: 1px solid #ccc; font-size: 18px;"/>
						</td>
					</tr>
					<tr>
						<td>
							<input type="button" name="bt1" id="bt0" onclick="editar(this.value)" style="box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); margin: 5px; padding: 8px 12px; border-radius: 5px; border: 1px solid #ccc; font-size: 18px;"/>
							<input type="button" name="bt1" value="Borrar" onclick="editar(-1)" style="box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); margin: 5px; padding: 8px 12px; border-radius: 5px; border: 1px solid #ccc; font-size: 18px;"/>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="submit" name="btnaceptar" value="Aceptar" style="padding: 10px 20px; border-radius: 8px; background-color: #4CAF50; color: white; border: none; box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.2);margin-top: 35px;"/>
			</td>
		</tr>

	</table>
</form>
</fieldset>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>


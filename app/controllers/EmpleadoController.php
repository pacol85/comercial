<?php
class EmpleadoController extends ControllerBase
{

	public function indexAction()
	{
		parent::limpiar();
		$sucursal = Sucursal::find("estado = 1");
		$campos = [
				["t", ["pNombre"], "Primer Nombre"],
				["t", ["sNombre"], "Segundo Nombre"],
				["t", ["pApellido"], "Primer Apellido"],
				["t", ["sApellido"], "Segundo Apellido"],
				["t", ["aCasada"], "Apellido Casada"],
				["sel", ["genero", ["m" => "Masculino", "f" => "Femenino"]], "G&eacute;nero"],
				["d", ["fNac"], "Fecha Nacimiento"],
				["h", ["fNac2"], ""],
				["t", ["dir"], "Direcci&oacute;n"],
				["d", ["fIngreso"], "Fecha Ingreso"],
				["h", ["fIngreso2"], ""],
				["sdb", ["suc", $sucursal, ["id", "nombre"]], "Sucursal"],
				["m", ["salario", 0], "Salario"],
				["t", ["dui"], "DUI"],
				["t", ["nit"], "NIT"],
				["t", ["isss"], "ISSS"],
				["t", ["afp"], "AFP"],
				["t", ["nafp"], "No. AFP"],
				["h", ["id"], ""],
				["s", [""], "Guardar"]	
		];		
		$form = parent::form($campos, "empleado/guardar", "form1");
		
		$head = ["Nombre", "Apellido", "G&eacute;nero", "Fecha Ingreso",
				"Sucursal", "Salario", "Acciones"				
		];
		$tabla = parent::thead("empleados", $head);
		$empleados = Empleado::find();
		foreach ($empleados as $e){
			$s = Sucursal::findFirst("id = ".$e->sucursal);
			$deshabilitar = "Deshabilitar";
			if($e->estado == 0){
				$deshabilitar = "Habilitar";
			}
			$genero = "Masculino";
			if($e->genero == "f"){
				$genero = "Femenino";
			}
			$tabla = $tabla.parent::tbody([
					$e->primer_nombre,
					$e->primer_apellido,
					$genero,
					$e->fecha_ingreso,
					$s->nombre,
					$e->salario, 
					parent::a(2, "cargarDatos('".$e->id."', '".$e->primer_nombre."', '".
							$e->segundo_nombre."', '".$e->primer_apellido."', '".
							$e->segundo_apellido."', '".$e->apellido_casada."', '".
							$e->genero."', '".$e->fecha_nacimiento."', '".$e->fecha_nacimiento."', '".$e->direccion."', '".
							$e->fecha_ingreso."', '".$e->fecha_ingreso."', '".$e->sucursal."', '".$e->salario."', '".
							$e->dui."', '".$e->nit."', '".$e->isss."', '".$e->afp."', '".
							$e->afpnum."')", "Editar")." | ".
					parent::a(1, "empleado/deshabilitar", $deshabilitar, [["id", $e->id]])
			]);
		}		
		
		//js
		$fields = ["id", "pNombre", "sNombre", "pApellido", "sApellido", "aCasada", "genero",
				"fNac", "fNac2", "dir", "fIngreso", "fIngreso2", "suc", "salario", "dui", "nit", "isss", "afp", "nafp"];
		$otros = "";
		$jsBotones = ["form1", "empleado/edit", "empleado/index"];
		
		parent::view("Empleados", $form, $tabla, [$fields, $otros, $jsBotones]);
	}
	
	public function guardarAction(){
		if(parent::vPost("pNombre") && parent::vPost("pApellido") && parent::vPost("dir") && parent::vPost("fIngreso") &&
				parent::vPost("salario") && parent::vPost("dui") && parent::vPost("nit") ){
			$dui = parent::gPost("dui");
			$nit = parent::gPost("nit");
			$isss = parent::gPost("isss");
			$afp = parent::gPost("afp");
			$emp = Empleado::find("dui like '$dui' or nit like '$nit' or isss like '$isss' or afpnum like '$afp'");
			if(count($emp) > 0){
				parent::msg("Los documentos ingresados equivalen a los de otro usuario ya existente");
				return parent::forward("empleado", "index");
			}//else{
				//crear nuevo empleado
				$e = new Empleado();
				$e->primer_nombre = parent::gPost("pNombre");
				$e->segundo_nombre = parent::gPost("sNombre");
				$e->primer_apellido = parent::gPost("pApellido");
				$e->segundo_apellido = parent::gPost("sApellido");
				$e->apellido_casada = parent::gPost("aCasada");
				$e->genero = parent::gPost("genero");
				$e->fecha_nacimiento = parent::gPost("fNac");
				$e->direccion = parent::gPost("dir");
				$e->fecha_ingreso = parent::gPost("fIngreso");
				$e->sucursal = parent::gPost("suc");
				$e->salario = parent::gPost("salario");
				$e->dui = parent::gPost("dui");
				$e->nit = parent::gPost("nit");
				$e->isss = parent::gPost("isss");
				$e->afp = parent::gPost("afp");
				$e->afpnum = parent::gPost("nafp");
				$e->fcreacion = parent::fechaHoy(true);
				$e->fmod = parent::fechaHoy(true);
				$e->estado = 1;
				if($e->save()){
					parent::msg("El empleado fue creado exitosamente", "s");
				}else{
					parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
				}
			//}			
		}else{
			parent::msg("Aseg&uacute;rese de llenar todos los campos");
		}
		parent::forward("empleado", "index");
	}
	
	public function editAction(){
		if(!parent::vPost("id")){
			parent::msg("Empleado no se carg&oacute; correctamente");
			parent::forward("empleado", "index");
		}else{
			$id = parent::gPost("id");
			$dui = parent::gPost("dui");
			$nit = parent::gPost("nit");
			$isss = parent::gPost("isss");
			$afp = parent::gPost("nafp");
			
			$e = Empleado::findFirst("id = $id");
			$emps = Empleado::find("(dui like '$dui' or nit like '$nit' or isss like '$isss' or afpnum like '$afp')
					and id not like $id");
			if(count($emps) > 0){
				parent::msg("El empleado $e->primer_nombre $e->primer_apellido ya existe con alguno de estos documentos (DUI, NIT, ISSS o AFP");
				parent::forward("empleado", "index");
			}else{
				$e->primer_nombre = parent::gPost("pNombre");
				$e->segundo_nombre = parent::gPost("sNombre");
				$e->primer_apellido = parent::gPost("pApellido");
				$e->segundo_apellido = parent::gPost("sApellido");
				$e->apellido_casada = parent::gPost("aCasada");
				$e->genero = parent::gPost("genero");
				if(parent::vPost("fNac")){
					$e->fecha_nacimiento = parent::gPost("fNac");
				}else{
					$e->fecha_nacimiento = parent::gPost("fNac2");
				}
				$e->direccion = parent::gPost("dir");
				if(parent::vPost("fIngreso")){
					$e->fecha_ingreso = parent::gPost("fIngreso");
				}else{
					$e->fecha_ingreso = parent::gPost("fIngreso2");
				}
				$e->sucursal = parent::gPost("suc");
				$e->salario = parent::gPost("salario");
				$e->dui = $dui;
				$e->nit = $nit;
				$e->isss = $isss;
				$e->afp = parent::gPost("afp");
				$e->afpnum = $afp;
				$e->fmod = parent::fechaHoy(true);
				if($e->update()){
					parent::msg("Edici&oacute;n exitosa", "s");
					parent::forward("empleado", "index");
				}else{
					parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
					parent::forward("empleado", "index");
				}
			}
				
		}
		
	}
	
	public function deshabilitarAction(){
		$id = parent::gReq("id");
		if($id == "" || $id == null){
			parent::msg("Empleado no se carg&oacute; correctamente");
			parent::forward("empleado", "index");
		}
		$e = Empleado::findFirst("id = $id");
		if($e->estado == 1){
			$e->estado = 0;
		}else{
			$e->estado = 1;
		}
		$e->fmod = parent::fechaHoy(true);
		if($e->update()){
			if($e->estado == 1){
				parent::msg("$e->primer_nombre $e->primer_apellido Habilitado exitosamente", "s");
			}else{
				parent::msg("$e->primer_nombre $e->primer_apellido Deshabilitado exitosamente", "s");
			}			
			parent::forward("empleado", "index");
		}else{
			parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
			parent::forward("empleado", "index");
		}
	}

}
?>
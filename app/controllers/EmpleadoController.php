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
				["t", ["dir"], "Direcci&oacute;n"],
				["d", ["fIngreso"], "Fecha Ingreso"],
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
		
		$head = ["Correlativo", "Nombre", "Apellido", "G&eacute;nero", "Fecha Ingreso",
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
					$e->id,
					$e->primer_nombre,
					$e->primer_apellido,
					$genero,
					$e->fecha_ingreso,
					$s->nombre,
					$e->salario, 
					parent::a(2, "cargarDatos('".$e->id."', '".$e->primer_nombre."', '".
							$e->segundo_nombre."', '".$e->primer_apellido."', '".
							$e->segundo_apellido."', '".$e->apellido_casada."', '".
							$e->genero."', '".$e->fecha_nacimiento."', '".$e->direccion."', '".
							$e->fecha_ingreso."', '".$e->sucursal."', '".$e->salario."', '".
							$e->dui."', '".$e->nit."', '".$e->isss."', '".$e->afp."', '".
							$e->afpnum."')", "Editar")." | ".
					parent::a(1, "empleado/deshabilitar", $deshabilitar, [["id", $u->id]])
			]);
		}		
		
		//js
		$fields = ["id", "pNombre", "sNombre", "pApellido", "sApellido", "aCasada", "genero",
				"fNac", "dir", "fIngreso", "suc", "salario", "dui", "nit", "isss", "afp", "nafp"];
		$otros = "";
		$jsBotones = ["form1", "empleado/edit", "empleado/index"];
		
		parent::view("Empleados", $form, $tabla, [$fields, $otros, $jsBotones]);
	}
	
	public function guardarAction(){
		if(parent::vPost("pNombre") && parent::vPost("pApellido") && parent::vPost("dir") && parent::vPost("fIngreso") &&
				parent::vPost("salario") && parent::vPost("dui") && parent::vPost("nit") ){
			$dui = parent::gPost("dui");
			$emp = Empleado::find("dui = $dui");
			if(count($emp) > 0){
				$emp1 = $emp->getFirst();
				parent::msg("El empleado ya existe en el sistema bajo el nombre: $emp1->primer_ombre $emp1->primer_apellido");
				parent::forward("empleado", "index");
			}
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
		}else{
			parent::msg("Aseg&uacute;rese de llenar todos los campos");
		}
		parent::forward("empleado", "index");
	}
	
	public function editAction(){
		if(!parent::vPost("id")){
			parent::msg("Id no se carg&oacute; correctamente");
			parent::forward("usuario", "index");
		}
		$id = parent::gPost("id");
		$u = Usuario::findFirst("id = $id");
		$user = parent::gPost("usuario");
		$users = Usuario::find("usuario like '$user' and id not like $id");
		if(count($users) > 0){
			parent::msg("El usuario $user ya est&aacute; siendo utilizado");
			parent::forward("usuario", "index");
		}
		$u->usuario = $user;
		$u->rol_id = parent::gPost("rol");
		$u->fmod = parent::fechaHoy(true);
		if($u->update()){
			parent::msg("Edici&oacute;n exitosa", "s");
			parent::forward("usuario", "index");
		}else{
			parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
			parent::forward("usuario", "index");
		}
		
	}
	
	public function resetearAction(){
		$id = parent::gReq("id");
		if($id == "" || $id == null){
			parent::msg("Id no se carg&oacute; correctamente");
			parent::forward("usuario", "index");
		}
		$u = Usuario::findFirst("id = $id");
		$u->clave = parent::newPass();
		$u->fmod = parent::fechaHoy(true);
		if($u->update()){
			parent::msg("Contrase&ntilde;a reseteada para usuario $u->usuario", "s");
			parent::forward("usuario", "index");
		}else{
			parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
			parent::forward("usuario", "index");
		}
	}
	
	public function deshabilitarAction(){
		$id = parent::gReq("id");
		if($id == "" || $id == null){
			parent::msg("Id no se carg&oacute; correctamente");
			parent::forward("usuario", "index");
		}
		$u = Usuario::findFirst("id = $id");
		if($u->estado == 1){
			$u->estado = 0;
		}else{
			$u->estado = 1;
		}
		$u->fmod = parent::fechaHoy(true);
		if($u->update()){
			if($u->estado == 1){
				parent::msg("Usuario $u->usuario deshabilitado exitosamente", "s");
			}else{
				parent::msg("Usuario $u->usuario habilitado exitosamente", "s");
			}			
			parent::forward("usuario", "index");
		}else{
			parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
			parent::forward("usuario", "index");
		}
	}

}
?>
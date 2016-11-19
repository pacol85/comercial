<?php
class ClienteController extends ControllerBase
{

	public function indexAction()
	{
		parent::limpiar();
		$sucursal = Sucursal::find("estado = 1");
		$dept = Departamentos::find();
		$d = $dept->getFirst();
		$muni = Municipios::find("departamento = $d->id");
		//["ls", ["municipio", "municipios('ajax/municipios')"], "Municipio"],
		
		$campos = [
				["t", ["nombre"], "Nombre Completo"],
				["sel", ["tipoDoc", ["d" => "DUI", "n" => "NIT"]], "Tipo Documento"],
				["t", ["doc"], "Documento"],
				["d", ["expedicion"], "Fecha Expedici&oacute;n"],
				["t", ["lugar"], "Lugar Expedici&oacute;n"],
				["t", ["dir"], "Direcci&oacute;n"],
				["sdb", ["dept", $dept, ["id", "nombre"]], "Departamento"],
				["sdb", ["muni", $muni, ["id", "nombre"]], "Municipio", "mdiv"],
				["h", ["mid"], ""],
				["sel", ["alquila", ["1" => "S&iacute;", "0" => "No"]], "Alquila"],
				["t", ["propietario"], "Propietario"],
				["t", ["trabajo"], "Trabajo"],
				["t", ["area"], "Area de trabajo"],
				["t", ["cargo"], "Cargo"],
				["d", ["fdesde"], "Desde"],
				["m", ["sueldo", 0], "Sueldo"],
				["t", ["tofic"], "Tel&eacute;fono Oficina"],
				["h", ["id"], ""],
				["s", [""], "Guardar"]	
		];		
		$form = parent::form($campos, "cliente/guardar", "form1");
		
		$head = ["Nombre", "Documento", "Municipio", "Alquila", "Trabajo",
				"Sueldo", "Tel.", "Acciones"				
		];
		$tabla = parent::thead("clientes", $head);
		$clientes = Cliente::find();
		foreach ($clientes as $c){
			$s = Sucursal::findFirst("id = ".$e->sucursal);
			$deshabilitar = "Deshabilitar";
			if($c->estado == 0){
				$deshabilitar = "Habilitar";
			}
			$alquila = "S&iacute;";
			if($c->alquila == 0){
				$alquila = "No";
			}
			$m = Municipios::findFirst("municipio = $c->municipio");
			$tabla = $tabla.parent::tbody([
					$c->nombre,
					$c->documento,
					$m,
					$alquila,
					$c->trabajo,
					$c->sueldo,
					$c->telOficina,
					parent::a(2, "cargarDatos('".$c->id."', '".$c->nombre."', '".$c->tipodoc."', '".
							$c->documento."', '".$c->fexpedicion."', '".$c->lugarExpedicion."', '".$c->direccion."', '".
							$m->departamento."', '".$m->id."', '".$c->alquila."', '".$c->propietario."', '".
							$c->trabajo."', '".$c->areaTrab."', '".$c->cargo."', '".$c->fdesde."', '".
							$c->sueldo."', '".$c->telOficina."')", "Editar")." | ".
					parent::a(1, "cliente/deshabilitar", $deshabilitar, [["id", $c->id]])
			]);
		}		
		
		//js
		$fields = ["id", "nombre", "tipoDoc", "doc", "expedicion", "lugar", "dir", "dept", "muni", "alquila", 
				"propietario", "trabajo", "area", "cargo", "fdesde", "sueldo", "tofic"];
		$otros = "";
		$jsBotones = ["form1", "cliente/edit", "cliente/index"];
		
		parent::view("Clientes", $form, $tabla, [$fields, $otros, $jsBotones]);
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
	
	public function listMunicipioAction()
	{
		$dept = parent::gPost("dept");
		//$muni = Municipios::find("departamento = $dept");
		//$sel = parent::elemento("sdb", ["muni", $muni, ["id", "nombre"]], "Municipio");
		$select = $this->tag->select(array("muni",
				Municipios::find("departamento = $dept"),
				"using" => array("id", "nombre"), "class" => "form-control", "id" => "muni"));
		$response = ['select' => $select, 'dept' => $dept];
		return parent::sendJson($response);
	
	}

}
?>
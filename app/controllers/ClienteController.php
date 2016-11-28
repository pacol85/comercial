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
				["sel", ["tipoDoc", ["1" => "DUI", "2" => "NIT"]], "Tipo Documento"],
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
				["t", ["jefe"], "Jefe"],
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
			$deshabilitar = "Deshabilitar";
			if($c->estado == 0){
				$deshabilitar = "Habilitar";
			}
			$alquila = "S&iacute;";
			if($c->alquila == 0){
				$alquila = "No";
			}
			$m = Municipios::findFirst("id = $c->municipio");
			$tabla = $tabla.parent::tbody([
					$c->nombre,
					$c->documento,
					$m->nombre,
					$alquila,
					$c->trabajo,
					$c->sueldo,
					$c->telOficina,
					parent::a(2, "cargarDatos('".$c->id."', '".$c->nombre."', '".$c->tipodoc."', '".
							$c->documento."', '".$c->fexpedicion."', '".$c->lugarExpedicion."', '".$c->direccion."', '".
							$m->departamento."', '".$m->id."', '".$c->alquila."', '".$c->propietario."', '".
							$c->trabajo."', '".$c->areaTrab."', '".$c->cargo."', '".$c->jefe."', '".$c->fdesde."', '".
							$c->sueldo."', '".$c->telOficina."')", "Editar")." | ".
					parent::a(1, "cliente/deshabilitar", $deshabilitar, [["id", $c->id]])
			]);
		}		
		
		//js
		$fields = ["id", "nombre", "tipoDoc", "doc", "expedicion", "lugar", "dir", "dept", "muni", "alquila", 
				"propietario", "trabajo", "area", "cargo", "jefe", "fdesde", "sueldo", "tofic"];
		$otros = "";
		$jsBotones = ["form1", "cliente/edit", "cliente/index"];
		
		parent::view("Clientes", $form, $tabla, [$fields, $otros, $jsBotones]);
	}
	
	public function guardarAction(){
		if(parent::vPost("nombre") && parent::vPost("doc") && parent::vPost("dir") && parent::vPost("trabajo") &&
				parent::vPost("fdesde") && parent::vPost("sueldo")){
			$doc = parent::gPost("doc");
			$nombre = parent::gPost("nombre");
			$cli = Cliente::find("documento like '$doc' or nombre like '$nombre'");
			if(count($cli) > 0){
				parent::msg("Los documentos ingresados equivalen a los de otro cliente ya existente");
				return parent::forward("cliente", "index");
			}
			//crear nuevo cliente
			$c = new Cliente();
			$c->alquila = parent::gPost("alquila");
			$c->areaTrab = parent::gPost("area");
			$c->cargo = parent::gPost("cargo");
			$c->direccion = parent::gPost("dir");
			$c->documento = parent::gPost("doc");
			$c->estado = 1;
			$c->fdesde = parent::gPost("fdesde");
			$c->fexpedicion = parent::gPost("expedicion");
			$c->jefe = parent::gPost("jefe");
			$c->lugarExpedicion = parent::gPost("lugar");
			$c->municipio = parent::gPost("muni");
			$c->trabajo = parent::gPost("trabajo");
			$c->nombre = parent::gPost("nombre");
			if($c->alquila == 1){
				$c->propietario = parent::gPost("propietario");
			}else{
				$c->propietario = $c->nombre;
			}			
			$c->sueldo = parent::gPost("sueldo");
			$c->telOficina = parent::gPost("tofic");
			$c->tipodoc = parent::gPost("tipoDoc");
			if($c->save()){
				parent::msg("El cliente fue creado exitosamente", "s");
			}else{
				parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
			}
		}else{
			parent::msg("Aseg&uacute;rese de llenar todos los campos");
		}
		parent::forward("cliente", "index");
	}
	
	public function editAction(){
		if(!parent::vPost("id")){
			parent::msg("Cliente no se carg&oacute; correctamente");
			return parent::forward("cliente", "index");
		}
		$id = parent::gPost("id");
		$doc = parent::gPost("doc");
		$nombre = parent::gPost("nombre");
		
		$c = Cliente::findFirst("id = $id");
		$cli = Cliente::find("(documento like '$doc' or nombre like '$nombre')
				and id not like $id");
		if(count($cli) > 0){
			parent::msg("El cliente $c->nombre ya existe");
			return parent::forward("cliente", "index");
		}
		$c->alquila = parent::gPost("alquila");
		$c->areaTrab = parent::gPost("area");
		$c->cargo = parent::gPost("cargo");
		$c->direccion = parent::gPost("dir");
		$c->documento = parent::gPost("doc");
		$c->fdesde = parent::gPost("fdesde");
		$c->fexpedicion = parent::gPost("expedicion");
		$c->jefe = parent::gPost("jefe");
		$c->lugarExpedicion = parent::gPost("lugar");
		$c->municipio = parent::gPost("muni");
		$c->trabajo = parent::gPost("trabajo");
		$c->nombre = parent::gPost("nombre");
		if($c->alquila == 1){
			$c->propietario = parent::gPost("propietario");
		}else{
			$c->propietario = $c->nombre;
		}			
		$c->sueldo = parent::gPost("sueldo");
		$c->telOficina = parent::gPost("tofic");
		$c->tipodoc = parent::gPost("tipoDoc");
		if($c->update()){
			parent::msg("Edici&oacute;n exitosa", "s");
			return parent::forward("cliente", "index");
		}else{
			parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
			return parent::forward("cliente", "index");
		}
		
			
		
		
	}
	
	public function deshabilitarAction(){
		/*$id = parent::gReq("id");
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
		}*/
		parent::msg("Esta funci&oacute;n no esta activa en este momento");
		parent::forward("cliente", "index");
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
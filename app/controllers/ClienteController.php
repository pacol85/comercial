<?php
class ClienteController extends ControllerBase
{

	public function indexAction()
	{
		parent::limpiar();
		$sucursal = Sucursal::find("estado = 1");
		$dept = Departamentos::find();
		$data = "";
		foreach ($dept as $d){
			$munis = Municipios::find("departamento = $d->id");
			foreach ($munis as $m){
				$data = $data."$m->nombre, $d->nombre;";
			}			
		}
		$data = substr($data, 0, strlen($data)-1);
		//
		//$d = $dept->getFirst();
		//$muni = Municipios::find("departamento = $d->id");
		//["ls", ["municipio", "municipios('ajax/municipios')"], "Municipio"],
		//["sdb", ["dept", $dept, ["id", "nombre"]], "Departamento"],
		
		$campos = [
				["t", ["nombre"], "Nombre Completo"],
				["t", ["dui"], "DUI"],
				["d", ["expedicion"], "Fecha Expedici&oacute;n"],
				["t", ["lugar"], "Lugar Expedici&oacute;n"],
				["t", ["nit"], "NIT"],
				["t", ["dir"], "Direcci&oacute;n"],
				["t", ["dept"], "Municipio, Departamento"],
				["h", ["deptid"], ""],
				["h", ["listDept"], $data],
				["t", ["cel"], "Celular"],
				["t", ["telCasa"], "Tel&eacute;fono Casa"],
				["sel", ["alquila", ["1" => "S&iacute;", "0" => "No"]], "Alquila"],
				["t", ["propietario"], "Propietario"],
				["t", ["trabajo"], "Trabajo"],
				["t", ["area"], "Area de trabajo"],
				["t", ["jefe"], "Jefe"],
				["t", ["pagador"], "Pagador"],
				["t", ["cargo"], "Cargo"],
				["m", ["sueldo", 0], "Sueldo"],
				["t", ["fdesde"], "Desde"],
				["t", ["tofic"], "Tel&eacute;fono Oficina"],
				["t", ["dirtrab"], "Dir. Trabajo"],
				["h", ["id"], ""],
				["s", [""], "Guardar"]	
		];		
		$form = parent::form($campos, "cliente/guardar", "form1", 2);
		
		$head = ["Nombre", "DUI", "Municipio", "Alquila", "Trabajo",
				"Sueldo", "# Tel", "Acciones"				
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
			$depto = Departamentos::findFirst("id = $m->departamento");
			$tabla = $tabla.parent::tbody([
					$c->nombre,
					$c->dui,
					$m->nombre,
					$alquila,
					$c->trabajo,
					$c->sueldo,
					$c->celular,
					parent::a(2, "cargarDatos('".$c->id."', '".$c->nombre."', '".$c->dui."', '".
							$c->fexpedicion."', '".$c->lugarExpedicion."', '".$c->nit."', '".$c->direccion."', '".
							$m->nombre.", ".$depto->nombre."', '".$c->celular."', '".$c->telcasa."', '".$c->alquila."', '".
							$c->propietario."', '".$c->trabajo."', '".$c->areaTrab."', '".$c->cargo."', '".$c->jefe."', '".
							$c->pagador."', '".$c->fdesde."', '".$c->sueldo."', '".$c->telOficina."', '".$c->dirtrab."')", "Editar")." | ".
					parent::a(1, "credito/index/$c->id", "Cr&eacute;ditos")." | ".
					parent::a(1, "referencia/index2/$c->id/1", "C&oacute;nyugue")." | ".
					parent::a(1, "referencia/index2/$c->id/2", "Familiar")." | ".
					parent::a(1, "referencia/index2/$c->id/3", "Amigos")." | ".
					parent::a(1, "fiador/index/$c->id", "Fiadores")
			]);
		}		
		
		//js
		$fields = ["id", "nombre", "dui", "expedicion", "lugar", "nit", "dir", "dept", "cel", "telCasa", "alquila", 
				"propietario", "trabajo", "area", "cargo", "jefe", "pagador", "fdesde", "sueldo", "tofic", "dirtrab"];
		$otros = "";
		$jsBotones = ["form1", "cliente/edit", "cliente/index"];
		
		parent::view("Clientes", $form, $tabla, [$fields, $otros, $jsBotones]);
	}
	
	public function guardarAction(){
		if(parent::vPost("nombre") && parent::vPost("dui") && parent::vPost("dir") && parent::vPost("trabajo") &&
				parent::vPost("sueldo") && parent::vPost("nit") && parent::vPost("dept")){
			$dui = str_replace("-", "", parent::gPost("dui"));
			$nit = str_replace("-", "", parent::gPost("nit"));
			$nombre = parent::gPost("nombre");
			$cli = Cliente::find("dui like '$dui' or nombre like '$nombre' or nit like '$nit'");
			if(count($cli) > 0){
				parent::msg("Los documentos ingresados equivalen a los de otro cliente ya existente");
				return parent::forward("cliente", "index");
			}
			
			//departamento y municipio
			$mid = $this->dym(parent::gPost("dept"));
						
			//crear nuevo cliente
			$c = new Cliente();
			$c->alquila = parent::gPost("alquila");
			$c->areaTrab = parent::gPost("area");
			$c->cargo = parent::gPost("cargo");
			$c->celular = parent::gPost("cel");
			$c->direccion = parent::gPost("dir");
			$c->dui = $dui;
			$c->estado = 1;
			$c->fdesde = parent::gPost("fdesde");
			$c->fexpedicion = parent::gPost("expedicion");
			$c->jefe = parent::gPost("jefe");
			$c->lugarExpedicion = parent::gPost("lugar");
			$c->municipio = $mid;
			$c->nit = $nit;
			$c->trabajo = parent::gPost("trabajo");
			$c->nombre = parent::gPost("nombre");
			if($c->alquila == 1){
				$c->propietario = parent::gPost("propietario");
			}else{
				$c->propietario = $c->nombre;
			}			
			$c->sueldo = parent::gPost("sueldo");
			$c->telcasa = parent::gPost("telCasa");
			$c->telOficina = parent::gPost("tofic");
			$c->tipodoc = parent::gPost("tipoDoc");
			$c->pagador = parent::gPost("pagador");
			$c->fcreacion = parent::fechaHoy(true);
			$c->dirtrab = parent::gPost("dirtrab");
			if($c->save()){
				parent::msg("El cliente fue creado exitosamente", "s");
				return parent::forward("referencia", "index", [$c->id,"1"]); //primer paso: cónyugue
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
		$dui = str_replace("-", "", parent::gPost("dui"));
		$nit = str_replace("-", "", parent::gPost("nit"));
		$nombre = parent::gPost("nombre");
		
		$c = Cliente::findFirst("id = $id");
		$cli = Cliente::find("(dui like '$dui' or nombre like '$nombre' or nit like '$nit')
				and id not like $id");
		if(count($cli) > 0){
			parent::msg("El cliente $c->nombre ya existe");
			return parent::forward("cliente", "index");
		}
		
		//departamento y municipio
		$mid = $this->dym(parent::gPost("dept"));
		
		$c->alquila = parent::gPost("alquila");
		$c->areaTrab = parent::gPost("area");
		$c->cargo = parent::gPost("cargo");
		$c->celular = parent::gPost("cel");
		$c->direccion = parent::gPost("dir");
		$c->dui = $dui;
		$c->estado = 1;
		if(parent::gPost("fdesde") != "" && parent::gPost("fdesde") != null){
			$c->fdesde = parent::gPost("fdesde");
		}
		if(parent::gPost("expedicion") != "" && parent::gPost("expedicion") != null){
			$c->fexpedicion = parent::gPost("expedicion");
		}		
		$c->jefe = parent::gPost("jefe");
		$c->lugarExpedicion = parent::gPost("lugar");
		$c->municipio = $mid;
		$c->nit = $nit;
		$c->trabajo = parent::gPost("trabajo");
		$c->nombre = parent::gPost("nombre");
		if($c->alquila == 1){
			$c->propietario = parent::gPost("propietario");
		}else{
			$c->propietario = $c->nombre;
		}			
		$c->sueldo = parent::gPost("sueldo");
		$c->telcasa = parent::gPost("telCasa");
		$c->telOficina = parent::gPost("tofic");
		$c->tipodoc = parent::gPost("tipoDoc");
		$c->pagador = parent::gPost("pagador");
		$c->dirtrab = parent::gPost("dirtrab");
		
		if($c->update()){
			parent::msg("Edici&oacute;n exitosa", "s");
			return parent::forward("cliente", "index");
		}else{
			parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
			return parent::forward("cliente", "index");
		}	
	}
	
	/**
	 * dym recibe dept y muni
	 */
	public function dym($dm){//, $muni){
		$md = explode(",", $dm);
		$muni = trim($md[0]);
		$dept = trim($md[1]);
		$mid = "";
		$depts = Departamentos::find("nombre like '$dept'");
		if(count($depts) > 0){
			$d = $depts->getFirst();
			$munis = Municipios::find("departamento = $d->id");
			foreach ($munis as $m){
				if($m->nombre == $muni){
					$mid = $m->id;
				}
			}
			if($mid == ""){
				$municipio2 = new Municipios();
				$municipio2->nombre = $muni;
				$municipio2->departamento = $d->id;
				$municipio2->fcreacion = parent::fechaHoy(true);
				$municipio2->save();
				$mid = $municipio2->id;
			}
		}else{
			$depto = new Departamentos();
			$depto->nombre = $dept;
			$depto->save();
			
			$municipio = new Municipios();
			$municipio->nombre = $muni;
			$municipio->departamento = $depto->id;
			$municipio->fcreacion = parent::fechaHoy(true);
			$municipio->save();
			$mid = $municipio->id;
		}
		return $mid;
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
	
	public function autoDeptAction(){
		$depts = Departamentos::find();
		$data = [];
		$val;
		foreach ($depts as $d){
			array_push($data, ['id' => $d->id, 'nombre' => $d->nombre]);
		}
		return parent::sendJson($data);//json_encode ( $data );
	}

	public function autoMuniAction()
	{
		$dept = parent::gPost("dept");
		$depts = Departamentos::find("nombre like '$dept'");
		$data = "";
		if(count($depts) > 0){
			$d = $depts->getFirst();
			$munis = Municipios::find("departamento = $d->id");
			foreach ($munis as $m){
				$data = $data."$m->nombre,";
			}
			$data = substr($data, 0, strlen($data) -1);
		}
		return parent::sendAjax($data);
	
	}
}
?>
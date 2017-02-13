<?php

class ReferenciaController extends ControllerBase
{

    public function indexAction($cid, $tipoRef)
    {
		parent::limpiar();
		$titulo = "Amigos";
		//campos según tipo
		$campos = [];
		switch ($tipoRef) {
			case 1: //conyugue
			$campos = [
				["l", ["Si no se ingresa nombre y presiona guardar contin&uacute;a sin agregar c&oacute;nyugue"], "Aviso", "texto"],
					["t", ["nombre"], "Nombre"],
					["h", ["id"], ""],
					["t", ["trabajo"], "Trabajo"],
					["m", ["sueldo", 0], "Sueldo"],
					["t", ["area"], "Departamento"],
					["t", ["cargo"], "Cargo"],
					["t", ["jefe"], "Jefe"],
					["t", ["desde"], "Desde"],
					["t", ["tel"], "Tel&eacute;fono"],
					["t", ["dir"], "Dir. Trabajo"],
					["s", ["guardar"], "Guardar"]
			];
			$titulo = "C&oacute;nyugue";
			break;
			case 2: //parientes
				$par = Parentesco::find();
				$campos = [
						["l", ["Si no se ingresa nombre y presiona guardar contin&uacute;a sin agregar Parientes"], "Aviso:", "texto"],
						["t", ["nombre"], "Nombre"],
						["h", ["id"], ""],
						["sdb", ["par", $par, ["id", "parentesco"]], "Parentesco"],
						["t", ["dir"], "Direcci&oacute;n"],
						["t", ["tel"], "Tel&eacute;fono"],
						["t", ["trabajo"], "Trabajo"],
						["t", ["area"], "Departamento"],
						["t", ["cargo"], "Cargo"],
						["t", ["desde"], "Desde"],
						["t", ["telOfic"], "Tel. Oficina"],
						["s", ["guardar"], "Guardar"]
				];
				$titulo = "Parientes";
				break;
			default: //amigos
				$campos = [
				["l", ["Si no se ingresa nombre y presiona guardar contin&uacute;a sin agregar Amigos"], "Aviso:", "texto"],
					["t", ["nombre"], "Nombre"],
					["h", ["id"], ""],
					["t", ["dir"], "Direcci&oacute;n"],
					["t", ["tel"], "Tel&eacute;fono"],
					["t", ["trabajo"], "Trabajo"],
					["t", ["area"], "Departamento"],
					["t", ["cargo"], "Cargo"],
					["t", ["desde"], "Desde"],
					["t", ["telOfic"], "Tel. Oficina"],
					["t", ["ref"], "Referencias"],
					["s", ["guardar"], "Guardar"]
			];
			break;
		}
			
    	$form = parent::form($campos, "referencia/guardarIni/$cid/$tipoRef", "form1");
    	    
    	parent::view($titulo, $form);
    }
    
    public function guardarIniAction($cid, $tipoRef){
    	$next = $tipoRef +1;
    	if(parent::vPost("nombre")){
    		$ref = new Referencia();
    		$ref->areaTrab = parent::gPost("area");
    		$ref->cargo = parent::gPost("cargo");
    		$ref->cliente = $cid;
    		$ref->direccion = parent::gPost("dir");
    		$ref->fcreacion = parent::fechaHoy(true);
    		$ref->jefe = parent::gPost("jefe");
    		$ref->nombre = parent::gPost("nombre");
    		if($tipoRef == 1) {
    			$ref->parentesco = 1;
    		}else {
    			$ref->parentesco = parent::gPost("par");
    		}
    		if($tipoRef == 3){
    			$ref->pariente = 0;
    		}else $ref->pariente = 1;
    		$ref->referencias = parent::gPost("ref");
    		$ref->sueldo = parent::gPost("sueldo");
    		$ref->telefono = parent::gPost("tel");
    		$ref->telOficina = parent::gPost("telOfic");
    		$ref->trabajo = parent::gPost("trabajo");
    		$ref->validez = 0; //no ha sido validado
    		$ref->fdesde = parent::gPost("desde");
    		$ref->fcreacion = parent::fechaHoy(true);
    		
    		if($ref->save()){
    			parent::msg("Referencia creada exitosamente", "s");
    			if($tipoRef >= 3){
    				return parent::forward("fiador", "index", [$cid]);
    			}else{
    				return parent::forward("referencia", "index", [$cid, $next]);
    			}
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operación");
    		}
    	}else{
    		if($tipoRef >= 3){
    			return parent::forward("fiador", "index", [$cid]);
    		}else{    			
    			return parent::forward("referencia", "index", [$cid, $next]);
    		}    		
    	}
    	parent::forward("referencia", "index", [$cid, $tipoRef]);
    }
    
    public function eliminarAction($rid){
    	$ref = Referencia::findFirst("id = $rid");
    	$referencia = $ref->nombre;    		 
    		if($ref->delete()){
    			parent::msg("Se elimin&oacute; la Referencia: $referencia", "s");
    		}else{
    			parent::msg("","db");
    		}	
    	parent::forward("cliente", "index");
    }

    public function editAction(){
    	$tipoRef = parent::gPost("tipoRef");
    	$rid = parent::gPost("id");
    	$ref = Referencia::findFirst("id = $rid");
    	if(parent::vPost("nombre")){    		
    		$ref->areaTrab = parent::gPost("area");
    		$ref->cargo = parent::gPost("cargo");
    		$ref->direccion = parent::gPost("dir");
    		$ref->fcreacion = parent::fechaHoy(true);
    		$ref->jefe = parent::gPost("jefe");
    		$ref->nombre = parent::gPost("nombre");
    		if($tipoRef == 2) {
    			$ref->parentesco = parent::gPost("par");
    		}
    		$ref->referencias = parent::gPost("ref");
    		$ref->sueldo = parent::gPost("sueldo");
    		$ref->telefono = parent::gPost("tel");
    		$ref->telOficina = parent::gPost("telOfic");
    		$ref->trabajo = parent::gPost("trabajo");
    		$ref->validez = 0; //no ha sido validado
    		$ref->fdesde = parent::gPost("desde");
    		$ref->fmod = parent::fechaHoy(true);
    		
    		if($ref->update()){
    			parent::msg("Referencia editada exitosamente", "s");    			
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operación");
    		}
    	}else{
    		parent::msg("El nombre no puede quedar en blanco");    		
    	}
    	parent::forward("referencia", "index2", [$ref->cliente, $tipoRef]);
    }

    public function index2Action($cid, $tipoRef)
    {
    	parent::limpiar();
    	$titulo = "Amigos";
    	
    	$head = ["Nombre", "Direcci&oacute;n", "Tel&eacute;fono", "Trabajo", "Departamento", 
    			"Cargo", "Desde", "Tel. Oficina", "Referencias", "Acciones"];
    	$tabla = parent::thead("treferencia", $head);
    	//js
    	$fields = ["id", "nombre", "dir", "tel", "trabajo", "area", "cargo", "desde", "telOfic", "ref"];
    	$otros = "";
    	$jsBotones = ["form1", "referencia/edit/$cid/$tipoRef", "referencia/index2/$cid/$tipoRef"];
    	
    	//campos según tipo
    	$campos = [];
    	
    	switch ($tipoRef) {
    		case 1: //conyugue
    			$campos = [
    			["t", ["nombre"], "Nombre"],
    			["h", ["id"], ""],
    			["t", ["trabajo"], "Trabajo"],
    			["m", ["sueldo", 0], "Sueldo"],
    			["t", ["area"], "Departamento"],
    			["t", ["cargo"], "Cargo"],
    			["t", ["jefe"], "Jefe"],
    			["t", ["desde"], "Desde"],
    			["t", ["tel"], "Tel&eacute;fono"],
    			["t", ["dir"], "Dir. Trabajo"],
    			["s", ["guardar"], "Guardar"],
    			["h", ["tipoRef"], "1"]
    			];
    			$titulo = "C&oacute;nyugue";
    			
    			$head = ["Nombre", "Trabajo", "Sueldo", "Departamento", "Cargo", 
    					"Jefe", "Desde", "Tel&eacute;fono", "Dir. Trabajo", "Acciones"];
    			$tabla = parent::thead("treferencia", $head);
    			//js
    			$fields = ["id", "nombre", "trabajo", "sueldo", "area", "cargo", 
		   			"jefe", "desde", "tel", "dir"];
    			
    			$referencias = Referencia::find("cliente = $cid and pariente = 1 and parentesco = 1");
    			foreach ($referencias as $r){
    				$tabla = $tabla.parent::tbody([
    						$r->nombre,	$r->trabajo, $r->sueldo,
    						$r->areaTrab, $r->cargo, $r->jefe, 
    						$r->fdesde, $r->telefono, $r->direccion,
    						parent::a(2, "cargarDatos('".$r->id."','".$r->nombre."','".$r->trabajo
    								."','".$r->sueldo."','".$r->areaTrab."','".$r->cargo."','".$r->jefe
    								."','".$r->fdesde."','".$r->telefono."','".$r->direccion."');", "Editar")." | ".
    						parent::a(1, "referencia/eliminar/$r->id", "Eliminar")
    				]);
    			}
    			
    			break;
    		case 2: //parientes
    			$par = Parentesco::find();
    			$campos = [
    					["t", ["nombre"], "Nombre"],
    					["h", ["id"], ""],
    					["sdb", ["par", $par, ["id", "parentesco"]], "Parentesco"],
    					["t", ["dir"], "Direcci&oacute;n"],
    					["t", ["tel"], "Tel&eacute;fono"],
    					["t", ["trabajo"], "Trabajo"],
    					["t", ["area"], "Departamento"],
    					["t", ["cargo"], "Cargo"],
    					["t", ["desde"], "Desde"],
    					["t", ["telOfic"], "Tel. Oficina"],
    					["s", ["guardar"], "Guardar"],
    					["h", ["tipoRef"], "2"]
    			];
    			$titulo = "Parientes";
    			
    			$head = ["Nombre", "Parentesco", "Direcci&oacute;n", "Tel&eacute;fono", 
    					"Trabajo", "Departamento", "Cargo", "Desde", "Tel. Oficina", "Acciones"];
    			$tabla = parent::thead("treferencia", $head);
    			//js
    			$fields = ["id", "nombre", "par", "dir", "tel", "trabajo", "area", "cargo", 
    					"desde", "telOfic"];
    			
    			$referencias = Referencia::find("cliente = $cid and pariente = 1 and parentesco not like 1");
    			foreach ($referencias as $r){
    				$par2 = Parentesco::findFirst("id = $r->parentesco");
    				$tabla = $tabla.parent::tbody([
    						$r->nombre,	$par2->parentesco, $r->direccion, 
    						$r->telefono, $r->trabajo, $r->areaTrab, 
    						$r->cargo, $r->fdesde, $r->telOficina, 
    						parent::a(2, "cargarDatos('".$r->id."','".$r->nombre."','".$r->parentesco."','".$r->direccion
    								."','".$r->telefono."','".$r->trabajo."','".$r->areaTrab."','".$r->cargo
    								."','".$r->fdesde."','".$r->telOficina."');", "Editar")." | ".
    						parent::a(1, "referencia/eliminar/$r->id", "Eliminar")
    				]);
    			}
    			
    			break;
    		default: //amigos
    			$campos = [
    			["t", ["nombre"], "Nombre"],
    			["h", ["id"], ""],
    			["t", ["dir"], "Direcci&oacute;n"],
    			["t", ["tel"], "Tel&eacute;fono"],
    			["t", ["trabajo"], "Trabajo"],
    			["t", ["area"], "Departamento"],
    			["t", ["cargo"], "Cargo"],
    			["t", ["desde"], "Desde"],
    			["t", ["telOfic"], "Tel. Oficina"],
    			["t", ["ref"], "Referencias"],
    			["s", ["guardar"], "Guardar"],
    			["h", ["tipoRef"], "3"]
    			];
    			//$head = ["Nombre", "Trabajo", "Sueldo", "Departamento", "Cargo",
    			//		"Jefe", "Desde", "Tel&eacute;fono", "Dir. Trabajo"];
    			//$fields = ["id", "nombre", "dir", "tel", "trabajo", "area", "cargo", "desde", "telOfic", "ref"];
    			$referencias = Referencia::find("cliente = $cid and pariente = 0");
    			foreach ($referencias as $r){
    				$tabla = $tabla.parent::tbody([
    						$r->nombre,	$r->trabajo, $r->sueldo,
    						$r->areaTrab, $r->cargo, $r->jefe,
    						$r->fdesde, $r->telefono, $r->direccion,
    						parent::a(2, "cargarDatos('".$r->id."','".$r->nombre."','".$r->direccion
    								."','".$r->telefono."','".$r->trabajo."','".$r->areaTrab."','".$r->cargo
    								."','".$r->fdesde."','".$r->telOficina."','".$r->referencias."');", "Editar")." | ".
    						parent::a(1, "referencia/eliminar/$r->id", "Eliminar")
    				]);
    			}
    			break;
    	}

    	$form = parent::form($campos, "referencia/guardar/$cid/$tipoRef", "form1");
    		
    	parent::view($titulo, $form, $tabla, [$fields, $otros, $jsBotones]);
    }
    
    public function guardarAction($cid, $tipoRef){
    	if(parent::vPost("nombre")){
    		$ref = new Referencia();
    		$ref->areaTrab = parent::gPost("area");
    		$ref->cargo = parent::gPost("cargo");
    		$ref->cliente = $cid;
    		$ref->direccion = parent::gPost("dir");
    		$ref->fcreacion = parent::fechaHoy(true);
    		$ref->jefe = parent::gPost("jefe");
    		$ref->nombre = parent::gPost("nombre");
    		if($tipoRef == 1) {
    			$ref->parentesco = 1;
    		}else {
    			$ref->parentesco = parent::gPost("par");
    		}
    		if($tipoRef == 3){
    			$ref->pariente = 0;
    		}else $ref->pariente = 1;
    		$ref->referencias = parent::gPost("ref");
    		$ref->sueldo = parent::gPost("sueldo");
    		$ref->telefono = parent::gPost("tel");
    		$ref->telOficina = parent::gPost("telOfic");
    		$ref->trabajo = parent::gPost("trabajo");
    		$ref->validez = 0; //no ha sido validado
    		$ref->fdesde = parent::gPost("desde");
    		$ref->fcreacion = parent::fechaHoy(true);
    
    		if($ref->save()){
    			parent::msg("Referencia creada exitosamente", "s");    			
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operación");
    		}
    	}else{
    		parent::msg("Como m&iacute;nimo el nombre debe ser incluido");
    	}
    	parent::forward("referencia", "index2", [$cid, $tipoRef]);
    }
    
}
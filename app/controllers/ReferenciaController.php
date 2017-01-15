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
					["d", ["desde"], "Desde"],
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
						["d", ["desde"], "Desde"],
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
					["d", ["desde"], "Desde"],
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
    
    public function eliminarAction(){
    	$par = Parentesco::findFirst("id = ".parent::gReq("id"));
    	$ref = Referencia::find(array("parentesco = $par->id"));
    	if(count($ref) > 0){
    		parent::msg("No se puede eliminar un Parentesco que tenga asociado uno o m&aacute;s Referencias", "w");
    	}else {
    		$parentesco = $par->parentesco;    		 
    		if($par->delete()){
    			parent::msg("Se elimin&oacute; el Parentesco: $parentesco", "s");
    		}else{
    			parent::msg("","db");
    		}
    	}    	
    	parent::forward("parentesco", "index");
    }

    public function editAction(){
    	if(parent::vPost("id")){
    		$par = Parentesco::findFirst("id = ".parent::gPost("id"));
    		$par->parentesco = parent::gPost("parentesco");
    		$par->desc = parent::gPost("desc");
    		if($par->update()){
    			parent::msg("Parentesco modificado exitosamente", "s");
    		}else{
    			parent::msg("", "db");
    		}
    	}else{
    		parent::msg("Ocurri&oacute; un error al cargar el Parentesco");
    	}
    	parent::forward("parentesco", "index");
    }
}

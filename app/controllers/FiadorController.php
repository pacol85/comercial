<?php

class FiadorController extends ControllerBase
{

    public function indexAction($cid)
    {
		parent::limpiar();
		
		$par = Parentesco::find();
		
		$campos = [
			["t", ["nombre"], "Nombre"],
			["h", ["id"], ""],
			["sdb", ["par", $par, ["id", "parentesco"]], "Parentesco"],
			["t", ["dir"], "Direcci&oacute;n"],
			["t", ["tel"], "Tel&eacute;fono"],
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
			["t", ["dui"], "DUI"],
			["t", ["expedido"], "Lugar Expedici&oacute;n"],
			["t", ["nit"], "NIT"],
			["d", ["fexpedicion"], "Fecha Expedici&oacute;n"],
			["t", ["conyugue"], "Nombre C&oacute;nyugue"],
			["t", ["ctrabajo"], "Trabajo"],
			["t", ["carea"], "Area de trabajo"],
			["t", ["ccargo"], "Cargo"],
			["t", ["cfdesde"], "Desde"],
			["t", ["ctofic"], "Tel&eacute;fono Oficina"],
			["t", ["cdirtrab"], "Dir. Trabajo"],
			["s", ["guardar"], "Guardar"]
		];
		$form = parent::form($campos, "fiador/guardarIni/$cid", "form1", 2);

		$head = ["Principal", "Nombre", "Tel&eacute;fono", "Sueldo", "DUI", "NIT", "Acciones"];
		$tabla = parent::thead("tfiadores", $head);
		$fiadores = Fiador::find("cliente = $cid");
		foreach ($fiadores as $f){
			$principal = parent::a(1, "fiador/principal/$f->id", "Principal");
			$main = "";
			if($f->principal == 1){
				$principal = "";
				$main = "***";
			}
			
			$tabla = $tabla.parent::tbody([
					$main,
					$f->nombre,
					$f->telefono,
					$f->sueldo,
					$f->dui,
					$f->nit,
					$principal. " | " . parent::a(1, "fiador/edit/$f->id", "Editar")
			]);
		}
		
		//js
		$fields = ["id", "nombre", "par", "dir", "tel", "alquila", "propietario", 
				"trabajo", "area", "jefe", "pagador", "cargo", "sueldo", "fdesde", 
				"tofic", "dirtrab", "dui", "expedido", "nit", "fexpedicion", "conyugue", 
				"ctrabajo", "carea", "ccargo", "cfdesde", "ctofic", "cdirtrab"];
		$otros = "";
		//$jsBotones = ["form1", "credito/edit/$cid", "credito/cargar/$cid"];
		
    	parent::view("Fiador", $form, $tabla);
    }
    
    public function guardarIniAction($cid){
    	if(parent::vPost("nombre")){
    		$dui = str_replace("-", "", parent::gPost("dui"));
    		$nit = str_replace("-", "", parent::gPost("nit"));
    		$f = new Fiador();
    		$f->alquila = parent::gPost("alquila");
    		$f->cargo = parent::gPost("cargo");
    		$f->depto = parent::gPost("area");
    		$f->desde = parent::gPost("fdesde");
    		$f->direccion = parent::gPost("dir");
    		$f->dirtrab = parent::gPost("dirtrab");
    		$f->dui = $dui;
    		$f->expedicion = parent::gPost("expedido");
    		$f->fcreacion = parent::fechaHoy(true);
    		$f->fexpedicion = parent::gPost("fexpedicion");
    		$f->jefe = parent::gPost("jefe");
    		$f->nit = $nit;
    		$f->nombre = parent::gPost("nombre");
    		$f->pagador = parent::gPost("pagador");
    		$f->parentesco = parent::gPost("par");
    		$f->propietario = parent::gPost("propietario");
    		$f->sueldo = parent::gPost("sueldo");
    		$f->telefono = parent::gPost("tel");
    		$f->telofic = parent::gPost("tofic");
    		$f->trabajo = parent::gPost("trabajo");
    		
    		//información del cónyugue
    		$f->ccargo = parent::gPost("ccargo");
    		$f->cdepto = parent::gPost("carea");
    		$f->cdesde = parent::gPost("cfdesde");
    		$f->cdirtrab = parent::gPost("cdirtrab");
    		$f->conyugue = parent::gPost("conyugue");
    		$f->ctelefono = parent::gPost("ctofic");
    		$f->ctrabajo = parent::gPost("ctrabajo");
    		
    		$f->cliente = $cid;
    		
    		//Verificar si hay prestamo activo a su nombre o si existe ya como fiador de alguien más
    		$mensaje = "";
    		$fiadoresA = Fiador::find("dui like '$f->dui' or nombre like '$f->nombre' and aprobado = 1");
    		if(count($fiadoresA) > 0){
    			$fa = $fiadoresA->getFirst();
    			$cred = CreditoXCliente::find("fiador = $fa->id");
    			if(count($cred) > 0){
    				$ca = $cred->getFirst();
    				$mensaje = "Posible duplicado, existe como fiador aprobado cuenta $ca->cuenta. ";
    			}
    			
    		}
    		$fiadoresD = Fiador::find("dui like '$f->dui' or nombre like '$f->nombre' and aprobado = 2");
    		if(count($fiadoresD) > 0){
    			$mensaje = $mensaje. "Este fiador ya hab&iacute;a sido denegado anteriormente. ";
    		}
    		
    		$clientes = Cliente::find("dui like '$f->dui' or nombre like '$f->nombre'");
    		if(count($clientes) > 0){
    			$cli = $clientes->getFirst();
    			$credC = CreditoXCliente::find("cliente = $cli->id and fecha_cancelacion is not null");
    			if(count($credC) > 0){
    				$cc = $credC->getFirst();
    				$mensaje = $mensaje."Existe como cliente con cuenta vigente: $ca->cuenta. ";
    			}
    		}
    		
    		if($f->save()){
    			parent::msg("Fiador creado exitosamente", "s");
    			parent::msg($mensaje, "w");
    			return parent::forward("credito", "fullproc", [$cid, $f->id]);
    			
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operación");
    		}
    	}else{
    		parent::msg("Se continu&oacute; sin agregar Fiador");
    		return parent::forward("credito", "index", [$cid]);    		
    	}
    	parent::forward("fiador", "index", [$cid]);
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

    public function editAction($fid){
    	$fiador = Fiador::findFirst($fid);
    	$par = Parentesco::find();
		
		$campos = [
			["t", ["nombre"], "Nombre"],
			["h", ["id"], ""],
			["sdb", ["par", $par, ["id", "parentesco"]], "Parentesco"],
			["t", ["dir"], "Direcci&oacute;n"],
			["t", ["tel"], "Tel&eacute;fono"],
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
			["t", ["dui"], "DUI"],
			["t", ["expedido"], "Lugar Expedici&oacute;n"],
			["t", ["nit"], "NIT"],
			["d", ["fexpedicion"], "Fecha Expedici&oacute;n"],
			["t", ["conyugue"], "Nombre C&oacute;nyugue"],
			["t", ["ctrabajo"], "Trabajo"],
			["t", ["carea"], "Area de trabajo"],
			["t", ["ccargo"], "Cargo"],
			["t", ["cfdesde"], "Desde"],
			["t", ["ctofic"], "Tel&eacute;fono Oficina"],
			["t", ["cdirtrab"], "Dir. Trabajo"],
			["s", ["guardar"], "Guardar"]
		];
		$form = parent::form($campos, "fiador/guardarIni/$cid", "form1", 2);
    }
    
    
}

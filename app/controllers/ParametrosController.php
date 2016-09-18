<?php

class ParametrosController extends ControllerBase
{

    public function indexAction()
    {
		parent::limpiar();
    	$campos = [
				["h", ["id"], ""],
				["t", ["parametro"], "Par&aacute;metro"],
				["t", ["valor"], "Valor"],
    			["t", ["desc"], "Descripci&oacute;n"],
    			["s", ["guardar"], "Guardar"]
		];
		$head = ["Par&aacute;metro", "Valor", "Descripci&oacute;n", "Fecha modificaci&oacute;n", "Acciones"];
		$tabla = parent::thead("parametros", $head);
		$parametros = Parametros::find();
		foreach ($parametros as $r){
			$tabla = $tabla.parent::tbody([
					$r->parametro,
					$r->valor,
					$r->descripcion,
					$r->fmod,
					parent::a(2, "cargarDatos('".$r->id."','".$r->parametro."','".$r->valor."','".$r->descripcion."');", "Editar")." | ".
					parent::a(1, "parametros/eliminar", "Eliminar", [["id", $r->id]])
			]);
		}
		
		//js
		$fields = ["id", "parametro", "valor", "desc"];
		$otros = "";
		$jsBotones = ["form1", "parametros/edit", "parametros"];
		
    	$form = parent::form($campos, "parametros/guardar", "form1");
    	$tabla = parent::ftable($tabla);
    
    	parent::view("Parametros", $form, $tabla, [$fields, $otros, $jsBotones]);
    }
    
    public function guardarAction(){
    	if(parent::vPost("parametro")){
    		$parametros = new Parametros();
    		$parametros->parametro = parent::gPost("parametro");
    		$parametros->valor = parent::gPost("valor");
    		$parametros->descripcion = parent::gPost("desc");
    		$parametros->fmod = parent::fechaHoy(true);
    		if($parametros->save()){
    			parent::msg("Par&aacute;metro creado exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operación");
    		}
    	}else{
    		parent::msg("El campo par&aacute;metro no puede quedar en blanco");
    	}
    	parent::forward("parametros", "index");
    }
    
    public function eliminarAction(){
    	$parametros = Parametros::findFirst("id = ".parent::gReq("id"));
    	$nParametros = $parametros->parametro;    		 
    	if($parametros->delete()){
    		parent::msg("Se elimin&oacute; el par&aacute;metro: $nParametros", "s");
    	}else{
    		parent::msg("","db");
    	}    	
    	parent::forward("parametros", "index");
    }

    public function editAction(){
    	if(parent::vPost("id")){
    		$parametros = Parametros::findFirst("id = ".parent::gPost("id"));
    		$parametros->parametro = parent::gPost("parametro");
    		$parametros->valor = parent::gPost("valor");
    		$parametros->descripcion = parent::gPost("desc");
    		$parametros->fmod = parent::fechaHoy(true);
    		if($parametros->update()){
    			parent::msg("Par&aacute;metro modificado exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operaci&oacute;n");
    		}
    	}else{
    		parent::msg("Ocurri&oacute; un error al cargar el Par&aacute;metro");
    	}
    	parent::forward("parametros", "index");
    }
}

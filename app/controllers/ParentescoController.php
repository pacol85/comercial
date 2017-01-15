<?php

class ParentescoController extends ControllerBase
{

    public function indexAction()
    {
		parent::limpiar();
    	$campos = [
				["t", ["parentesco"], "Parentesco"],
				["h", ["id"], ""],
				["t", ["desc"], "Descripci&oacute;n"],
				["s", ["guardar"], "Guardar"]
		];
		$head = ["Parentesco", "Descripci&oacute;n", "Acciones"];
		$tabla = parent::thead("tparentesco", $head);
		$parentesco = Parentesco::find();
		foreach ($parentesco as $p){
			$tabla = $tabla.parent::tbody([
					$p->parentesco,
					$p->desc,
					parent::a(2, "cargarDatos('".$p->id."','".$p->parentesco."','".$p->desc."');", "Editar")." | ".
					parent::a(1, "parentesco/eliminar", "Eliminar", [["id", $p->id]])
			]);
		}
		
		//js
		$fields = ["id", "parentesco", "desc"];
		$otros = "";
		$jsBotones = ["form1", "parentesco/edit", "parentesco"];
		
    	$form = parent::form($campos, "parentesco/guardar", "form1");
    	    
    	parent::view("Parentesco", $form, $tabla, [$fields, $otros, $jsBotones]);
    }
    
    public function guardarAction(){
    	if(parent::vPost("parentesco")){
    		$par = new Parentesco();
    		$par->parentesco = parent::gPost("parentesco");
    		$par->desc = parent::gPost("desc");
    		if($par->save()){
    			parent::msg("Parentesco creado exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operación");
    		}
    	}else{
    		parent::msg("El campo Parentesco no puede quedar en blanco");
    	}
    	parent::forward("parentesco", "index");
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

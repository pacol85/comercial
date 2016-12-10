<?php

class EstadoclienteController extends ControllerBase
{

    public function indexAction()
    {
		parent::limpiar();
    	$campos = [
				["t", ["estado"], "Estado"],
				["h", ["id"], ""],
				["t", ["desc"], "Descripci&oacute;n"],
				["s", ["guardar"], "Guardar"]
		];
		$head = ["Estado", "Descripci&oacute;n", "Acciones"];
		$tabla = parent::thead("estadoCliente", $head);
		$estadoCliente = EstadoCliente::find();
		foreach ($estadoCliente as $r){
			$tabla = $tabla.parent::tbody([
					$r->estado,
					$r->descripcion,
					parent::a(2, "cargarDatos('".$r->id."','".$r->estado."','".$r->descripcion."');", "Editar")." | ".
					parent::a(1, "estadocliente/eliminar", "Eliminar", [["id", $r->id]])
			]);
		}
		
		//js
		$fields = ["id", "estado", "desc"];
		$otros = "";
		$jsBotones = ["form1", "estadocliente/edit", "estadoCliente"];
		
    	$form = parent::form($campos, "estadocliente/guardar", "form1");
    	$tabla = parent::ftable($tabla);
    
    	parent::view("Estado de cliente", $form, $tabla, [$fields, $otros, $jsBotones]);
    }
    
    public function guardarAction(){
    	if(parent::vPost("estado")){
    		$estadoCliente = new EstadoCliente();
    		$estadoCliente->estado = parent::gPost("estado");
    		$estadoCliente->descripcion = parent::gPost("desc");
    		if($estadoCliente->save()){
    			parent::msg("Estado de cliente creado exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operación");
    		}
    	}else{
    		parent::msg("El campo estado no puede quedar en blanco");
    	}
    	parent::forward("estadocliente", "index");
    }
    
    public function eliminarAction(){
    	$estadoCliente = EstadoCliente::findFirst("id = ".parent::gReq("id"));
    	$clientes = Cliente::find(array("estado = $estadoCliente->id"));
    	if(count($clientes) > 0){
    		parent::msg("No se puede eliminar un Estado de cliente que tenga asociado uno o m&aacute;s clientes", "w");
    	}else {
    		$nEstadoCliente = $estadoCliente->estado;    		 
    		if($estadoCliente->delete()){
    			parent::msg("Se elimin&oacute; el Estado de cliente: $nEstadoCliente", "s");
    		}else{
    			parent::msg("","db");
    		}
    	}    	
    	parent::forward("estadocliente", "index");
    }

    public function editAction(){
    	if(parent::vPost("id")){
    		$estadoCliente = EstadoCliente::findFirst("id = ".parent::gPost("id"));
    		$estadoCliente->estado = parent::gPost("estado");
    		$estadoCliente->descripcion = parent::gPost("desc");
    		if($estadoCliente->update()){
    			parent::msg("Estado de cliente modificada exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operaci&oacute;n");
    		}
    	}else{
    		parent::msg("Ocurri&oacute; un error al cargar el Estado de cliente");
    	}
    	parent::forward("estadocliente", "index");
    }
}


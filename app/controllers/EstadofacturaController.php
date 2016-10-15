<?php

class EstadofacturaController extends ControllerBase
{

    public function indexAction()
    {
		parent::limpiar();
    	$campos = [
				["t", ["nombre"], "Nombre"],
				["h", ["id"], ""],
				["t", ["desc"], "Descripci&oacute;n"],
				["s", ["guardar"], "Guardar"]
		];
		$head = ["Nombre", "Descripci&oacute;n", "Acciones"];
		$tabla = parent::thead("estadoFactura", $head);
		$estadoFactura = EstadoFactura::find();
		foreach ($estadoFactura as $r){
			$tabla = $tabla.parent::tbody([
					$r->nombre,
					$r->descripcion,
					parent::a(2, "cargarDatos('".$r->id."','".$r->nombre."','".$r->descripcion."');", "Editar")." | ".
					parent::a(1, "estadoFactura/eliminar", "Eliminar", [["id", $r->id]])
			]);
		}
		
		//js
		$fields = ["id", "nombre", "desc"];
		$otros = "";
		$jsBotones = ["form1", "estadoFactura/edit", "estadoFactura"];
		
    	$form = parent::form($campos, "estadoFactura/guardar", "form1");
    	$tabla = parent::ftable($tabla);
    
    	parent::view("Estado de factura", $form, $tabla, [$fields, $otros, $jsBotones]);
    }
    
    public function guardarAction(){
    	if(parent::vPost("nombre")){
    		$estadoFactura = new EstadoFactura();
    		$estadoFactura->nombre = parent::gPost("nombre");
    		$estadoFactura->descripcion = parent::gPost("desc");
    		if($estadoFactura->save()){
    			parent::msg("Estado de factura creado exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operación");
    		}
    	}else{
    		parent::msg("El campo nombre no puede quedar en blanco");
    	}
    	parent::forward("estadoFactura", "index");
    }
    
    public function eliminarAction(){
    	$estadoFactura = EstadoFactura::findFirst("id = ".parent::gReq("id"));
    	$facturas = Factura::find(array("estado = $estadoFactura->id"));
    	if(count($facturas) > 0){
    		parent::msg("No se puede eliminar un Estado de factura que tenga asociado uno o m&aacute;s facturas", "w");
    	}else {
    		$nEstadoFactura = $estadoFactura->nombre;    		 
    		if($estadoFactura->delete()){
    			parent::msg("Se elimin&oacute; el Rol: $nEstadoFactura", "s");
    		}else{
    			parent::msg("","db");
    		}
    	}    	
    	parent::forward("estadoFactura", "index");
    }

    public function editAction(){
    	if(parent::vPost("id")){
    		$estadoFactura = EstadoFactura::findFirst("id = ".parent::gPost("id"));
    		$estadoFactura->nombre = parent::gPost("nombre");
    		$estadoFactura->descripcion = parent::gPost("desc");
    		if($estadoFactura->update()){
    			parent::msg("Estado de factura modificada exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operaci&oacute;n");
    		}
    	}else{
    		parent::msg("Ocurri&oacute; un error al cargar el Estado de factura");
    	}
    	parent::forward("estadoFactura", "index");
    }
}

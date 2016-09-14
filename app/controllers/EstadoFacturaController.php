<?php

class EstadoFacturaController extends ControllerBase
{

    public function indexAction()
    {
		$campos = [
				["t", ["nombre"], "Nombre"],
				["t", ["desc"], "Descripci&oacute;n"],
				["s", ["guardar"], "Guardar"]
		];
		$head = ["Nombre", "Descripci&oacute;n", "Modificaci&oacute;n", "Acciones"];
		$tabla = parent::thead("estadoFactura", $head);
		$estadoFactura = EstadoFactura::find();
		foreach ($estadoFactura as $r){
			$tabla = $tabla.parent::tbody([
					$r->nombre,
					$r->descripcion,
					parent::a(1, "estadoFactura/eliminar", "Eliminar", [["id", $r->id]])
			]);
		}
    	$this->view->titulo = parent::elemento("h1", ["titulo"], "Estado de factura");
    	$this->view->form = parent::form($campos, "estadoFactura/guardar", "form1");
    	$this->view->tabla = parent::ftable($tabla);
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

}


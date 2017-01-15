<?php

class TipopagoController extends ControllerBase
{

    public function indexAction()
    {
		parent::limpiar();
    	$campos = [
				["t", ["tipo"], "Tipo Pago"],
				["h", ["id"], ""],
				["t", ["desc"], "Descripci&oacute;n"],
				["s", ["guardar"], "Guardar"]
		];
		$head = ["Nombre", "Descripci&oacute;n", "Acciones"];
		$tabla = parent::thead("tipoPago", $head);
		$tpago = TipoPago::find();
		foreach ($tpago as $t){
			$tabla = $tabla.parent::tbody([
					$t->tipo,
					$t->descripcion,
					parent::a(2, "cargarDatos('".$t->id."','".$t->tipo."','".$t->descripcion."');", "Editar")." | ".
					parent::a(1, "tipoPago/eliminar", "Eliminar", [["id", $t->id]])
			]);
		}
		
		//js
		$fields = ["id", "tipo", "desc"];
		$otros = "";
		$jsBotones = ["form1", "tipoPago/edit", "tipoPago"];
		
    	$form = parent::form($campos, "tipoPago/guardar", "form1");
    	$tabla = parent::ftable($tabla);
    
    	parent::view("Tipo de Pago", $form, $tabla, [$fields, $otros, $jsBotones]);
    }
    
    public function guardarAction(){
    	if(parent::vPost("tipo")){
    		$tpago = new TipoPago();
    		$tpago->tipo = parent::gPost("tipo");
    		$tpago->descripcion = parent::gPost("desc");
    		if($tpago->save()){
    			parent::msg("Tipo de Pago creado exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operación");
    		}
    	}else{
    		parent::msg("El campo tipo no puede quedar en blanco");
    	}
    	parent::forward("tipoPago", "index");
    }
    
    public function eliminarAction(){
    	$tpago = TipoPago::findFirst("id = ".parent::gReq("id"));
    	$orden = OrdenCompra::find(array("tipopago = $tpago->id"));
    	if(count($orden) > 0){
    		parent::msg("No se puede eliminar un Tipo de Pago que tenga asociado uno o m&aacute;s &Oacute;rdenes de Compra", "w");
    	}else {
    		$tipo = $tpago->tipo;    		 
    		if($tpago->delete()){
    			parent::msg("Se elimin&oacute; el Tipo: $tipo", "s");
    		}else{
    			parent::msg("","db");
    		}
    	}    	
    	parent::forward("tipoPago", "index");
    }

    public function editAction(){
    	if(parent::vPost("id")){
    		$tpago = TipoPago::findFirst("id = ".parent::gPost("id"));
    		$tpago->tipo = parent::gPost("tipo");
    		$tpago->descripcion = parent::gPost("desc");
    		if($tpago->update()){
    			parent::msg("Tipo de Pago modificado exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operaci&oacute;n");
    		}
    	}else{
    		parent::msg("Ocurri&oacute; un error al cargar el Tipo de Pago");
    	}
    	parent::forward("tipoPago", "index");
    }
}

<?php

class TipoproveedorController extends ControllerBase
{

    public function indexAction()
    {
		parent::limpiar();
    	$campos = [
				["t", ["tipo"], "Tipo"],
				["h", ["id"], ""],
				["t", ["desc"], "Descripci&oacute;n"],
				["s", ["guardar"], "Guardar"]
		];
		$head = ["Tipo", "Descripci&oacute;n", "Acciones"];
		$tabla = parent::thead("tipoProveedor", $head);
		$tipoProveedor = TipoProveedor::find();
		foreach ($tipoProveedor as $r){
			$tabla = $tabla.parent::tbody([
					$r->tipo,
					$r->descripcion,
					parent::a(2, "cargarDatos('".$r->id."','".$r->tipo."','".$r->descripcion."');", "Editar")." | ".
					parent::a(1, "tipoProveedor/eliminar", "Eliminar", [["id", $r->id]])
			]);
		}
		
		//js
		$fields = ["id", "tipo", "desc"];
		$otros = "";
		$jsBotones = ["form1", "tipoProveedor/edit", "tipoProveedor"];
		
    	$form = parent::form($campos, "tipoProveedor/guardar", "form1");
    	$tabla = parent::ftable($tabla);
    
    	parent::view("Tipo de proveedor", $form, $tabla, [$fields, $otros, $jsBotones]);
    }
    
    public function guardarAction(){
    	if(parent::vPost("tipo")){
    		$tipoProveedor = new TipoProveedor();
    		$tipoProveedor->tipo = parent::gPost("tipo");
    		$tipoProveedor->descripcion = parent::gPost("desc");
    		if($tipoProveedor->save()){
    			parent::msg("Tipo de proveedor creado exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operación");
    		}
    	}else{
    		parent::msg("El campo tipo no puede quedar en blanco");
    	}
    	parent::forward("tipoProveedor", "index");
    }
    
    public function eliminarAction(){
    	$tipoProveedor = TipoProveedor::findFirst("id = ".parent::gReq("id"));
    	$proveedores = Proveedor::find(array("tipo = $tipoProveedor->id"));
    	if(count($proveedores) > 0){
    		parent::msg("No se puede eliminar un Tipo de proveedor que tenga asociado uno o m&aacute;s proveedores", "w");
    	}else {
    		$nTipoProveedor = $tipoProveedor->tipo;    		 
    		if($tipoProveedor->delete()){
    			parent::msg("Se elimin&oacute; el Tipo de proveedor: $nTipoProveedor", "s");
    		}else{
    			parent::msg("","db");
    		}
    	}    	
    	parent::forward("tipoProveedor", "index");
    }

    public function editAction(){
    	if(parent::vPost("id")){
    		$tipoProveedor = TipoProveedor::findFirst("id = ".parent::gPost("id"));
    		$tipoProveedor->tipo = parent::gPost("tipo");
    		$tipoProveedor->descripcion = parent::gPost("desc");
    		if($tipoProveedor->update()){
    			parent::msg("Tipo de proveedor modificada exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operaci&oacute;n");
    		}
    	}else{
    		parent::msg("Ocurri&oacute; un error al cargar el Tipo de proveedor");
    	}
    	parent::forward("tipoProveedor", "index");
    }
}


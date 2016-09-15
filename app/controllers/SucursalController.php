<?php

class SucursalController extends ControllerBase
{

    public function indexAction()
    {
		parent::limpiar();
    	$campos = [
				["t", ["nombre"], "Nombre"],
				["t", ["dir"], "Direcci&oacute;n"],
    			["t", ["tel"], "Tel&eacute;fono"],
    			["t", ["fax"], "Fax"],
				["e", ["email"], "eMail"],
    			["h", ["id"], ""],
				["s", ["guardar"], "Guardar"]
		];
		
		$head = ["Nombre", "Tel&eacute;fono", "Fax", "eMail", "Acciones"];
		$tabla = parent::thead("sucursales", $head);
		$suc = Sucursal::find();
		foreach ($suc as $s){
			$tabla = $tabla.parent::tbody([
					$s->nombre,
					$s->telefono, 
					$s->fax, 
					$s->email,
					parent::a(2, "cargarDatos('".$s->id."','".$s->nombre."','".$s->direccion."','".
							$s->telefono."','".$s->fax."','".$s->email."');", "Modificar")." | ".
					parent::a(1, "sucursal/eliminar", "Eliminar", [["id", $s->id]])
			]);
		}
		
		//js
		$fields = ["id", "nombre", "dir", "tel", "fax", "email"];
		$otros = "";
		$jsBotones = ["form1", "sucursal/editar", "sucursal/index"];
		
    	$form = parent::form($campos, "sucursal/guardar", "form1");
    	$tabla = parent::ftable($tabla);
    	parent::view("Sucursal", $form, $tabla, [$fields, $otros, $jsBotones]);
    }
    
    public function guardarAction(){
    	if(parent::vPost("nombre") && parent::vPost("dir")){
    		$s = new Sucursal();
    		$s->direccion = parent::gPost("dir");
    		$s->email = parent::gPost("email");
    		$s->empresa = 1;
    		$s->fax = parent::gPost("fax");
    		$s->nombre = parent::gPost("nombre");
    		$s->telefono = parent::gPost("tel");
    		$s->estado = 1;
    		if($s->save()){
    			parent::msg("Sucursal creada exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operaci&oacute;n");
    		}
    	}else{
    		parent::msg("Nombre y Direcci&oacute;n no pueden quedar en blanco");
    	}
    	parent::forward("sucursal", "index");
    }
    
    public function eliminarAction(){
    	$suc = Sucursal::findFirst("id = ".parent::gReq("id"));
    	$emp = Empleado::find("id = $suc->id");
    	if(count($emp) > 0){
    		parent::msg("No se puede eliminar una Sucursal que est&aacute; en uso");
    		parent::forward("sucursal", "index");
    	}else{
    		if($suc->delete()){
    			parent::msg("Se elimin&oacute; correctamente la Sucursal", "s");		
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operaci&oacute;n");
    		}
    	}    		    	
    	parent::forward("sucursal", "index");
    }
    
    public function editarAction(){
    	if(parent::vPost("id")){
    		$s = Sucursal::findFirst("id = ".parent::gPost("id"));
    		$s->direccion = parent::gPost("dir");
    		$s->email = parent::gPost("email");
    		$s->empresa = 1;
    		$s->fax = parent::gPost("fax");
    		$s->nombre = parent::gPost("nombre");
    		$s->telefono = parent::gPost("tel");
    		if($s->update()){
    			parent::msg("Sucursal modificada exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operaci&oacute;n");
    		}
    	}else{
    		parent::msg("Ocurri&oacute; un error al cargar la Sucursal");
    	}
    	parent::forward("sucursal", "index");
    }

}


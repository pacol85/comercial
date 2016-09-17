<?php

class DepartamentosController extends ControllerBase
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
		$tabla = parent::thead("departamentos", $head);
		$departamentos = Departamentos::find();
		foreach ($departamentos as $r){
			$tabla = $tabla.parent::tbody([
					$r->nombre,
					$r->descripcion,
					parent::a(2, "cargarDatos('".$r->id."','".$r->nombre."','".$r->descripcion."');", "Editar")." | ".
					parent::a(1, "departamentos/eliminar", "Eliminar", [["id", $r->id]])
			]);
		}
		
		//js
		$fields = ["id", "nombre", "desc"];
		$otros = "";
		$jsBotones = ["form1", "departamentos/edit", "departamentos"];
		
    	$form = parent::form($campos, "departamentos/guardar", "form1");
    	$tabla = parent::ftable($tabla);
    
    	parent::view("Departamentos", $form, $tabla, [$fields, $otros, $jsBotones]);
    }
    
    public function guardarAction(){
    	if(parent::vPost("nombre")){
    		$departamentos = new Departamentos();
    		$departamentos->nombre = parent::gPost("nombre");
    		$departamentos->descripcion = parent::gPost("desc");
    		if($departamentos->save()){
    			parent::msg("Departamentos creado exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operación");
    		}
    	}else{
    		parent::msg("El campo nombre no puede quedar en blanco");
    	}
    	parent::forward("departamentos", "index");
    }
    
    public function eliminarAction(){
    	$departamentos = Departamentos::findFirst("id = ".parent::gReq("id"));
    	$municipios = Municipios::find(array("departamento = $departamentos->id"));
    	if(count($municipios) > 0){
    		parent::msg("No se puede eliminar un Departamento que tenga asociado uno o m&aacute;s municipios", "w");
    	}else {
    		$nDepartamentos = $departamentos->nombre;    		 
    		if($departamentos->delete()){
    			parent::msg("Se elimin&oacute; el departamento: $nDepartamentos", "s");
    		}else{
    			parent::msg("","db");
    		}
    	}    	
    	parent::forward("departamentos", "index");
    }

    public function editAction(){
    	if(parent::vPost("id")){
    		$departamentos = Departamentos::findFirst("id = ".parent::gPost("id"));
    		$departamentos->nombre = parent::gPost("nombre");
    		$departamentos->descripcion = parent::gPost("desc");
    		if($departamentos->update()){
    			parent::msg("Departamento modificado exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operaci&oacute;n");
    		}
    	}else{
    		parent::msg("Ocurri&oacute; un error al cargar el Departamento");
    	}
    	parent::forward("departamentos", "index");
    }
}

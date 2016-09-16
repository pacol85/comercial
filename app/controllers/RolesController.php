<?php

class RolesController extends ControllerBase
{

    public function indexAction()
    {
		$campos = [
				["t", ["rol"], "Rol"],
				["t", ["desc"], "Descripci&oacute;n"],
				["s", ["guardar"], "Guardar"]
		];
		$head = ["Rol", "Descripci&oacute;n", "Modificaci&oacute;n", "Acciones"];
		$tabla = parent::thead("roles", $head);
		$roles = Roles::find();
		foreach ($roles as $r){
			$tabla = $tabla.parent::tbody([
					$r->rol,
					$r->descripcion, 
					$r->fmod,
					parent::a(1, "roles/eliminar", "Eliminar", [["id", $r->id]])
			]);
		}
    	$this->view->titulo = parent::elemento("h1", ["titulo"], "Roles");
    	$this->view->form = parent::form($campos, "roles/guardar", "form1");
    	$this->view->tabla = parent::ftable($tabla);
    }
    
    public function guardarAction(){
    	if(parent::vPost("rol")){
    		$rol = new Roles();
    		$rol->rol = parent::gPost("rol");
    		$rol->descripcion = parent::gPost("desc");
    		$rol->fcreacion = parent::fechaHoy(true);
    		$rol->fmod = parent::fechaHoy(true);
    		if($rol->save()){
    			parent::msg("Rol creado exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operación");
    		}
    	}else{
    		parent::msg("El campo de Rol no puede quedar en blanco");
    	}
    	parent::forward("roles", "index");
    }
    
    public function eliminarAction(){
    	$rol = Roles::findFirst("id = ".parent::gReq("id"));
    	$menus = MenuXRol::find(array("rol = $rol->id"));
    	if(count($menus) > 0){
    		parent::msg("No se puede eliminar un Rol que tenga asociado uno o m&aacute;s men&uacute;s", "w");
    		return parent::forward("roles", "index");
    	}
    	$user = Usuario::find("rol_id = $rol->id");
    	if(count($user) > 0){
    		parent::msg("No se puede eliminar un Rol que tenga asociado uno o m&aacute;s Usuarios", "w");
    		return parent::forward("roles", "index");
    	}
    	$nrol = $rol->rol;    		 
    	if($rol->delete()){
    		parent::msg("Se elimin&oacute; el Rol: $nrol", "s");
    	}else{
    		parent::msg("Ocurri&oacute; un error durante la operación");
    	}
    	    	
    	parent::forward("roles", "index");
    }

}


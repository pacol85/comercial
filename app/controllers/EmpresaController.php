<?php

class EmpresaController extends ControllerBase
{

    public function indexAction()
    {
		$empresa = Empresa::find();
		if(count($empresa) < 1){
			$suc = Sucursal::find();
			$campos = [
					["t", ["nombre"], "Nombre"],
					["sdb", ["matriz", $suc, ["id", "nombre"]], "Casa Matriz"],
					["h", ["id"], ""],
					["s", ["guardar"], "Guardar"]
			];
			$form = parent::form($campos, "empresa/guardar", "form1");
			parent::view("Empresa", $form);
		}else{
			foreach ($empresa as $e){
				$suc = Sucursal::find();
				$sdb = ["sdb", ["matriz", $suc, ["id", "nombre"], $e->matriz], "Casa Matriz"];
				if($e->matriz == null || $e->matriz == ""){
					$sdb = ["sdb", ["matriz", $suc, ["id", "nombre"]], "Casa Matriz"];
				}
				$campos = [
						["tv", ["nombre", $e->nombre], "Nombre"],
						$sdb,
						["h", ["id"], $e->id],
						["s", ["guardar"], "Guardar"]
				];
				$form = parent::form($campos, "empresa/editar", "form1");
				parent::view("Empresa", $form);
			}
		}		
    }
    
    public function guardarAction(){
    	if(parent::vPost("nombre")){
    		$e = new Empresa();
    		$e->nombre = parent::gPost("nombre");
    		$e->matriz = parent::gPost("matriz");
    		if($e->matriz == "") $e->matriz = null;
    		if($e->save()){
    			parent::msg("Empresa creada exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operaci&oacute;n");
    		}
    	}else{
    		parent::msg("El campo de Nombre no puede quedar en blanco");
    	}
    	parent::forward("empresa", "index");
    }
    
    public function editarAction(){
    	if(parent::vPost("nombre")){
    		$id = parent::gPost("id");
    		$e = Empresa::findFirst("id = $id");
    		$e->nombre = parent::gPost("nombre");
    		$e->matriz = parent::gPost("matriz");
    		if($e->matriz == "") $e->matriz = null;
    		if($e->update()){
    			parent::msg("Empresa modificada exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operaci&oacute;n");
    		}
    	}else{
    		parent::msg("El campo de Nombre no puede quedar en blanco");
    	}
    	parent::forward("empresa", "index");
    }   

}


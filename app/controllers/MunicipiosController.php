<?php
class MunicipiosController extends ControllerBase
{

	public function indexAction()
	{
		parent::limpiar();
		$departamento = Departamentos::find();
		$campos = [
				["h", ["id"], ""],
				["t", ["nombre"], "Nombre"],
				["t", ["descripcion"], "Descripci&oacute;n"],
				["sdb", ["departamento", $departamento, ["id", "nombre"]], "Departamento"],
				["s", [""], "Guardar"]	
		];		
		$form = parent::form($campos, "municipios/guardar", "form1");
		
		$head = ["Nombre", "Descripci&oacute;n", "Departamento", "Creaci&oacute;n", "Modificaci&oacute;n", "Acciones"];
		$tabla = parent::thead("municipios", $head);
		$municipios = Municipios::find();
		foreach ($municipios as $u){
			$r = Departamentos::findFirst("id = ".$u->departamento);
			$tabla = $tabla.parent::tbody([
					$u->nombre,
					$u->descripcion,
					$r->nombre,
					$u->fcreacion,
					$u->fmod,
					parent::a(2, "cargarDatos('".$u->id."', '".$u->nombre."', '".$u->descripcion."', '".$u->departamento."')", "Editar")." | ".
					parent::a(1, "municipios/eliminar", "Eliminar", [["id", $u->id]])
					]);
		}		
		
		//js
		$fields = ["id", "nombre", "descripcion", "departamento"];
		$otros = "";
		$jsBotones = ["form1", "municipios/edit", "municipios/index"];
		
		parent::view("Municipios", $form, $tabla, [$fields, $otros, $jsBotones]);
	}
	
	public function guardarAction(){
		$uid = parent::gPost("nombre");
		$dept = parent::gPost("departamento");
		if($uid != null && $uid != ""){
			$u = Municipios::find("nombre = '$uid' AND departamento = '$dept'");
			if(count($u) > 0){
				parent::msg("El municipio $uid ya existe");
				parent::forward("municipios", "index");
			} else {
				$municipio = new Municipios();
				$municipio->nombre = $uid;
				$municipio->descripcion = parent::gPost("descripcion");
				$municipio->departamento = $dept;
				$municipio->fcreacion = parent::fechaHoy(true);
				$municipio->fmod = parent::fechaHoy(true);
				if($municipio->save()){
					parent::msg("El municipio fue creado exitosamente", "s");
				}else{
					parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
				}
			}
		}else{
			parent::msg("El nombre del municipio no puede quedar en blanco");
		}
		parent::forward("municipios", "index");
	}

	public function eliminarAction(){
		$municipios = Municipios::findFirst("id = ".parent::gReq("id"));
		$clientes = Cliente::find(array("municipio = $municipios->id"));
		if(count($clientes) > 0){
			parent::msg("No se puede eliminar un municipio que tenga asociado uno o m&aacute;s clientes", "w");
		}else {
			$nMunicipios = $municipios->nombre;
			if($municipios->delete()){
				parent::msg("Se elimin&oacute; el municipio: $nMunicipios", "s");
			}else{
				parent::msg("","db");
			}
		}
		parent::forward("municipios", "index");
	}
	
	public function editAction(){
    	if(parent::vPost("id")){
    		$municipio = Municipios::findFirst("id = ".parent::gPost("id"));
    		$municipio->nombre = parent::gPost("nombre");
    		$municipio->departamento = parent::gPost("departamento");
    		$departamento = Departamentos::findFirst("id = $municipio->departamento");
    		
    		$consulta = Municipios::find("nombre = '$municipio->nombre' AND departamento = '$municipio->departamento'");
    		if(count($consulta) > 0){
    			parent::msg("La combinaci&oacute;n de municipio $municipio->nombre y departamento $departamento->nombre");
    			parent::forward("municipios", "index");
    		} else {
	    		$municipio->descripcion = parent::gPost("desc");
	    		$municipio->fmod = parent::fechaHoy(true);
	    		if($municipio->update()){
	    			parent::msg("Municipio modificado exitosamente", "s");
	    		}else{
	    			parent::msg("Ocurri&oacute; un error durante la operaci&oacute;n");
	    		}
    		}
    	}else{
    		parent::msg("Ocurri&oacute; un error al cargar el Municipio");
    	}
    	parent::forward("municipios", "index");
	}
	
}
?>
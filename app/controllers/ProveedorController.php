<?php
class ProveedorController extends ControllerBase
{

	public function indexAction()
	{
		parent::limpiar();
		$tipoProveedor = TipoProveedor::find();
		$campos = [
				["h", ["id"], ""],
				["t", ["nombre"], "Nombre"],
				["t", ["documento"], "Documento"],
				["t", ["documento2"], "Documento 2"],
				["t", ["direccion"], "Direcci&oacute;n"],
				["t", ["telefono"], "Tel&eacute;fono"],
				["sdb", ["tipo", $tipoProveedor, ["id", "tipo"]], "Tipo"],
				["s", [""], "Guardar"]
		];		
		$form = parent::form($campos, "proveedor/guardar", "form1");
		
		$head = ["Nombre", "Documento", "Documento 2", "Direcci&oacute;n", "Tel&eacute;fono", "Tipo", "Acciones"];
		$tabla = parent::thead("proveedor", $head);
		$proveedor = Proveedor::find();
		foreach ($proveedor as $u){
			$r = TipoProveedor::findFirst("id = ".$u->tipo);
			$tabla = $tabla.parent::tbody([
					$u->nombre,
					$u->documento,
					$u->documento2,
					$u->direccion,
					$u->telefono,
					$r->tipo,
					parent::a(2, "cargarDatos('".$u->id."', '".$u->nombre."', '".$u->documento."', '".$u->documento2."', '".$u->direccion."', '".$u->telefono."', '".$u->tipo."')", "Editar")." | ".
					parent::a(1, "contactos/index/$u->id", "Contactos"). " | " .
					parent::a(1, "proveedor/eliminar", "Eliminar", [["id", $u->id]])
					]);
		}		
		
		//js
		$fields = ["id", "nombre", "documento", "documento2", "direccion", "telefono", "tipo"];
		$otros = "";
		$jsBotones = ["form1", "proveedor/edit", "proveedor/index"];
		
		parent::view("Proveedor", $form, $tabla, [$fields, $otros, $jsBotones]);
	}
	
	public function guardarAction(){
		$uid = parent::gPost("nombre");
		$tipo = parent::gPost("tipo");
		if($uid != null && $uid != ""){
			$u = Proveedor::find("nombre = '$uid' AND tipo = '$tipo'");
			if(count($u) > 0){
				parent::msg("El proveedor $uid ya existe");
				parent::forward("proveedor", "index");
			} else {
				$proveedor = new Proveedor();
				$proveedor->nombre = $uid;
				$proveedor->documento = parent::gPost("documento");
				$proveedor->documento2 = parent::gPost("documento2");
				$proveedor->direccion = parent::gPost("direccion");
				$proveedor->telefono = parent::gPost("telefono");
				$proveedor->tipo = $tipo;
				if($proveedor->save()){
					parent::msg("El proveedor fue creado exitosamente", "s");
				}else{
					parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
				}
			}
		}else{
			parent::msg("El nombre del proveedor no puede quedar en blanco");
		}
		parent::forward("proveedor", "index");
	}

	public function eliminarAction(){
		$proveedor = Proveedor::findFirst("id = ".parent::gReq("id"));
		$ordenesCompra = OrdenCompra::find(array("proveedor = $proveedor->id"));
		if(count($ordenesCompra) > 0){
			parent::msg("No se puede eliminar un proveedor que tenga asociadas uno o m&aacute;s &oacute;rdenes de compra", "w");
		}else {
			$nProveedor = $proveedor->nombre;
			if($proveedor->delete()){
				parent::msg("Se elimin&oacute; el proveedor: $nProveedor", "s");
			}else{
				parent::msg("","db");
			}
		}
		parent::forward("proveedor", "index");
	}
	
	public function editAction(){
    	if(parent::vPost("id")){
    		$proveedor = Proveedor::findFirst("id = ".parent::gPost("id"));
    		$proveedor->nombre = parent::gPost("nombre");
    		$proveedor->tipo = parent::gPost("tipo");
    		$tipoProveedor = TipoProveedor::findFirst("id = $proveedor->tipo");
    		
    		$consulta = Proveedor::find("nombre = '$proveedor->nombre' AND tipo = '$proveedor->tipo' AND id <> '$proveedor->id'");
    		if(count($consulta) > 0){
    			parent::msg("La combinaci&oacute;n de proveedor $proveedor->nombre y tipo $tipoProveedor->tipo ya existe");
    			parent::forward("proveedor", "index");
    		} else {
	    		$proveedor->documento = parent::gPost("documento");
	    		$proveedor->documento2 = parent::gPost("documento2");
	    		$proveedor->direccion = parent::gPost("direccion");
	    		$proveedor->telefono = parent::gPost("telefono");
	    		$proveedor->tipo = parent::gPost("tipo");
	    		if($proveedor->update()){
	    			parent::msg("Proveedor modificado exitosamente", "s");
	    		}else{
	    			parent::msg("Ocurri&oacute; un error durante la operaci&oacute;n");
	    		}
    		}
    	}else{
    		parent::msg("Ocurri&oacute; un error al cargar el Proveedor");
    	}
    	parent::forward("proveedor", "index");
	}
	
}
?>
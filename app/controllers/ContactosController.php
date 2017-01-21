<?php
class ContactosController extends ControllerBase
{

	public function indexAction($pid)
	{
		parent::limpiar();
		$campos = [
				["h", ["id"], ""],
				["t", ["nombre"], "Nombre"],
				["t", ["tel"], "Tel&eacute;fono"],
				["t", ["cel"], "Celular"],
				["e", ["email"], "E-mail"],
				["s", [""], "Guardar"]
		];		
		$form = parent::form($campos, "contactos/guardar/$pid", "form1");
		
		$head = ["Nombre", "Tel&eacute;fono", "Celular", "E-mail", "Acciones"];
		$tabla = parent::thead("tcontactos", $head);
		$contactos = Contactos::find("proveedor = $pid");
		foreach ($contactos as $u){
			$tabla = $tabla.parent::tbody([
					$u->nombre,
					$u->telefono,
					$u->celular,
					$u->email,
					parent::a(2, "cargarDatos('".$u->id."', '".$u->nombre."', '".$u->telefono."', '".$u->celular."', '".$u->email."')", "Editar")." | ".
					parent::a(1, "contactos/eliminar/$u->id", "Eliminar")
					]);
		}		
		
		//js
		$fields = ["id", "nombre", "tel", "cel", "email"];
		$otros = "";
		$jsBotones = ["form1", "contactos/edit/$pid", "contactos/index/$pid"];
		
		parent::view("Contactos", $form, $tabla, [$fields, $otros, $jsBotones]);
	}
	
	public function guardarAction($pid){
		$uid = parent::gPost("nombre");
		if($uid != null && $uid != ""){
			$u = Contactos::find("nombre = '$uid'");
			if(count($u) > 0){
				$u1 = $u->getFirst();
				$p = Proveedor::findFirst("id = $u1->proveedor");
				parent::msg("El contacto $uid ya existe como parte del proveedor $p->nombre. Corregir para crearlo");				
			} else {
				$c = new Contactos();
				$c->celular = parent::gPost("cel");
				$c->email = parent::gPost("email");
				$c->nombre = $uid;
				$c->proveedor = $pid;
				$c->telefono = parent::gPost("tel");
				if($c->save()){
					parent::msg("El contacto fue creado exitosamente", "s");
				}else{
					parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
				}
			}
		}else{
			parent::msg("El nombre del Contacto no puede quedar en blanco");
		}
		parent::forward("contactos", "index", [$pid]);
	}

	public function eliminarAction($cid){
		$con = Contactos::findFirst("id = $cid");
		$pid = $con->proveedor;
		$nCon = $con->nombre;
		if($con->delete()){
			parent::msg("Se elimin&oacute; el Contacto: $nCon", "s");
		}else{
			parent::msg("","db");
		}
		parent::forward("contactos", "index", [$pid]);
	}
	
	public function editAction($pid){
    	if(parent::vPost("id")){
    		$c = Contactos::findFirst("id = ".parent::gPost("id"));
    		$uid = parent::gPost("nombre");
			if($uid != null && $uid != ""){
				$u = Contactos::find("nombre like '$uid' and (proveedor not like $pid and id not like $c->id)");				
				if(count($u) > 0){
					$u1 = $u->getFirst();
					$p = Proveedor::findFirst("id = $u1->proveedor");
					parent::msg("El contacto $uid ya existe como parte del proveedor $p->nombre. Corregir para crearlo");				
				} else {
					$c->celular = parent::gPost("cel");
					$c->email = parent::gPost("email");
					$c->nombre = $uid;
					$c->telefono = parent::gPost("tel");
					if($c->update()){
						parent::msg("El contacto fue editado exitosamente", "s");
					}else{
						parent::msg("","db");
					}
				}
			}else{
				parent::msg("El nombre del Contacto no puede quedar en blanco");
			}
    	}else{
    		parent::msg("Ocurri&oacute; un error al cargar el Contacto");
    	}
    	parent::forward("contactos", "index", [$pid]);
	}
	
}
?>
<?php
class UsuarioController extends ControllerBase
{

	public function indexAction()
	{
		parent::limpiar();
		$rol = Roles::find();
		$campos = [
				["t", ["usuario"], "Usuario"],
				["h", ["id"], ""],
				["sdb", ["rol", $rol, ["id", "rol"]], "Rol"],
				["s", [""], "Guardar"]	
		];		
		$form = parent::form($campos, "usuario/guardar", "form1");
		
		$head = ["Usuario", "Rol", "Creaci&oacute;n", "Modificaci&oacute;n", "Vencimiento", "Acciones"];
		$tabla = parent::thead("usuarios", $head);
		$usuarios = Usuario::find();
		foreach ($usuarios as $u){
			$r = Roles::findFirst("id = ".$u->rol_id);
			$deshabilitar = "Deshabilitar";
			if($u->estado == 0){
				$deshabilitar = "Habilitar";
			}
			$tabla = $tabla.parent::tbody([
					$u->usuario,
					$r->rol,
					$u->fcreacion,
					$u->fmod,
					$u->fclave,
					parent::a(2, "cargarDatos('".$u->id."', '".$u->usuario."', '".$u->rol_id."')", "Editar")." | ".
					parent::a(1, "usuario/deshabilitar", $deshabilitar, [["id", $u->id]])." | ".
					parent::a(1, "usuario/resetear", "Resetear", [["id", $u->id]])
			]);
		}		
		
		//js
		$fields = ["id", "usuario", "rol"];
		$otros = "";
		$jsBotones = ["form1", "usuario/edit", "usuario/index"];
		
		parent::view("Usuarios", $form, $tabla, [$fields, $otros, $jsBotones]);
	}
	
	public function guardarAction(){
		$uid = parent::gPost("usuario");
		if($uid != null && $uid != ""){
			$u = Usuario::find("usuario = '$uid'");
			if(count($u) > 0){
				parent::msg("El usuario $uid ya existe");
				parent::forward("usuario", "index");
			} else {
				$user = new Usuario();
				$user->clave = parent::newPass();
				$user->fclave = parent::datePlus("+5");
				$user->fcreacion = parent::fechaHoy(true);
				$user->fmod = parent::fechaHoy(true);
				$user->rol_id = parent::gPost("rol");
				$user->usuario = $uid;
				if($user->save()){
					parent::msg("El usuario fue creado exitosamente", "s");
				}else{
					parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
				}
			}
		}else{
			parent::msg("El nombre de usuario no puede quedar en blanco");
		}
		parent::forward("usuario", "index");
	}
	
	public function editAction(){
		if(!parent::vPost("id")){
			parent::msg("Id no se carg&oacute; correctamente");
			parent::forward("usuario", "index");
		}
		$id = parent::gPost("id");
		$u = Usuario::findFirst("id = $id");
		$user = parent::gPost("usuario");
		$users = Usuario::find("usuario like '$user' and id not like $id");
		if(count($users) > 0){
			parent::msg("El usuario $user ya est&aacute; siendo utilizado");
			parent::forward("usuario", "index");
		} else {
			$u->usuario = $user;
			$u->rol_id = parent::gPost("rol");
			$u->fmod = parent::fechaHoy(true);
			if($u->update()){
				parent::msg("Edici&oacute;n exitosa", "s");
				parent::forward("usuario", "index");
			}else{
				parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
				parent::forward("usuario", "index");
			}
		}		
	}
	
	public function resetearAction(){
		$id = parent::gReq("id");
		if($id == "" || $id == null){
			parent::msg("Id no se carg&oacute; correctamente");
			parent::forward("usuario", "index");
		}
		$u = Usuario::findFirst("id = $id");
		$u->clave = parent::newPass();
		$u->fmod = parent::fechaHoy(true);
		if($u->update()){
			parent::msg("Contrase&ntilde;a reseteada para usuario $u->usuario", "s");
			parent::forward("usuario", "index");
		}else{
			parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
			parent::forward("usuario", "index");
		}
	}
	
	public function deshabilitarAction(){
		$id = parent::gReq("id");
		if($id == "" || $id == null){
			parent::msg("Id no se carg&oacute; correctamente");
			parent::forward("usuario", "index");
		}
		$u = Usuario::findFirst("id = $id");
		if($u->estado == 1){
			$u->estado = 0;
		}else{
			$u->estado = 1;
		}
		$u->fmod = parent::fechaHoy(true);
		if($u->update()){
			if($u->estado == 1){
				parent::msg("Usuario $u->usuario Habilitado exitosamente", "s");
			}else{
				parent::msg("Usuario $u->usuario Deshabilitado exitosamente", "s");
			}			
			parent::forward("usuario", "index");
		}else{
			parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
			parent::forward("usuario", "index");
		}
	}

}
?>
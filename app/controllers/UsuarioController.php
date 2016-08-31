<?php
class UsuarioController extends ControllerBase
{

	public function indexAction()
	{
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
			$tabla = $tabla.parent::tbody([
					$u->usuario,
					$r->rol,
					$u->fcreacion,
					$u->fmod,
					$u->fclave,
					parent::a(1, "usuario/deshabilitar", "Deshabilitar", [["id", $u->id]])." | ".
					parent::a(1, "usuario/resetear", "Resetear", [["id", $u->id]])
			]);
		}		
		
		parent::view("Usuarios", $form, $tabla);
	}
	
	public function guardarAction(){
		$uid = parent::gPost("usuario");
		if($uid != null && $uid != ""){
			$u = Usuario::find("usuario = '$uid'");
			if(count($u) > 0){
				parent::msg("El usuario $uid ya existe");
				parent::forward("usuario", "index");
			}
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
				parent::msg("clave = $user->clave");
				parent::msg("fclave = $user->fclave");
				parent::msg("fcreacion = $user->fcreacion");
				parent::msg("fmod = $user->fmod");
				parent::msg("rol_id = $user->rol_id");
				parent::msg("usuario = $user->usuario");
			}
		}else{
			parent::msg("El nombre de usuario no puede quedar en blanco");
		}
		parent::forward("usuario", "index");
	}

}
?>
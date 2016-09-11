<?php
class InicioController extends ControllerBase
{

	public function indexAction()
	{
		$user = parent::gSession("usuario");
		$u = Usuario::findFirst("id = $user");
		$titulo = "Bienvenid@ $u->usuario";
		parent::view($titulo);
	}
	
}
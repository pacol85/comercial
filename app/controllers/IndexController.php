<?php

class IndexController extends ControllerBase
{

    public function indexAction()
    {
		
    }
    
    public function logOffAction(){
    	$usuario = $this->session->destroy(true);
    	parent::forward("index", "index");    	
    }
    
    public function entrarAction(){
    		//$this->view->disable();
		$user = parent::gPost("user");
		$user = trim($user);
		$user = strtoupper($user);
		$pass = parent::gPost("pass");
		$pass = trim($pass);
		// Ver si existe el usuario y contraseña
		$success = Usuario::find("usuario ='$user' and estado = 1");
		 
		if ($success->count() < 2 && $success->count() > 0) {
			$usuario = new Usuario();
			$usuario = $success->getFirst();
	
			//validar contrasena
			if(parent::checkPass($pass, $usuario->clave)){
				$this->session->set("usuario", $usuario->id);				
				if(parent::checkPass($pass, "",true)){
					parent::forward("index", "newPass");					
				}else{
					parent::forward("inicio", "index");					
				}
			}else{
				parent::msg("Credenciales suministradas son err&oacute;neas");
				parent::forward("index", "retry");
			}
			 
		} else {
			parent::msg("Usuario no encontrado");
			parent::forward("index", "retry");
		}
	}
    
	public function newPassAction(){
		 
	}
	
    public function changePassAction(){
    	$usuario = parent::gSession("usuario");
    	$op = parent::gPost("oldPass");
    	$np = parent::gPost("newPass");
    	$rp = parent::gPost("repeatPass");
    	$exito = "";
    	$error = "";
    		
    	if(($op == null || $op == "") and ($np == null || $np == "") and ($rp == null || $rp == "")){
    		parent::msg("Alguno de los campos solicitados no fue llenado");
    		parent::forward("index", "newPass");
    			
    	}else{
    		$u = Usuario::findFirst($usuario);
    		if(!$this->security->checkHash($op, $u->clave)){
    			$error = "Contrase&ntilde;a original incorrecta";
    		}
    		if($np != $rp){
    			$error = "Las contrase&ntilde;as no concuerdan";
    		}
    		$oldClaves = ClavesUsuario::find("usuario = $u->id");
    		foreach ($oldClaves as $oc){
    			if(parent::checkPass($np, $oc->clave)){
    				$error = "No se pueden utilizar las &uacute;ltimas 5 claves";
    			}
    		}
    		
    		if(preg_match('/^(?=.{8,}$)(?=.*?[A-Z])(?=.*?([\x20-\x40\x5b-\x60\x7b-\x7e\x80-\xbf]).*?$).*$/',$np)){
    			$exito = "Contrase&ntilde;a cambiada exitosamente";
    		}else{
    			$error = "Nueva contrase&ntilde;a no cumple con los estandares";
    		}
    		if($error != ""){
    			parent::msg($error);
    			parent::forward("index", "newPass");
    		}else{
    			//guardar clave anterior en listado de claves
    			$claves = new ClavesUsuario();
    			$claves->clave = $u->clave;
    			$claves->usuario = $u->id;
    			$claves->save();
    			
    			$u->clave = $this->security->hash($np);
    			$u->update();
    			parent::msg($exito, "s");
    			parent::forward("inicio", "index");
    		}
    	}
    }
    
    public function retryAction(){
    	 
    }

}


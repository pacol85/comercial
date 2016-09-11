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
		$user = $this->request->get("user");
		$user = trim($user);
		$user = strtoupper($user);
		$pass = $this->request->get("pass");
		$pass = trim($pass);
		// Ver si existe el usuario y contraseña
		$success = Usuario::find("usuario ='$user'");
		 
		if ($success->count() < 2 && $success->count() > 0) {
			$usuario = new Usuario();
			$usuario = $success->getFirst();
	
			//validar contrasena
			if(parent::checkPass($pass, $usuario->clave)){
				$this->session->set("usuario", $usuario->id);

				
				if(parent::checkPass($usuario->clave, "",true)){
					parent::forward("index", "newPass");					
				}else{
					parent::forward("index", "index");					
				}
			}else{
				parent::msg("Credenciales suministradas son err&oacute;neas");
				parent::forward("index", "retry");
			}
			 
		} else {
			parent::msg("no existe el usuario");
			parent::forward("index", "retry");
		}
	}
    
	public function newPassAction(){
		 
	}
	
    public function changePassAction(){
    	$usuario = $this->session->get("usuario");
    	$op = $this->request->getPost("oldPass");
    	$np = $this->request->getPost("newPass");
    	$rp = $this->request->getPost("repeatPass");
    	$exito = "";
    	$error = "";
    		
    	if(($op == null || $op == "") and ($np == null || $np == "") and ($rp == null || $rp == "")){
    		$this->flashSession->error("Alguno de los campos solicitados no fue llenado");
    		return $this->dispatcher->forward(array(
    				"controller" => "inicio",
    				"action" => "newPass"
    		));
    			
    	}else{
    		$u = Usuarios::findFirst($usuario);
    		if(!$this->security->checkHash($op, $u->u_contrasena)){
    			$error = "Contrase&ntilde;a incorrecta";
    		}
    		if($np != $rp){
    			$error = "Las contrase&ntilde;as no concuerdan";
    		}
    		if(preg_match('/^(?=.{8,}$)(?=.*?[A-Z])(?=.*?([\x20-\x40\x5b-\x60\x7b-\x7e\x80-\xbf]).*?$).*$/',$np)){
    			$exito = "Contrase&ntilde;a cambiada exitosamente";
    		}else{
    			$error = "Nueva contrase&ntilde;a no cumple con los estandares";
    		}
    		if($error != ""){
    			$this->flashSession->error($error);
    			return $this->dispatcher->forward(
    					array(
    							"controller" => "inicio",
    							"action"     => "newPass"
    					)
    			);
    		}else{
    			$u->u_contrasena = $this->security->hash($np);
    			$u->save();
    			$this->flash->success($exito);
    			return $this->dispatcher->forward(
    					array(
    							"controller" => "inicio",
    							"action"     => "index"
    					)
    			);
    		}
    	}
    }
    
    public function retryAction(){
    	 
    }

}


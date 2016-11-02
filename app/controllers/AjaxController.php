<?php

class AjaxController extends ControllerBase
{

    public function indexAction()
    {
		
    }
    
    public function municipiosAction(){
    	$dept = $this->request->getPost("muni");
    	$municipios = Municipios::find(["nombre like '%$dept%'", "limit" => 3]);
    	
    	$select = $this->tag->select(array("user",
    			Usuarios::find("u_estado = 1 AND d_id = $dept"),
    			"using" => array("u_id", "u_nombre"), "class" => "form-control", "id" => "fieldUser"));
    	
    	$response = ['select' => $select, 'dept' => $dept];
    	return parent::sendJson($response);
    }
}

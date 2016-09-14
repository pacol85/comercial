<?php

class NotificacionController extends ControllerBase
{

    public function tipoNotificacionAction()
    {
		parent::limpiar();
    	$campos = [
				["t", ["tipo"], "Tipo"],
				["t", ["desc"], "Descripci&oacute;n"],
				["h", ["id"], ""],
				["s", ["guardar"], "Guardar"]
		];
		
		$head = ["Tipo", "Descripci&oacute;n", "Acciones"];
		$tabla = parent::thead("tipoNotificacion", $head);
		$tipos = TipoNotificacion::find();
		foreach ($tipos as $t){
			$tabla = $tabla.parent::tbody([
					$t->tipo,
					$t->descripcion, 
					parent::a(2, "cargarDatos('".$t->id."','".$t->tipo."','".$t->descripcion."');", "Modificar")." | ".
					parent::a(1, "notificacion/eliminaTipo", "Eliminar", [["id", $t->id]])
			]);
		}
		
		//js
		$fields = ["id", "tipo", "desc"];
		$otros = "";
		$jsBotones = ["form1", "notificacion/editTipoNotif", "notificacion/tipoNotificacion"];
		
    	$form = parent::form($campos, "notificacion/crearTipo", "form1");
    	$tabla = parent::ftable($tabla);
    	parent::view("Tipo Notificaci&oacute;n", $form, $tabla, [$fields, $otros, $jsBotones]);
    }
    
    public function crearTipoAction(){
    	if(parent::vPost("tipo")){
    		$tipo = new TipoNotificacion();
    		$tipo->tipo = parent::gPost("tipo");
    		$tipo->descripcion = parent::gPost("desc");
    		if($tipo->save()){
    			parent::msg("Tipo de Notificaci&oacute;n creada exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operaci&oacute;n");
    		}
    	}else{
    		parent::msg("El campo de Tipo no puede quedar en blanco");
    	}
    	parent::forward("notificacion", "tipoNotificacion");
    }
    
    public function eliminaTipoAction(){
    	$tipo = TipoNotificacion::findFirst("id = ".parent::gReq("id"));
    	$notif = Notificaciones::find("id = $tipo->id");
    	if(count($notif) > 0){
    		parent::msg("No se puede eliminar un tipo de notificaci&oacute;n que est&aacute; en uso");
    		parent::forward("notificacion", "tipoNotificacion");
    	}else{
    		if($tipo->delete()){
    			parent::msg("Se elimin&oacute; correctamente el tipo de notificaci&oacute;n");		
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operaci&oacute;n");
    		}
    	}    		    	
    	parent::forward("notificacion", "tipoNotificacion");
    }
    
    public function editTipoNotifAction(){
    	if(parent::vPost("id")){
    		$tipo = TipoNotificacion::findFirst("id = ".parent::gPost("id"));
    		$tipo->tipo = parent::gPost("tipo");
    		$tipo->descripcion = parent::gPost("desc");
    		if($tipo->update()){
    			parent::msg("Tipo de Notificaci&oacute;n modificada exitosamente", "s");
    		}else{
    			parent::msg("Ocurri&oacute; un error durante la operaci&oacute;n");
    		}
    	}else{
    		parent::msg("Ocurri&oacute; un error al cargar el Tipo de Notificaci&oacute;n");
    	}
    	parent::forward("notificacion", "tipoNotificacion");
    }

}


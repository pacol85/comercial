<?php
class IsucController extends ControllerBase
{

	public function indexAction($iid)
	{
		parent::limpiar();
		$isuc = ItemXSucursal::find("item = $iid");
		
		$campos = [["l", ["Indicar cuantos items hay en cada sucursal indicada, si se deja vac&iacute;o es 0"], "Aviso", "texto"]];
		$counter = 1;
		foreach ($isuc as $is){
			$s = Sucursal::findFirst("id = $is->sucursal");
			array_push($campos, ["tv", ["suc$s->id", "$is->cantidad"], "$s->nombre"]);
		}
		array_push($campos, ["s", [""], "Guardar"]);
		
		$form = parent::form($campos, "isuc/guardar/$iid", "form1");
		$item = Item::findFirst("id = $iid");
		parent::view("Inventario en Sucursales para C&oacute;digo: $item->codigo", $form);
	}
	
	public function guardarAction($iid){
		$isuc = ItemXSucursal::find("item = $iid");
		$error = false;
		foreach ($isuc as $is){
			$is->cantidad = parent::gPost("suc$is->sucursal");
			if(!$is->update()){
				$suc = Sucursal::findFirst("id = $is->sucursal");
				parent::msg("Ocurri&oacute; error al actualizar Sucursal: $suc->nombre");
				$error = true;
			}
		}
		if($error == false){
			parent::msg("Modificaci&oacute;n exitosa", "s");
		}
		parent::forward("isuc", "index", ["$iid"]);
	}
	
}
?>
<?php
class FacturaController extends ControllerBase
{

	public function indexAction()
	{
		parent::limpiar();
		$campos = [
				["h", ["id"], ""],
				["t", ["marca"], "Marca"],
				["t", ["modelo"], "Modelo"],
				["t", ["desc"], "Descripci&oacute;n"],
				["t", ["costo"], "Costo"],
				["t", ["codigo"], "C&oacute;digo"],
				["s", [""], "Guardar"]
		];		
		$form = parent::form($campos, "items/guardar", "form1");
		
		$head = ["C&oacute;digo", "Marca", "Modelo", "Descripci&oacute;n", "Costo", "M&iacute;nimo", "Acciones"];
		$tabla = parent::thead("titems", $head);
		$items = Item::find();
		foreach ($items as $u){
			$tabla = $tabla.parent::tbody([
					$u->codigo,
					$u->marca, 
					$u->modelo, 
					$u->descripcion, 
					$u->valor,
					$u->minimo,
					parent::a(2, "cargarDatos('".$u->id."', '".$u->marca."', '".$u->modelo."', '".$u->descripcion."', '".
							$u->valor."', '".$u->codigo."')", "Editar")." | ".
					parent::a(1, "items/eliminar/$u->id", "Eliminar")
					]);
		}		
		
		//js
		$fields = ["id", "marca", "modelo", "desc", "costo", "codigo"];
		$otros = "";
		$jsBotones = ["form1", "items/edit", "items/index"];
		
		parent::view("Items", $form, $tabla, [$fields, $otros, $jsBotones]);
	}
	
	public function guardarAction(){
		if(parent::vPost("desc") && parent::vPost("costo")){
			$cod = parent::gPost("codigo");
			$desc = parent::gPost("desc");
			$u = Item::find("descripcion like '$desc' or codigo like '$cod'");
			if(count($u) > 0){
				parent::msg("El item con descripci&oacute;n: $desc o el c&oacute;digo: $cod ya existe");
				parent::forward("items", "index");
			} else {
				//obten impuesto usual 13%
				$imp = Parametros::findFirst("parametro like 'iva'");
				
				$i = new Item();
				$i->codigo = $cod;
				$i->descripcion = $desc;
				$i->impuesto = $imp->valor;
				$i->marca = parent::gPost("marca");
				$i->modelo = parent::gPost("modelo");
				$i->valor = parent::gPost("costo");
				$i->total = $i->valor*(1 + $i->impuesto/100);
				$i->minimo = $i->total*1.10; //para mientras un 10%
				if($i->save()){
					parent::msg("El item fue creado exitosamente", "s");
				}else{
					parent::msg("", "db");
				}
			}
		}else{
			parent::msg("La descripci&oacute;n y el costo no pueden ir en blanco");
		}
		parent::forward("items", "index");
	}

	public function eliminarAction($iid){
		$item = Item::findFirst("id = $iid");
		$ordenesCompra = OrdenCompra::find("item = $iid");
		$ixfactura = ItemXFactura::find("item = $iid");
		$ixsucursal = ItemXSucursal::find("item = $iid");
		if(count($ordenesCompra) > 0 || count($ixfactura) > 0 || count($ixsucursal) > 0){
			parent::msg("No se puede eliminar un item que este asociado a una orden de compra, factura o sucursal", "w");
		}else {
			$cItem = "$item->codigo - $item->descripcion";
			if($item->delete()){
				parent::msg("Se elimin&oacute; el item: $cItem", "s");
			}else{
				parent::msg("","db");
			}
		}
		parent::forward("items", "index");
	}
	
	public function editAction(){
    	if(parent::vPost("id")){
    		$i = Item::findFirst("id = ".parent::gPost("id"));
	    	if(parent::vPost("desc") && parent::vPost("costo")){
				$cod = parent::gPost("codigo");
				$desc = parent::gPost("desc");
				$u = Item::find("(descripcion like '$desc' or codigo like '$cod') and id not like $i->id");
				if(count($u) > 0){
					parent::msg("El item con descripci&oacute;n: $desc o el c&oacute;digo: $cod ya existe");
					//$u2 = $u->getFirst();
					//parent::msg("ID = $i->id, $u2->id, $u2->descripcion, $u2->codigo");
					parent::forward("items", "index");
				} else {
					//obten impuesto usual 13%
					$imp = Parametros::findFirst("parametro like 'iva'");
					
					$i->codigo = $cod;
					$i->descripcion = $desc;
					$i->impuesto = $imp->valor;
					$i->marca = parent::gPost("marca");
					$i->modelo = parent::gPost("modelo");
					$i->valor = parent::gPost("costo");
					$i->total = $i->valor*(1 + $i->impuesto/100);
					$i->minimo = $i->total*1.10; //para mientras un 10%
					if($i->update()){
						parent::msg("El item fue editado exitosamente", "s");
					}else{
						parent::msg("", "db");
					}
				}
			}else{
				parent::msg("La descripci&oacute;n y el costo no pueden ir en blanco");
			}
    		
    	}else{
    		parent::msg("Ocurri&oacute; un error al cargar el Item");
    	}
    	parent::forward("items", "index");
	}
	
}
?>
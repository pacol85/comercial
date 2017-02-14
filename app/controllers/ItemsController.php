<?php
class ItemsController extends ControllerBase
{

	public function indexAction()
	{
		parent::limpiar();
		
		//proveedores en $data
		$proveedores = Proveedor::find("estado = 1");
		$data = "";
		foreach ($proveedores as $p){
			$data = $data."$p->nombre;";
		}
		$data = substr($data, 0, strlen($data)-1);
		
		$campos = [
				["h", ["id"], ""],
				["t", ["proveedor"], "Proveedor"],
				["h", ["listProv"], $data],
				["t", ["marca"], "Marca"],
				["t", ["modelo"], "Modelo"],
				["t", ["desc"], "Descripci&oacute;n"],
				["t", ["costo"], "Costo"],
				["t", ["codigo"], "C&oacute;digo"],
				["s", [""], "Guardar"]
		];		
		$form = parent::form($campos, "items/guardar", "form1");
		
		$head = ["C&oacute;digo", "Proveedor", "Marca", "Modelo", "Descripci&oacute;n", "Costo", "M&iacute;nimo", "Acciones"];
		$tabla = parent::thead("titems", $head);
		$items = Item::find();
		foreach ($items as $u){
			//proveedor
			$prov = Proveedor::findFirst("id = $u->proveedor");
			
			$tabla = $tabla.parent::tbody([
					$u->codigo,
					$prov->nombre,
					$u->marca, 
					$u->modelo, 
					$u->descripcion, 
					$u->valor,
					$u->minimo,
					parent::a(2, "cargarDatos('".$u->id."', '".$prov->nombre."', '".$u->marca."', '".$u->modelo."', '".$u->descripcion."', '".
							$u->valor."', '".$u->codigo."')", "Editar")." | ".
					parent::a(1, "isuc/index/$u->id", "Inventario")." | ".
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
				//obten porc minimo
				$min = Parametros::findFirst("parametro like 'minimo'");
				
				$i = new Item();
				$i->codigo = $cod;
				$i->descripcion = $desc;
				$i->impuesto = $imp->valor;
				$i->marca = parent::gPost("marca");
				$i->modelo = parent::gPost("modelo");
				$i->valor = parent::gPost("costo");
				$i->total = parent::porcUp($i->valor, $i->impuesto); //costo mas impuesto
				$i->minimo = parent::redondear(parent::porcUp($i->total, $min->valor, 2));

				$prov = parent::gPost("proveedor");
				$i->proveedor = $this->provload($prov);
				if($i->save()){
					parent::msg("El item fue creado exitosamente", "s");
					
					//se inician los items por sucursal
					$this->isuc($i);
					
				}else{
					parent::msg("", "db");
				}
			}
		}else{
			parent::msg("La descripci&oacute;n y el costo no pueden ir en blanco");
		}
		parent::forward("items", "index");
	}
	
	public function isuc($i){
		$sucs = Sucursal::find();
		foreach ($sucs as $s){
			$isuc = new ItemXSucursal();
			$isuc->cantidad = 0;
			$isuc->item = $i->id;
			$isuc->sucursal = $s->id;
			if(!$isuc->save()){
				parent::msg("Ocurrio error al guardar en sucursal $s->id");
			}
		}
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
					//obten porc minimo
					$min = Parametros::findFirst("parametro like 'minimo'");
					
					$i->codigo = $cod;
					$i->descripcion = $desc;
					$i->impuesto = $imp->valor;
					$i->marca = parent::gPost("marca");
					$i->modelo = parent::gPost("modelo");
					$i->valor = parent::gPost("costo");
					$i->total = parent::porcUp($i->valor, $i->impuesto); //costo mas impuesto
					$i->minimo = parent::redondear(parent::porcUp($i->total, $min->valor, 2));
					
					$prov = parent::gPost("proveedor");
					$i->proveedor = $this->provload($prov);
					
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
	
	/**
	 * provload
	 */
	public function provload($prov){
		$proveedor = trim($prov);
		$dept = trim($md[1]);
		$mid = "";
		$provs = Proveedor::find("nombre like '$prov'");
		if(count($provs) > 0){
			$p = $provs->getFirst();
			return $p->id;
		}else{
			$pro = new Proveedor();
			$pro->nombre = $prov;
			$pro->documento = "Faltante";
			$pro->save();
			
			return $pro->id;
		}
	}
	
}
?>
<?php
use Defr\LoanRequest;

class CreditoController extends ControllerBase
{

	public function indexAction($cid)
	{
		parent::limpiar();
		$hoy = parent::fechaHoy(false);
		$sucursales = Sucursal::find();
		$campos = [
				["sdb", ["suc", $sucursales, ["id", "nombre"]], "Sucursal"],
				["d", ["fsolicitud", $hoy], "Fecha Solicitud"],
				["m", ["cuotas", 0], "Cuotas"],
				["m", ["monto", 0], "Monto"],
				["m", ["prima", 0], "Prima"],
				["h", ["id"], ""],
				["h", ["mcb"], ""],
				["h", ["mtb"], ""],
				["s", [""], "Solicitud Inicial"]	
		];

		$head1 = ["Cod.", "Marca", "Modelo", "Valor", "Seleccionar", "Cantidad"];
		$table = parent::thead("titems", $head1);
		
		$cont = Parametros::findFirst("parametro like 'contado'");
		$cred = Parametros::findFirst("parametro like 'icredito'");
		
		$items = Item::find();
		foreach ($items as $i){
			$tcont = parent::porcUp($i->total, $cont->valor, 2);
			$tcred = parent::porcUp($tcont, $cred->valor);
			
			$table = $table . parent::tbody([
				$i->codigo, 
				$i->marca, 
				$i->modelo, 
				parent::redondear($tcred), 
				parent::elemento("cf", ["check$i->id", "$i->id", "addValor('$i->id');", "suma"], ""),
				parent::elemento("tvcb", ["n$i->id", "1", "tbcant"], "Cant.")
			]);
		}
		$form = parent::formTabla($campos, $table, 4, "credito/guardar/$cid", "form1");
		
		$head = ["Id", "Sucursal", "Saldo Ini", "Adquisici&oacute;n", "Cancelaci&oacute;n", "Cuotas",
				"Saldo", "Prima", "Acciones"				
		];
		$tabla = parent::thead("credito", $head);
		$creds = CreditoXCliente::find("cliente = $cid");
		foreach ($creds as $c){
			$suc = Sucursal::findFirst("id = $c->sucursal");
			$cuotas = Cuotas::find("credito = $c->id and prima = 0");
			$totCuotas = count($cuotas);
			$saldo = $c->monto - $c->prima;
			foreach ($cuotas as $cuot){
				$recibo = Recibos::find("cuota = $cuot->id");
				if(count($recibo) > 0){
					$saldo = $saldo - $cuot->monto;
				}
			}
			$saldo = $c->monto - 
			$tabla = $tabla.parent::tbody([
					$c->id,
					$suc->nombre,
					$c->monto,
					$c->fecha_adquisicion,
					$c->fecha_cancelacion, 
					$totCuotas, 
					$saldo, 
					$c->prima,
					parent::a(1, "credito/editar/$c->id", "Editar")." | ".
					parent::a(1, "cuotas/index/$c->id", "Cuotas")
			]);
		}		
		
		//js
		$fields = ["id", "monto", "fsolicitud", "interes", "prima"];
		$otros = "";
		$jsBotones = ["form1", "credito/edit/$cid", "credito/cargar/$cid"];
		$client = Cliente::findFirst($cid);
		
		parent::view("Cr&eacute;ditos de Cliente: $client->nombre", $form, $tabla, [$fields, $otros, $jsBotones]);				
	}
	
	public function guardarAction($cid){
		if(parent::vPost("fsolicitud") && parent::vPost("cuotas") && parent::vPost("items")){
			$cuotas = parent::gPost("cuotas");
			$json_arreglo = parent::gPost("items");
			//usar funcion para calcular monto total y prima
			$myp = $this->CalcularJson($json_arreglo, $cuotas);
			
			$c = new CreditoXCliente();
			$c->cliente = $cid;
			$c->fsolicitud = parent::gPost("fsolicitud");
			$c->interes = parent::gPost("interes");
			$c->monto = parent::gPost("monto");
			$c->prima = parent::gPost("prima");
			$c->diaCorte = parent::gDay($c->fsolicitud);
			$cuotas = parent::gPost("cuotas");
			$loan = new LoanRequest($c->monto, $c->interes, $cuotas);
			$result = $loan->calculate();
			$c->cuotaBase = $result->getMonthlyPayment();
			if($c->save()){
				parent::msg("El cr&eacute;dito fue creado exitosamente", "s");

				//crear cuotas ya sea que se apruebe o no, se modificará cada vez que se haga una modificacion en general
				for ($i = 1; $i <= $cuotas; $i++){
					$cuota = new Cuotas();
					$cuota->credito = $c->id;
					$cuota->fechaPago = parent::datePlus2($c->fsolicitud, $i, "m");
					$cuota->monto = 0;
					$cuota->prima = 0;
					$cuota->save();
				}
			}else{
				parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
			}
		}else{
			parent::msg("Aseg&uacute;rese de llenar todos los campos");
		}
		parent::forward("credito", "index", [$cid]);
	}
	
	public function editarAction($credId){
		$cred = CreditoXCliente::findFirst($credId);
		$hoy = parent::fechaHoy(false);
		$campos = [
				["m", ["monto", $cred->monto], "Monto"],
				["d", ["fsolicitud", $cred->fsolicitud], "Fecha Solicitud"],
				["m", ["interes", $cred->interes], "Inter&eacute;s"],
				["m", ["prima", $cred->prima], "Prima"],
				["m", ["cuotas", 0], "Cuotas"],
				["h", ["id"], ""],
				["s", [""], "Modificacion"]	
		];		
		$form = parent::form($campos, "credito/actualizar/$credId", "form1");
		
		$head = ["Cuota", "Fecha programada", "Monto", "Notas"];
		$tabla = parent::thead("tcuota", $head);
		$cuotas = Cuotas::find("credito = '$cred->id'");
		$corr = 1;
		foreach ($cuotas as $c){
			$tabla = $tabla.parent::tbody([
					$corr,
					$c->fechaPago,
					$c->monto,
					$c->nota
			]);
			$corr++;
		}		
		
		//js
		$fields = ["id", "monto", "fsolicitud", "interes", "prima"];
		$otros = "";
		$jsBotones = ["form1", "credito/edit/", "credito/cargar/"];
		
		parent::view("Informaci&oacute;n de cr&eacute;dito No. $cred->id", $form, $tabla, [$fields, $otros, $jsBotones]);
	}
	
	public function deshabilitarAction(){
		/*$id = parent::gReq("id");
		if($id == "" || $id == null){
			parent::msg("Empleado no se carg&oacute; correctamente");
			parent::forward("empleado", "index");
		}
		$e = Empleado::findFirst("id = $id");
		if($e->estado == 1){
			$e->estado = 0;
		}else{
			$e->estado = 1;
		}
		$e->fmod = parent::fechaHoy(true);
		if($e->update()){
			if($e->estado == 1){
				parent::msg("$e->primer_nombre $e->primer_apellido Habilitado exitosamente", "s");
			}else{
				parent::msg("$e->primer_nombre $e->primer_apellido Deshabilitado exitosamente", "s");
			}			
			parent::forward("empleado", "index");
		}else{
			parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
			parent::forward("empleado", "index");
		}*/
		parent::msg("Esta funci&oacute;n no esta activa en este momento");
		parent::forward("cliente", "index");
	}
	
	public function listMunicipioAction()
	{
		$dept = parent::gPost("dept");
		//$muni = Municipios::find("departamento = $dept");
		//$sel = parent::elemento("sdb", ["muni", $muni, ["id", "nombre"]], "Municipio");
		$select = $this->tag->select(array("muni",
				Municipios::find("departamento = $dept"),
				"using" => array("id", "nombre"), "class" => "form-control", "id" => "muni"));
		$response = ['select' => $select, 'dept' => $dept];
		return parent::sendJson($response);
	
	}
	
	function subidaDarioAction(){
		$file = "c:\TEMP\dario.xlsx";
		$inputFileType = PHPExcel_IOFactory::identify($file);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$archivo = $objReader->load($file);
		set_time_limit(300);
		$sheet = $archivo -> setActiveSheetIndex(0);
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();
			
		$titulo = true;
		$fila = 0;
		for ($row = 1; $row <= $highestRow; $row++){
			//  Read a row of data into an array
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
					NULL,
					TRUE,
					FALSE);
			$colN = 1;
			foreach ($rowData as $col){
					
				if ($titulo == true){
					$titulo = false;
					continue;
				}
				else{
					$this->trabajaCol($col, 2);
				}
				$colN++;	
			}
		}
		parent::msg("Termin&oacute; subida de excel Comercial Dario", "n");
		parent::forward("inicio", "index");
	}
	
	public function trabajaCol($col, $suc){
		$result = true;
		//crear cliente
		$client = new Cliente();
		$client->documento = "NA".$col[0]; //se crearan con NA seguido del numero de cuenta temporalmente
		$client->estado = 1;
		$client->municipio = 2; //Se asume San Salvador
		$client->nombre = $col[2];
		if(!$client->save()) $result = false;
			
		//crear Item (producto)
		$prod = new Item();
		$prod->codigo = $col[0]; //para mientras se creará el artículo con el código de cuenta
		$prod->descripcion = $col[3];
		$prod->impuesto = 0;
		$prod->marca = "NA";
		$prod->modelo = "NA";
		$prod->total = 0;
		$prod->valor = $col[5]/1.035;
		if(!$prod->save()) $result = false;
			
		//crear credito
		$cred = new CreditoXCliente();
		$cred->cuenta = $col[0];
		$cred->fecha_adquisicion = parent::fechaExcel($col[7]);
		$cred->cuotaBase = $col[5]/($col[4] + 1);
		$cred->fsolicitud = parent::fechaExcel($col[1]);
		$cred->interes = 3.5;
		$cred->monto = $col[5];
		$cred->prima = $col[8];
		$cred->sucursal = $suc; //2 es Dario, 1 es Angel
		$cred->cliente = $client->id;
		if(!$cred->save()) $result = false;
			
		//crear cuota de prima
		$prima = new Cuotas();
		$prima->credito = $cred->id;
		$prima->fechaPago = $cred->fecha_adquisicion;
		$prima->monto = $cred->prima;
		$prima->prima = 1;
		if(!$prima->save()) $result = false;
			
		//crear recibo de la prima
		$receipt = new Recibos();
		$receipt->cuota = $prima->id;
		$receipt->fpago = $prima->fechaPago;
		$receipt->numero = $col[6];
		if(!$receipt->save()) $result = false;
			
		//crear Cuotas en blanco
		$cuotas = $col[4];
		parent::msg("total cuotas = $cuotas");
		$this->creaCuotas($cuotas, $cred);
		
		//actualizar cuotas ya pagadas
		$off = 9;
		$size = count($col);
		$counter = 1;
		$loadCuotas = Cuotas::find("credito = $cred->id and prima = 0");
		foreach ($loadCuotas as $l){
			if(($counter*3 + $off) <= $size){
				$pos = (($counter *3) + $off) - 1;
				$l->fechaPago = parent::fechaExcel($col[$pos]);
				$l->monto = $col[$pos +1];
				$l->update();
				
				//crear recibo
				$recibo = new Recibos();
				$recibo->cuota = $l->id;
				$recibo->fpago = $l->fechaPago;
				$recibo->numero = $col[$pos-1];
				$recibo->save();
			}
			$counter++;
		}
		
		return $result;
	}
	
	public function creaCuotas($cuotas, $cred){
		for ($i = 1; $i <= $cuotas; $i++){
			$cuota = new Cuotas();
			$cuota->credito = $cred->id;
			$cuota->prima = 0;
			$cuota->fechaPago = parent::datePlus2($cred->fsolicitud, $i, "m");
			$cuota->monto = 0;
			$cuota->save();
		}
		
	}
	
function subidaAngelAction(){
		$file = "c:\TEMP\angel.xlsx";
		$inputFileType = PHPExcel_IOFactory::identify($file);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$archivo = $objReader->load($file);
		set_time_limit(300);
		$sheet = $archivo -> setActiveSheetIndex(0);
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();
			
		$titulo = true;
		$fila = 0;
		for ($row = 1; $row <= $highestRow; $row++){
			//  Read a row of data into an array
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
					NULL,
					TRUE,
					FALSE);
			$colN = 1;
			foreach ($rowData as $col){
					
				if ($titulo == true){
					$titulo = false;
					continue;
				}
				else{
					$this->trabajaCol($col, 1);
				}
				$colN++;	
			}
		}
		parent::msg("Termin&oacute; subida de excel Comercial El Angel", "n");
		parent::forward("inicio", "index");
	}
	
	public function fullprocAction($cid, $fid)
	{
		parent::limpiar();
		$hoy = parent::fechaHoy(false);
		$sucursales = Sucursal::find();
		$campos = [
				["sdb", ["suc", $sucursales, ["id", "nombre"]], "Sucursal"],
				["d", ["fsolicitud", $hoy], "Fecha Solicitud"],
				["m", ["cuotas", 0], "Cuotas"],
				["m", ["monto", 0], "Monto"],
				["m", ["prima", 0], "Prima"],
				["h", ["id"], ""],
				["h", ["mcb"], ""],
				["h", ["mtb"], ""],
				["s", [""], "Solicitud Inicial"]	
		];
		
		$head1 = ["Cod.", "Marca", "Modelo", "Valor", "Seleccionar", "Cantidad"];
		$table = parent::thead("titems", $head1);
		
		$cont = Parametros::findFirst("parametro like 'contado'");
		$cred = Parametros::findFirst("parametro like 'icredito'");
		
		$items = Item::find();
		foreach ($items as $i){
			$tcont = parent::porcUp($i->total, $cont->valor);
			$tcred = parent::porcUp($tcont, $cred->valor);
				
			$table = $table . parent::tbody([
					$i->codigo,
					$i->marca,
					$i->modelo,
					$tcred,
					parent::elemento("cf", ["check$i->id", "$i->id", "addValor('$i->id');", "suma"], ""),
					parent::elemento("tvcb", ["n$i->id", "1", "tbcant"], "Cant.")
			]);
		}
		$form = parent::formTabla($campos, $table, 4, "credito/guardarfull/$cid/$fid", "form1");
		
		parent::view("Cr&eacute;dito", $form);
	}
	
	public function guardarfullAction($cid, $fid){
		if(parent::vPost("fsolicitud") && parent::vPost("cuotas") && parent::vPost("mcb")){
			$cuotas = parent::gPost("cuotas");
			$json_arreglo = parent::gPost("mcb");
			//usar funcion para calcular monto total y prima
			$myp = $this->CalcularJson($json_arreglo, $cuotas);
			
			$c = new CreditoXCliente();
			$c->cliente = $cid;
			$c->fsolicitud = parent::gPost("fsolicitud");
			$i = Parametros::findFirst("parametro like 'icredito'");
			$c->interes = $i->valor;
			$c->monto = $myp["monto"];
			$c->prima = $myp["prima"];
			$c->diaCorte = parent::gDay($c->fsolicitud);
			
			//$loan = new LoanRequest($c->monto, $c->interes, $cuotas);
			//$result = $loan->calculate();
			$c->cuotaBase = $myp["prima"];//$result->getMonthlyPayment();
			
			//$cliente = Cliente::findFirst("id = $cid");
			$referencias = Referencia::find("cliente = $cid");
			foreach ($referencias as $r){
				if($r->pariente = 1 and $r->parentesco != 1){
					$c->pariente = $r->id;
				}else{
					$c->amigo = $r->id;
				}
			}
			$c->fiador = $fid;
			$c->sucursal = parent::gPost("suc");
			
			if($c->save()){
				parent::msg("El cr&eacute;dito fue creado exitosamente", "s");
	
				//crear cuotas ya sea que se apruebe o no, se modificará cada vez que se haga una modificacion en general
				for ($i = 1; $i <= $cuotas; $i++){
					$cuota = new Cuotas();
					$cuota->credito = $c->id;
					$cuota->fechaPago = parent::datePlus2($c->fsolicitud, $i, "m");
					$cuota->monto = 0;
					$cuota->prima = 0;
					if(!$cuota->save()){
						parent::msg("Fallo cuota $i");
					}
				}
			}else{
				parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
				/*parent::msg("$c->cliente, $c->fsolicitud, $c->interes, $c->monto, $c->prima, $c->diaCorte, $c->cuotaBase, $c->pariente, 
						$c->amigo, $c->fiador");*/
				return parent::forward("credito", "fullproc", [$cid,$fid]);
			}
		}else{
			parent::msg("Aseg&uacute;rese de llenar todos los campos");
			return parent::forward("credito", "fullproc", [$cid,$fid]);
		}
		parent::forward("credito", "index", [$cid]);
	}
	
	/*
	 * Función para sumar totales en el crédito
	 */
	function CalcularAction(){
		$cuotas = parent::gPost("cuotas");
		
		$json_arreglo = parent::gPost("items");
		$response = $this->CalcularJson($json_arreglo, $cuotas);
		return parent::sendJson($response);
	}
	
	public function CalcularJson($json_arreglo, $cuotas){
		$arreglo = json_decode($json_arreglo);
		$monto = 0;
		$prima = 0;
		$error = "";
		$pos = 0;
		foreach ($arreglo as $a){
			if($a != 0 && $a != null){
				//$error = $error.$b.",";
				$i = Item::findFirst("id = $pos");
				$cont = Parametros::findFirst("parametro like 'contado'");
				$cred = Parametros::findFirst("parametro like 'icredito'");
		
				$tcont = parent::porcUp($i->total, $cont->valor, 2);
				$tcred = $tcont * $a;
				if($cuotas >= 6){
					$tcred = parent::porcUp($tcont, $cred->valor) * $a;
				}
				
		
				$monto = $monto + $tcred;
				$prima = $prima + ($tcred/($cuotas + 1));
			}
			$pos = $pos + 1;
		}
		
		$response = ['monto' => parent::redondear($monto), 'prima' => parent::redondear($prima)];
		return $response;
	}
}
?>
<?php
use Defr\LoanRequest;

class CuotasController extends ControllerBase
{

	public function indexAction($cred)
	{
		parent::limpiar();
		$hoy = parent::fechaHoy(false);
		$campos = [
				["m", ["monto", 0], "Monto"],
				["d", ["fsolicitud", $hoy], "Fecha Solicitud"],
				["m", ["interes", 0], "Inter&eacute;s"],
				["m", ["prima", 0], "Prima"],
				["m", ["cuotas", 0], "Cuotas"],
				["h", ["id"], ""],
				["s", [""], "Solicitud Inicial"]	
		];		
		//$form = parent::form($campos, "credito/guardar/$cid", "form1");
		
		$head = ["#", "Monto", "Fecha", "Recibo", "Pormenores", "Acciones"];
		$tabla = parent::thead("cuotas", $head);
		$cuotas = Cuotas::find("credito = $cred and prima = 0");
		$corr = 1;
		foreach ($cuotas as $c){
			$recibo = Recibos::findFirst("cuota = $c->id");
			$pendiente = "Pendiente de Pago";
			$pagar = parent::a(1, "cuotas/pagar/$c->id/$corr", "Pagar");
			
			if($recibo != null){
				$pendiente = $recibo->numero;
				$pagar = "";
			}
						
			$tabla = $tabla.parent::tbody([
					$corr,
					$c->monto,
					$c->fechaPago,
					$pendiente,
					$c->pormenores,
					$pagar
			]);
			$corr++;
		}		
		
		//js
		$fields = ["id", "monto", "fsolicitud", "interes", "prima"];
		$otros = "";
		//$jsBotones = ["form1", "credito/edit/$cid", "credito/cargar/$cid"];
		
		$credito = CreditoXCliente::findFirst("id = $cred");
		parent::view("Cuotas del Cr&eacute;dito: $credito->cuenta", "", $tabla);				
	}
	
	public function pagarAction($cid, $corr){
		$hoy = parent::fechaHoy(false);
		$cuota = Cuotas::findFirst("id = $cid");
		$cred = CreditoXCliente::findFirst("id = $cuota->credito");
		$campos = [
				["m", ["monto", $cred->cuotaBase], "Monto"],
				["d", ["fpago", $hoy], "Fecha Pago"],
				["t", ["recibo"], "Recibo"],
				["t", ["pormenores"], "Pormenores"],
				["t", ["nota"], "Nota"],
				["s", [""], "Pagar"]
		];
		$form = parent::form($campos, "cuotas/actualizar/$cid/$corr", "form1");
		
		
		parent::view("Cuota No. $corr de Cr&eacute;dito No. $cred->cuenta", $form);
	}
	
	public function actualizarAction($cid, $corr){
		$cuota = Cuotas::findFirst("id = $cid");
		if(!parent::vPost("recibo")){
			parent::msg("No se puede realizar un pago sin un Recibo");
			return parent::forward("cuotas", "index", ["$cuota->credito"]);
		}
		
		$cuota->monto = parent::gPost("monto");
		$cuota->fechaPago = parent::gPost("fpago");
		$cuota->nota = parent::gPost("nota");
		$cuota->pormenores = parent::gPost("pormenores");
		if($cuota->update()){
			//crear recibo
			$recibo = new Recibos();
			$recibo->cuota = $cuota->id;
			$recibo->fpago = $cuota->fechaPago;
			$recibo->numero = parent::gPost("recibo");
			if($recibo->save()){
				parent::msg("Cuota $corr y Recibo $recibo->numero guardados exitosamente", "s");			
			}else{
				parent::msg("Ocurri&oacute; un error al crear el Recibo");
			}
		}else{
			parent::msg("", "db");
		}
		return parent::forward("cuotas", "index", ["$cuota->credito"]);
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
			foreach ($rowData as $col){
					
				if ($titulo == true){
					$titulo = false;
					continue;
				}
				else{
					
					//crear cliente
					$client = new Cliente();
					$client->documento = "NA".$col[0]; //se crearan con NA seguido del numero de cuenta temporalmente
					$client->estado = 1;
					$client->municipio = 2; //Se asume San Salvador
					$client->nombre = $col[2];
					$client->save();
					
					//crear Item (producto)
					$prod = new Item();
					$prod->codigo = $col[0]; //para mientras se creará el artículo con el código de cuenta
					$prod->descripcion = $col[3];
					$prod->impuesto = 0;
					$prod->marca = "NA";
					$prod->modelo = "NA";
					$prod->total = 0;
					$prod->valor = $col[5]/1.035;
					$prod->save();
					
					//crear credito
					$cred = new CreditoXCliente();
					$cred->cuenta = $col[0];
					$cred->fecha_adquisicion = parent::fechaExcel($col[7]);
					$cred->cuotaBase = $col[5]/($col[4] + 1);
					$cred->fsolicitud = parent::fechaExcel($col[1]);
					$cred->interes = 3.5;
					$cred->monto = $col[5];
					$cred->prima = $col[8];
					$cred->sucursal = 2; //sucursal Darío
					$cred->cliente = $client->id;
					$cred->save();
					
					
					//crear Cuotas en blanco
					$cuotas = $col[4];
					$off = 9;
					$size = count($col);
					for ($i = 1; $i <= $cuotas; $i++){
						$cuota = new Cuotas();
						$cuota->credito = $cred->id;							
						if(($i*3 + $off) <= $size){
							$pos = (($i *3) + $off) - 1;
							$cuota->fechaPago = parent::fechaExcel($col[$pos]);
							$cuota->monto = $col[$pos +1];							
							$cuota->save();
							
							//crear recibo
							$recibo = new Recibos();
							$recibo->cuota = $cuota->id;
							$recibo->fpago = $cuota->fechaPago;
							$recibo->numero = $col[$pos-1];
							$recibo->save();
						}else{
							$cuota->fechaPago = parent::datePlus2($cred->fsolicitud, $i, "m");
							$cuota->monto = 0;
							$cuota->save();
						}
						
					}
					
					
				}
					
			}
		}
		parent::msg("Termin&oacute; subida de excel Comercial Dario", "n");
		parent::forward("inicio", "index");
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
			foreach ($rowData as $col){
					
				if ($titulo == true){
					$titulo = false;
					continue;
				}
				else{
						
					//crear cliente
					$client = new Cliente();
					$client->documento = "NA".$col[0]; //se crearan con NA seguido del numero de cuenta temporalmente
					$client->estado = 1;
					$client->municipio = 2; //Se asume San Salvador
					$client->nombre = $col[2];
					$client->save();
						
					//crear Item (producto)
					$prod = new Item();
					$prod->codigo = $col[0]; //para mientras se creará el artículo con el código de cuenta
					$prod->descripcion = $col[3];
					$prod->impuesto = 0;
					$prod->marca = "NA";
					$prod->modelo = "NA";
					$prod->total = 0;
					$prod->valor = $col[5]/1.035;
					$prod->save();
						
					//crear credito
					$cred = new CreditoXCliente();
					$cred->cuenta = $col[0];
					$cred->fecha_adquisicion = parent::fechaExcel($col[7]);
					$cred->cuotaBase = $col[5]/($col[4] + 1);
					$cred->fsolicitud = parent::fechaExcel($col[1]);
					$cred->interes = 3.5;
					$cred->monto = $col[5];
					$cred->prima = $col[8];
					$cred->sucursal = 1; //sucursal El Angel
					$cred->cliente = $client->id;
					$cred->save();
						
						
					//crear Cuotas en blanco
					$cuotas = $col[4];
					$off = 9;
					$size = count($col);
					for ($i = 1; $i <= $cuotas; $i++){
						$cuota = new Cuotas();
						$cuota->credito = $cred->id;
						if(($i*3 + $off) <= $size){
							$pos = (($i *3) + $off) - 1;
							$cuota->fechaPago = parent::fechaExcel($col[$pos]);
							$cuota->monto = $col[$pos +1];
							$cuota->save();
								
							//crear recibo
							$recibo = new Recibos();
							$recibo->cuota = $cuota->id;
							$recibo->fpago = $cuota->fechaPago;
							$recibo->numero = $col[$pos-1];
							$recibo->save();
						}else{
							$cuota->fechaPago = parent::datePlus2($cred->fsolicitud, $i, "m");
							$cuota->monto = 0;
							$cuota->save();
						}
	
					}
						
						
				}
					
			}
		}
		parent::msg("Termin&oacute; subida de excel Comercial El Angel", "n");
		parent::forward("inicio", "index");
	}

}
?>
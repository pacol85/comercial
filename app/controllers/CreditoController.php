<?php
use Defr\LoanRequest;

class CreditoController extends ControllerBase
{

	public function indexAction($cid)
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
		$form = parent::form($campos, "credito/guardar/$cid", "form1");
		
		$head = ["Id", "Monto", "Adquisici&oacute;n", "Cancelaci&oacute;n", "Cuota",
				"Inter&eacute;s", "Prima", "Acciones"				
		];
		$tabla = parent::thead("credito", $head);
		$creds = CreditoXCliente::find("cliente = $cid");
		foreach ($creds as $c){
			$tabla = $tabla.parent::tbody([
					$c->id,
					$c->monto,
					$c->fecha_adquisicion,
					$c->fecha_cancelacion, 
					$c->cuotaBase, 
					$c->interes, 
					$c->prima,
					parent::a(1, "credito/editar/$c->id", "Editar")." | ".
					parent::a(1, "cuotas/cargar/$c->id", "Cuotas")
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
		if(parent::vPost("monto") && parent::vPost("fsolicitud") && parent::vPost("interes") && parent::vPost("prima") &&
				parent::vPost("cuotas")){
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
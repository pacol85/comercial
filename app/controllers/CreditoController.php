<?php
use Defr\LoanRequest;

class CreditoController extends ControllerBase
{

	public function indexAction($cid)
	{
		//parent::limpiar();
		$sucursal = Sucursal::find("estado = 1");
		$dept = Departamentos::find();
		$d = $dept->getFirst();
		$muni = Municipios::find("departamento = $d->id");
		//["ls", ["municipio", "municipios('ajax/municipios')"], "Municipio"],
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
					parent::a(2, "cargarDatos('".$c->id."', '".$c->monto."', '".$c->fsolicitud."', '".
							$c->interes."', '".$c->prima."', '".$c->cuotaBase."')", "Editar")." | ".
					parent::a(1, "cuotas/cargar/$c->id", "Cuotas")
			]);
		}		
		
		//js
		$fields = ["id", "monto", "fsolicitud", "interes", "prima"];
		$otros = "";
		$jsBotones = ["form1", "credito/edit/$cid", "credito/cargar/$cid"];
		$client = Cliente::findFirst($cid);
		
		parent::view("Cr&eacute;ditos de Cliente: $client->nombre", $form, $tabla, [$fields, $otros, $jsBotones]);
		/*
		$loan = new LoanRequest(5000, 35, 12);
		$result = $loan->calculate();
		parent::msg($result->getMonthlyPayment());
		*/		
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
			
			$loan = new LoanRequest($c->monto, $c->interes, parent::gPost("cuotas"));
			$result = $loan->calculate();
			$c->cuotaBase = $result->getMonthlyPayment();
			if($c->save()){
				parent::msg("El cr&eacute;dito fue creado exitosamente", "s");
			}else{
				parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
			}
		}else{
			parent::msg("Aseg&uacute;rese de llenar todos los campos");
		}
		parent::forward("credito", "index", [$cid]);
	}
	
	public function editAction(){
		if(!parent::vPost("id")){
			parent::msg("Cliente no se carg&oacute; correctamente");
			return parent::forward("cliente", "index");
		}
		$id = parent::gPost("id");
		$doc = parent::gPost("doc");
		$nombre = parent::gPost("nombre");
		
		$c = Cliente::findFirst("id = $id");
		$cli = Cliente::find("(documento like '$doc' or nombre like '$nombre')
				and id not like $id");
		if(count($cli) > 0){
			parent::msg("El cliente $c->nombre ya existe");
			return parent::forward("cliente", "index");
		}
		$c->alquila = parent::gPost("alquila");
		$c->areaTrab = parent::gPost("area");
		$c->cargo = parent::gPost("cargo");
		$c->direccion = parent::gPost("dir");
		$c->documento = parent::gPost("doc");
		$c->fdesde = parent::gPost("fdesde");
		$c->fexpedicion = parent::gPost("expedicion");
		$c->jefe = parent::gPost("jefe");
		$c->lugarExpedicion = parent::gPost("lugar");
		$c->municipio = parent::gPost("muni");
		$c->trabajo = parent::gPost("trabajo");
		$c->nombre = parent::gPost("nombre");
		if($c->alquila == 1){
			$c->propietario = parent::gPost("propietario");
		}else{
			$c->propietario = $c->nombre;
		}			
		$c->sueldo = parent::gPost("sueldo");
		$c->telOficina = parent::gPost("tofic");
		$c->tipodoc = parent::gPost("tipoDoc");
		if($c->update()){
			parent::msg("Edici&oacute;n exitosa", "s");
			return parent::forward("cliente", "index");
		}else{
			parent::msg("Ocurri&oacute; un error durante la transacci&oacute;n");
			return parent::forward("cliente", "index");
		}
		
			
		
		
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

}
?>
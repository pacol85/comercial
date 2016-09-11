<?php
use Phalcon\Mvc\Controller;
class ControllerBase extends Controller {
	/**
	 * Main init function
	 */
	public function initialize() {
		// code ...
		$this->flash->output ();
	}
	public function fechaExcel($xl_date) {
		$PHPTimeStamp = PHPExcel_Shared_Date::ExcelToPHP ( $xl_date );
		$fechaExcel = date ( 'Y-m-d', $PHPTimeStamp );
		// $fechaExcel = date_format((($xl_date - 25569) * 86400), "Y-m-d H:i:s");
		return $fechaExcel;
	}
	public function fechaMySQLx($xl_date) {
		$PHPTimeStamp = PHPExcel_Shared_Date::ExcelToPHP ( $xl_date );
		$fechaExcel = date ( 'Y-m-d', $PHPTimeStamp );
		return $fechaExcel;
	}
	public function fechaHoraMySQLx($xl_date) {
		$PHPTimeStamp = PHPExcel_Shared_Date::ExcelToPHP ( $xl_date );
		$fechaExcel = date ( 'Y-m-d H:i:s', $PHPTimeStamp );
		return $fechaExcel;
	}
	public function fechaHoy($conHora) {
		$timezone = - 6;
		if ($conHora == true) {
			return gmdate ( "Y-m-d H:i:s", time () + 3600 * ($timezone) );
		} else {
			return gmdate ( "Y-m-d", time () + 3600 * ($timezone) );
		}
	}
	
	// Sends the json response
	public function sendJson($data) {
		$this->view->disable ();
		$this->response->setContentType ( 'application/json', 'UTF-8' );
		$this->response->setContent ( json_encode ( $data ) );
		return $this->response;
	}
	public function elemento($t, $n, $l, $r = 0) {
		$dId = "";
		if (! is_numeric ( $r )) {
			$dId = "id='$r'";
		}
		$elem = "";
		switch ($t) {
			case "i" :
				$elem = $elem . "<img id='$n[0]' src='$l' onclick ='$n[1]'>";
				break;
			case "hr" :
				$elem = $elem . "<hr>";
				break;
			case "h" :
				$elem = $elem . $this->tag->hiddenField ( array (
						"$n[0]",
						"value" => $l 
				) );
				break;
			case "s" :
				$elem = $elem . '<div class="form-group main"><div class="col-sm-12" align="center" ' . $dId . '>';
				$elem = $elem . $this->tag->submitButton ( array (
						"$l",
						"class" => "btn btn-default" 
				) );
				$elem = $elem . '</div></div>';
				break;
			case "bg" :
				$elem = $elem . '<div class="form-group edit"><div class="col-sm-12" align="center" ' . $dId . '>';
				foreach ( $n as $b ) {
					$elem = $elem . '<button class="btn btn-default" id="' . $b [0] . '" name="' . $b [0] . '" onclick="' . $b [1] . '">' . $b [2] . '</button> ';
				}
				$elem = $elem . '</div></div>';
				break;
			case "h2" :
				$elem = $elem . '<h2>' . $l . '</h2>';
				break;
			case "h1" :
				$elem = $elem . '<div class="page-header" ' . $dId . '><h1>' . $l . '</h1></div>';
				break;
			case "l" :
				$elem = $elem . '<div class="form-group"><label for="' . $l . '" class="col-sm-2 control-label">' . $l . '</label>';
				$elem = $elem . '<div class="col-sm-2 control-label" ' . $dId . '>' . $n [0] . '</div></div>';
				break;
			case "lf" :
				$elem = $elem . '<div class="form-group" ' . $dId . '><label for="' . $n [0] . '" class="col-sm-12">' . $l . '</label></div>';
				break;
			case "enter" :
				$elem = $elem . '<nobr>&nbsp;</nobr>';
				break;
			default :
				$elem = '<div class="form-group"><label for="';
				// agregamos el nombre
				$elem = $elem . $n [0] . '" class="col-sm-2 control-label">';
				// agrega label
				$elem = $elem . $l . '</label><div class="col-sm-10" ' . $dId . '>';
				// agrega nombre campo
				switch ($t) {
					case "t" :
						if ($r == 1) {
							$elem = $elem . $this->tag->textField ( array (
									"$n[0]",
									"size" => 30,
									"class" => "form-control",
									"id" => "$n[0]",
									"readonly" => "" 
							) );
						} else {
							$elem = $elem . $this->tag->textField ( array (
									"$n[0]",
									"size" => 30,
									"class" => "form-control",
									"id" => "$n[0]" 
							) );
						}
						break;
					case "tv" :
						if ($r == 1) {
							$elem = $elem . $this->tag->textField ( array (
									"$n[0]",
									"size" => 30,
									"class" => "form-control",
									"id" => "$n[0]",
									"value" => "$n[1]",
									"readonly" => "" 
							) );
						} else {
							$elem = $elem . $this->tag->textField ( array (
									"$n[0]",
									"size" => 30,
									"class" => "form-control",
									"id" => "$n[0]",
									"value" => "$n[1]" 
							) );
						}
						break;
					case "m" :
						$elem = $elem . $this->tag->textField ( array (
								"$n[0]",
								"size" => 30,
								"class" => "form-control money",
								"id" => "$n[0]",
								"value" => "$n[1]" 
						) );
						break;
					case "e" :
						$elem = $elem . $this->tag->textField ( array (
								"$n[0]",
								"size" => 30,
								"class" => "form-control email",
								"id" => "$n[0]" 
						) );
						break;
					case "p" :
						$elem = $elem . $this->tag->passwordField ( array (
								"$n[0]",
								"size" => 30,
								"class" => "form-control",
								"id" => "$n[0]" 
						) );
						break;
					case "d" :
						$elem = $elem . $this->tag->dateField ( array (
								"$n[0]",
								"min" => "0",
								"size" => 30,
								"class" => "form-control date datepicker",
								"id" => "$n[0]" 
						) );
						break;
					case "sdb" :
						if (count ( $n ) > 3) {
							$elem = $elem . $this->tag->select ( array (
									"$n[0]",
									$n [1],
									"using" => $n [2],
									"class" => "form-control",
									"id" => "$n[0]",
									"value" => $n [3] 
							) );
						} else {
							$elem = $elem . $this->tag->select ( array (
									"$n[0]",
									$n [1],
									"using" => $n [2],
									"class" => "form-control",
									"id" => "$n[0]" 
							) );
						}
						break;
					case "sel" :
						if (count ( $n ) > 2) {
							$elem = $elem . $this->tag->select ( array (
									"$n[0]",
									$n [1],
									"class" => "form-control",
									"id" => "$n[0]",
									"value" => $n [2] 
							) );
						} else {
							$elem = $elem . $this->tag->select ( array (
									"$n[0]",
									$n [1],
									"class" => "form-control",
									"id" => "$n[0]" 
							) );
						}
						break;
					case "r" :
						foreach ( $n [1] as $rb ) {
							$elem = $elem . "<label for='$rb'>$rb</label>";
							$elem = $elem . $this->tag->radioField ( array (
									"$n[0]",
									"value" => "$rb",
									"id" => "$rb" 
							) );
							$elem = $elem . "&nbsp;";
						}
						break;
				}
				$elem = $elem . '</div></div>';
		}
		return $elem;
	}
	
	public function form($campos, $action, $id = "id") {
		$form = $this->tag->form ( array (
				$action,
				"autocomplete" => "off",
				"class" => "form-horizontal",
				"id" => "$id" 
		) );
		foreach ( $campos as $c ) {
			if (count ( $c ) > 3) {
				$elem = ControllerBase::elemento ( $c [0], $c [1], $c [2], $c [3] );
			} else
				$elem = ControllerBase::elemento ( $c [0], $c [1], $c [2] );
			$form = $form . $elem;
		}
		
		$form = $form . $this->tag->endForm ();
		return $form;
	}
	
	/*
	 * 'enctype' => 'multipart/form-data'
	 */
	public function multiForm($campos, $action, $id = "id"){
		$form = $this->tag->form(
				array(
						$action,
						"autocomplete" => "off",
						"class" => "form-horizontal",
						'enctype' => 'multipart/form-data',
						"id" => "$id"
				)
		);
		foreach ($campos as $c){
			if(count($c) > 3){
				$elem = ControllerBase::elemento($c[0], $c[1], $c[2], $c[3]);
			}else $elem = ControllerBase::elemento($c[0], $c[1], $c[2]);
			$form = $form.$elem;
		}
	
		$form = $form.$this->tag->endForm();
		return $form;
	}
	
	public function thead($id, $head) {
		$tabla = '<div id="tdiv"><table id="' . $id . '" class="display" cellspacing="0"><thead><tr>';
		
		// Dibujar table head
		foreach ( $head as $h ) {
			$tabla = $tabla . '<th>' . $h . '</th>';
		}
		$tabla = $tabla . '</tr></thead><tbody>';
		return $tabla;
	}
	public function tbody($col) {
		$tr = "<tr>";
		$tr = $tr . $this->td ( $col );
		$tr = $tr . "</tr>";
		return $tr;
	}
	public function td($col) {
		$td = "";
		foreach ( $col as $c ) {
			$td = $td . '<td>' . $c . '</td>';
		}
		return $td;
	}
	public function ftable($tabla) {
		$tabla = $tabla . '</tbody></table></div>';
		return $tabla;
	}
	public function jsCargarDatos($campos, $hide = null, $show = null, $otros = null) {
		$js = "function cargarDatos(";
		foreach ( $campos as $c ) {
			$js = $js . $c . ",";
		}
		$js = rtrim ( $js, "," );
		$js = $js . "){";
		foreach ( $campos as $c2 ) {
			$js = $js . "$('#" . $c2 . "').val(" . $c2 . ");";
		}
		
		if ($hide != null) {
			foreach ( $hide as $h ) {
				$js = $js . "$('." . $h . "').hide();";
			}
		}
		if ($show != null) {
			foreach ( $show as $s ) {
				$js = $js . "$('." . $s . "').show();";
			}
		}
		if ($otros != null) {
			foreach ( $otros as $o ) {
				$js = $js . "$('#" . $o [0] . "').prop(" . $o [1] . ");";
			}
		}
		
		$js = $js . "}";
		return $js;
	}
	
	/*
	 * Funcion para el dispatcher Forward
	 */
	public function forward($controller, $action) {
		return $this->dispatcher->forward ( array (
				"controller" => $controller,
				"action" => $action 
		) );
	}
	
	/*
	 * Función para creación de Links
	 */
	public function a($tipo, $accion, $label, $data = []){
		$a = "<a ";
		if ($tipo == 1){
			$a = $a."href='".$accion;
			if(count($data) > 0){
				$a = $a."?";
				foreach ($data as $d){
					$a = $a.$d[0]."=".$d[1]."&";
				}
				$a = rtrim($a, "&");
			}
			$a = $a."'>".$label;
		}else{
			$a = $a."onClick=\"".$accion."\">".$label;
		}
		$a = $a."</a>";
		return $a;
	}
	
	/*
	 * obtener var de post
	 */
	public function gPost($var){
		$v = $this->request->getPost($var);
		return $v;
	}
	
	/*
	 * Validar post
	 */
	public function vPost($var){
		$p = $this->gPost($var);
		if($p != null && $p != ""){
			return true;
		}
		return false;		
	}
	
	/*
	 * mensajes
	 */
	public function msg($mensaje, $tipo = "e"){
		switch ($tipo) {
			case "s":
				return $this->flash->success($mensaje);
				break;
			case "n":
				return $this->flash->notice($mensaje);
				break;
			case "w":
				return $this->flash->warning($mensaje);
				break;
			case "db":
				return $this->flash->error("Ocurri&oacute; un error durante la operaci&oacute;n");
				break;
			default:
				return $this->flash->error($mensaje);
				break;
		}
	}
	
	/*
	 * obtener var de request
	 */
	public function gReq($var){
		$v = $this->request->get($var);
		return $v;
	}
	
	/*
	 * obtener var de session
	 */
	public function gSession($var){
		$v = $this->session->get($var);
		return $v;
	}
	
	/*
	 * set var de session
	 */
	public function sSession($var, $valor){
		$v = $this->session->set($var, $valor);
		return $v;
	}
	
	/*
	 * Query generica
	 */
	public function query($modelo, $sql){
		// Execute the query
		return new Resultset(null, $modelo, $modelo->getReadConnection()->query($sql));
	}
	
	/*
	 * view function, sets the usual suspects that go into a view
	 */
	public function view($titulo, $form = "", $tabla = ""){
		$this->view->titulo = $this->elemento("h1", ["titulo"], $titulo);
		$this->view->form = $form;
		$this->view->tabla = $this->ftable($tabla);
	}
	
	/*
	 * newPass: to generate initial generic password, in this example is pass
	 */
	public function newPass(){
		$pass = Parametros::findFirst("parametro = 'initialPass'");
		return $this->security->hash($pass);
	}
	
	/*
	 * checkPass: to compare passwords stored with encryption
	 */
	public function checkPass($pass, $comparePass = "", $initial = false){
		if ($initial){
			$p = Parametros::findFirst("parametro = 'initialPass'");
			return $this->security->checkHash($pass, $p->valor);
		}else{
			return $this->security->checkHash($pass, $comparePass);
		}
	}
	
	/*
	 * fecha + o - dias (d), meses (m), anios (y)
	 */
	public function datePlus($var, $option="d"){
		$date = date("Y-m-d");
		$resultado = $this->fechaHoy(false);
		switch ($option) {
			case "m":
				$mod_date = strtotime($date.$var." months");
				$resultado = date("Y-m-d",$mod_date);
				break;
			case "y":
				$mod_date = strtotime($date.$var." years");
				$resultado = date("Y-m-d",$mod_date);
				break;
			default:
				$mod_date = strtotime($date.$var." days");
				$resultado = date("Y-m-d",$mod_date);
				break;
		}
		return $resultado;
	}
	
}

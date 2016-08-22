<?php

class Funciones{

	public function fechaExcel($xl_date)
	{
		$PHPTimeStamp = PHPExcel_Shared_Date::ExcelToPHP($xl_date);
		$fechaExcel = date('Y-m-d',$PHPTimeStamp);
		//$fechaExcel = date_format((($xl_date - 25569) * 86400), "Y-m-d H:i:s");
		return $fechaExcel;
	}
	
	public function fechaMySQL($xl_date)
	{
		$PHPTimeStamp = PHPExcel_Shared_Date::ExcelToPHP($xl_date);
		$fechaExcel = date('Y-m-d H:i:s',$PHPTimeStamp);
		//$fechaExcel = date_format((($xl_date - 25569) * 86400), "Y-m-d H:i:s");
		return $fechaExcel;
	}
	
	public function fechaHoy($conHora){
		$timezone  = -6;
		if($conHora == true){
			return gmdate("Y-m-d H:i:s", time() + 3600*($timezone));
		}else{
			return gmdate("Y-m-d", time() + 3600*($timezone));
		}
	
	}	
}

?>
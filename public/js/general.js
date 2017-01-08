$(document).ready(
		function() {
			$('.number').keypress(
					function(event) {

						if (event.which == 8 || event.keyCode == 37
								|| event.keyCode == 39 || event.keyCode == 46
								|| event.keyCode == 9)
							return true;

						else if ((event.which != 46 || $(this).val().indexOf(
								'.') != -1)
								&& (event.which < 48 || event.which > 57))
							event.preventDefault();

					});
			
			$("#dui").inputmask({
			    mask: "99999999-9",
			    //definitions: {'5': {validator: "[0-5]"}}
			});

			$("#nit").inputmask({
			    mask: "9999-999999-999-9",
			    //definitions: {'5': {validator: "[0-5]"}}
			});
		});

function showResult(str) {
	if (str.length == 0) {
		document.getElementById("livesearch").innerHTML = "";
		document.getElementById("livesearch").style.border = "0px";
		return;
	}
	if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else { // code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById("livesearch").innerHTML = this.responseText;
			document.getElementById("livesearch").style.border = "1px solid #A5ACB2";
		}
	}
	xmlhttp.open("GET", "livesearch.php?q=" + str, true);
	xmlhttp.send();
}

function municipios(action){
	$valor = $("#municipio").val();
	if($valor.length > 0){
		$.ajax({
			type: "POST",
			url: action,//"formulario/listUsers", 
			data: {muni: $('#municipio').val()},
			dataType: "json",
			success: function(result){
				var jsondata = jQuery.parseJSON(JSON.stringify(result)); 
				$("#livesearch").html(jsondata.select);
			}});
	}
	
}


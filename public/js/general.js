$(document).ready(function() {
	 $('.number').keypress(function(event) {

	     if(event.which == 8 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 9) 
	          return true;

	     else if((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57))
	          event.preventDefault();

	});
});
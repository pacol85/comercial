<?php
function gadget_detect(){
 $gadgets = '/(android|iphone|ipad|blackberry)/i';
 if(preg_match($gadgets, $_SERVER['HTTP_USER_AGENT'])) {
 return true;
 } else {
 return false;
 }
}
?>


<?php
//Example usage of Gadget Detect
/*
 include(‘path/to/gadget_detection.php’);
 $device = gadget_detect();
 //Redirect if a mobile device is detected
 if($device == true) {
 header('Location: http://mymobile.my.phpcloud.com');
 exit();
 }
 */
?>
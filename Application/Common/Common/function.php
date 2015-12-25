<?php
function cg_domain(){
	$server = $_SERVER['HTTP_HOST'];
	$http = is_ssl()?"https://":"http://";
	return $http.$server.__ROOT__;
}


?>
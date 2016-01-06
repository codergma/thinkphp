<?php
function is_login(){
	$session_flag = C('SESSION_FLAG');
	$USER =  session($session_flag);
	if(isset($USER) && isset($USER['user_id']) && $USER['user_id']>0){
		return true;
	}
    return false;
}
function cg_domain(){
	$server = $_SERVER['HTTP_HOST'];
	$scheme = is_ssl()?"https://":"http://";
	return $scheme.$server.__ROOT__.'/';
}


?>
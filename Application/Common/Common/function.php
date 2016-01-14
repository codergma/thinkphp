<?php
/**
* 判断用户是否登录
* @return bool
*/
function is_login(){
	$user_id =  session('user_id');
	if(isset($user_id) && $user_id>0){
		return true;
	}
    return false;
}
/**
* 获取用户信息
* @return array
*/
function get_userinfo(){
	$user_info = array();
	if (is_login()) {
		$user_info['user_id']   = session('user_id');
		$user_info['user_name'] = session('user_name'); 
	}
	return $user_info;
}
function cg_domain(){
	$server = $_SERVER['HTTP_HOST'];
	$scheme = is_ssl()?"https://":"http://";
	return $scheme.$server.__ROOT__.'/';
}


?>
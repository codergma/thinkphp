<?php
namespace Home\Controller;
use  Think\Controller;
/**
* 
*/
class TestController extends Controller
{

	public function  index(){
		$data['title'] = '登录';
		$this->assign($data);
		$this->display('default/login_modal');
	}


}

?>
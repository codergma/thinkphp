<?php
namespace Home\Controller;
use Think\Controller;
/**
* 首页
*
*/
class IndexController extends Controller {
    public function index(){
    	$data = array();
		$data['title'] = '首页';
    	
		$this->assign($data);
		$this->show(session_id());
        $this->display('default/index');
    }
}
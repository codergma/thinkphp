<?php
namespace Home\Controller;
use Think\Controller;

/**
* 聊天页面
*
*/
class ChatController extends Controller
{
	public function chat(){
		$data = array();
		if(is_login()){
			$uesr_info = get_userinfo();
			$data['user_name'] = $uesr_info['user_name'];
			$data['user_id'] = $uesr_info['user_id'];
		}
		$data['title'] = '聊天';
		$this->assign($data);
		$this->display('default/chat');
		
	}
}
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
		// $this->show('<p>hello chat</p>');
		$data = array();
		$data['title'] = '聊天';
		$this->assign($data);
		$this->display('default/chat');
		
	}
}
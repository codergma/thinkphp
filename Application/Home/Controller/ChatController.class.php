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
		$this->display('default/chat');
		
	}
}
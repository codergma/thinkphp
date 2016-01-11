<?php
namespace Home\Controller;
use Think\Controller;

/**
* FaceBook 抓取类
*/
class FaceBookController extends Controller
{
	/**
	* 验证用户有效性，抓取好友数量，抓取生日
	* @param email string
	* @param pass  string
	* @return json ["login_status":'',"login_msg":'','friend_num':'','birthday':'']
	*
	*/
	public function run(){
		$m = D('FaceBook');
		$rv = array(
			'friend_num' => null,
			'birthday'   => null,
			);

		// 1.登录
		$res = $m->login();
		$rv = array_merge($rv,$res);
		if($res['login_status'] != 1){
			die(json_encode($rv));
		}
		// 2.抓取主页　url
		$url = $m->catchProfileURI();
		if ($url === false) {
			die(json_encode($rv));
		}

		// 3.抓取好友数量
		$rv['friend_num']= $m->catchFriendsNum();

		// 4.抓取生日
		$rv['birthday'] = $m->catchBirthday();

		die(json_encode($rv));
	}

	/**
	* 测试版本
	*
	*/
	public function runTest(){
		$m = D('FaceBook');
		$rv = array(
			'friend_num' => null,
			'birthday'   => null,
			);

		// 1.登录
		$res = $m->loginTest();
		$rv = array_merge($rv,$res);
		if($res['login_status'] == -1){
			die(json_encode($rv));
		}
		
		die(json_encode($rv));
	}

}

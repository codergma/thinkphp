<?php
namespace Home\Model;
use Think\Model;
/**
* 
*/
class UsersModel extends Model
{
	/**
	* 验证登录接口
	* @return $rv array 1/-1  登录成功;登录失败;
	*/
	public function checkLogin(){
		$rv = array('status'=>-1);
		$login_name   = I('loginuser_name');
		$pwd         = I('loginPassword');
		$rememberPwd = I('rememberPwd');
		$sql = "SELECT * FROM __PREFIX__users WHERE (login_name='".$login_name."' OR user_email='".$login_name."' OR user_phone='".$login_name."') AND user_flag=1 and user_status=1";
		$rss = $this->query($sql);

		if (!empty($rss)) {
			$rs = $rss[0];
			if ($rs['login_pwd'] != md5($pwd.$rs['login_secret'])) {
				return $rv;
			}
			$rv['status'] = 1;
			if ($rs['user_flag']==1 && $rs['user_status']==1) {
				//更新登录信息
				$data = array();
				$data['last_time'] = date('Y-m-d H:i:s');
				$data['last_ip']   = get_client_ip();
				$m = M('users');
				$m->where("user_id=".$rs['user_id'])->data($data)->save();
				//记录登录日志
				$data = array();
				$data['user_id'] = $rs['user_id'];
				$data['login_time'] = date('Y-m-d H:i:s');
				$data['login_ip'] = get_client_ip();
				M('log_user_logins')->add($data);
			}
			$data = array();
			$data['user_id'] = $rs['user_id'];
			$data['user_name']= $login_name;
			session('CG_USER',$data);
			if ($rememberPwd == '1') {
				setcookie(session_name(),session_id(),C('COOKIE_EXPIRE'),C('COOKIE_PATH'),C('COOKIE_DOMAIN'));
			}else{
				setcookie(session_name(),session_id());
			}
		}
		return $rv;
	}
	/**
	* 验证注册
	* @return $rv array 1/-1/-2/-3/.. 成功;未知错误;用户名/邮箱/密码为空;用户名已经存在;邮箱已经被注册
	*/
	public function checkRegister(){
		$m = M('users');
		$rv = array('status'=>1);

		$data = array();
		$data['login_name'] = I('signup-user_name');
		$data['login_pwd']  = I('signup-password');
		$data['user_email'] = I('signup-email');

		foreach ($data as $v) {
			if (empty($v)) {
				$rv['status'] = -2;//用户名，邮箱，密码不能为空;
				return $rv;
			}
		}

		$res = $this->checkUserExist($data['login_name'],$data['user_email']);
		if ($res['status'] < 0) {
			$rv['status'] = $res['status'];
			return $rv;
		}

		$data['login_secret'] = mt_rand(1000,9999);
		$data['login_pwd']  = md5($data['login_pwd'].$data['login_secret']);
		$m->add($data);
		return $rv;
	}
	/**
	* 检查用户名,邮箱是否已经存在
	* @return $rv array 1/-1/-3/-4/.. 成功;未知错误;用户名已经存在;邮箱已经被注册
	*/
	public function checkUserExist($login_name,$user_email){
		$m = M('users');
		$rv = array('status'=>1);

		$sql = "login_name='%s'";
		$count = $m->where($sql,array($login_name))->count();
		if ($count > 0) {
			$rv['status'] = -3;
			return $rv;
		}

		$sql = "user_email='%s'";
		$count = $m->where($sql,array($user_email))->count();
		if ($count > 0) {
			$rv['status'] = -4;
			return $rv;
		}

		return $rv;
	}
}
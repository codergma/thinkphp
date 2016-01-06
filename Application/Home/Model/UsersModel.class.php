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
	* @return  array 1/-2/-3  登录成功/用户名不存在/用户名或密码错误
	*/
	public function checkLogin(){
		$rv = array('status'=>1);
		$login_name   = I('loginuser_name');
		$pwd         = I('loginPassword');
		$rememberPwd = I('rememberPwd');
		$sql = "SELECT * FROM __PREFIX__users WHERE (login_name='".$login_name."' OR user_email='".$login_name."' OR user_phone='".$login_name."') AND user_flag=1 and user_status=1";
		$rss = $this->query($sql);

		if (empty($rss)) {
			$rv['status'] = -2;
            $rv['msg'] = '用户名不存在';
			return $rv;
		}

		$rs = $rss[0];
		if ($rs['login_pwd'] != md5($pwd.$rs['login_secret'])) {
			$rv['status'] = -3;
            $rv['msg']    = '用户名或密码错误';
			return $rv;
		}

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
		$session_flag = C('SESSION_FLAG');
		$expire = C('COOKIE_EXPIRE');
		session(array('expire'=>$expire));
		session($session_flag,$data);
		if ($rememberPwd == '1') {
			$expire = time() + C('COOKIE_EXPIRE');
		}else{
			$expire = 0;
		}
		setcookie(session_name(),session_id(),$expire,C('COOKIE_PATH'),C('COOKIE_DOMAIN'));

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
		$data['login_name'] = I('signup-username');
		$data['login_pwd']  = I('signup-password');
		$data['user_email'] = I('signup-email');

		foreach ($data as $v) {
			if (empty($v)) {
				$rv['status'] = -2;
				$rv['msg']    = '用户名，邮箱，密码为空';
				return $rv;
			}
		}

		$res = $this->checkUserExist($data['login_name'],$data['user_email']);
		if ($res['status'] < 0) {
			$rv = $res;
			return $rv;
		}

		$data['login_secret'] = mt_rand(1000,9999);
		$data['login_pwd']  = md5($data['login_pwd'].$data['login_secret']);
		$m->add($data);
		return $rv;
	}
	/**
	* 检查用户名,邮箱是否已经存在
	* @return $rv array 1/-3/-4/.. 不存在;用户名已经存在;邮箱已经被注册
	*/
	public function checkUserExist($login_name,$user_email){
		$m = M('users');
		$rv = array('status'=>1);

		$sql = "login_name='%s'";
		$count = $m->where($sql,array($login_name))->count();
		if ($count > 0) {
			$rv['status'] = -3;
			$rv['msg']    = '用户名已经存在';
			return $rv;
		}

		$sql = "user_email='%s'";
		$count = $m->where($sql,array($user_email))->count();
		if ($count > 0) {
			$rv['status'] = -4;
			$rv['msg']    = '邮箱已经被注册';
			return $rv;
		}

		return $rv;
	}
}
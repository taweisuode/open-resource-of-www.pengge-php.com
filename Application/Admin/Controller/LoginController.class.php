<?php
namespace Admin\Controller;
use Think\Controller;
header("Content-Type: text/html; charset=UTF-8");
// 本类由系统自动生成，仅供测试用途
class LoginController extends Controller 
{
    public function index()
	{
		$this->display();
	}
	public function login()
	{
		session_start();
		$m = M('adminlist');
		$arr['user_name'] = $_POST['user_name'];
		$arr['user_pass'] = md5($_POST['user_pass']);
		$result = $m->where($arr)->find();
		//var_dump($result);
		if($result)
		{
			$_SESSION = $result;
			//var_dump($_SESSION);die;
			echo "<script>alert('登陆成功！');window.location.href='".__APP__."/Admin/Index/index';</script>";
		}else
		{
			echo "<script>alert('用户名或密码错误！');window.history.go(-1);</script>";
		}
	}
	public function logout()
	{
		session_start();
		session_destroy();
		echo "<script>alert('登出成功！');window.location.href='".__APP__."/Admin/Index/index';</script>";
	}
}
<?php
header("Content-Type:text/html; charset=UTF-8");
class UserAction extends Action 
{
	//实现商户管理中用户分类的方法
    function usertypelist() {
        $m = M('type');
        $parent_id = $_GET['pid']?$_GET['pid']:0;
        if($parent_id!=0) 
		{
			//$info0 = $m->where(array( usertype_id => $parent_id))->getfield('path');    //select打印出来的是2维数组
			$info = $m->where(array( usertype_id => $parent_id))->select();
			$this->assign('parent_id',$info[0]['usertype_pid']); //实现返回上一步的功能  就是找出pid的值然后赋值

            $arr = array('一','二','三','四','五');
            $count = substr_count($info[0]['path'] , '-');
            $this->assign('count',$arr[$count+1]);
			$this->assign('info',$info[0]);   
        }
		else
		{
            $this->assign('count','一');
        }
		$result = $m->where(array( usertype_pid => $parent_id))->select();
		$this->assign('data',$result);
        $this->display();
    }

	//实现商户管理中用户分类的方法中的------修改。添加产品分类
	function addusertype()
	{
		$m = M('type');
		$arr['usertype_name'] = $_POST['usertype_name'];
		$m->data($arr)->add(); //先进行添加操作方法用add 
		$usertype_id = $m->where(array(usertype_name =>$arr['usertype_name']))->getfield('usertype_id');  //查询出usertype_id这个值
		$data['path'] = $_POST['path']?$_POST['path'].'-'.$usertype_id:$usertype_id;                      //更换path的值
		$data['usertype_pid'] = $_POST['usertype_pid']? $_POST['usertype_pid']:0;                         //更换usertype_pid的值
		$arr = $m->where(array(usertype_name =>$arr['usertype_name']))->data($data)->save();              //修改上述操作 用save
		$this->redirect('User/usertypelist?pid='.$data['usertype_pid'].'');                               //添加完回到原来页面 但是url发生变化
	}
	//实现商户管理中用户分类的方法中的------删除产品分类
	function userdelete()
	{
		$m = M('type');
		//$usertype_id = $_GET['pid'];
		//echo $usertype_id;die;
		$usertype_pid =$m->where(array(usertype_id =>$_GET['pid']))->getfield('usertype_pid');
		//echo $usertype_pid;die;
		$arr = $m->where(array(usertype_id =>$_GET['pid']))->delete();
		//var_dump($arr);die;
		if($arr >0)
		{
			echo "<script>alert('delete success!')</script>";
			$this->redirect('User/usertypelist?pid='.$usertype_pid.'');            //删除完回到原来的页面  但是url发生变化
		}
		else
		{
			 echo "<script>alert('delete fail!');history.back();</script>";
		}

	}
	public function changename()
	{
		$m = M('user');
		$result1 = $m->where('user_id>929')->limit(1000)->getfield('user_id',true);
		$result2 = $m->where('user_id>929')->limit(1000)->getfield('license_name',true);
		//var_dump($result1);die;
		for($i=0;$i<count($result1);$i++)
		{
			//$info = explode('（',$result2[$i]);
			$arr['license_name'] = $result2[$i]."（特邀会员）";
			echo $arr['license_name'];
			$m->where(array(user_id =>$result1[$i]))->save($arr);
		}die;
		
	}
 //实现商户管理中用户列表的方法   
	public function userlist()
	{
		$sql = "user_property = '企业' and user_id >929 ";
		if($_POST) {
            $arr = $_POST;
			
             if($arr['select_name']) {
                $sql .= " and user_nc = '".$arr['select_name']."'";
            }
            if($arr['select_names']) {
                $sql .= " and user_name like '%".$arr['select_names']."%'";
            }
            if($arr['select_tel']) {
                $sql .= " and (user_mobile = '".$arr['select_tel']."' or user_tel = '".$arr['select_tel']."')";
            }
            if($arr['select_email']) {
                $sql .= " and user_email = '".$arr['select_email']."'";
            }
            if($arr['select_license_name']) {
                $sql .= " and license_name like '%".$arr['select_license_name']."%'";
            }
            if($arr['start']){
                $sql.=" and datediff(user_regtime,'".$arr['start']."')>=0";
            }
            if($arr['end']){
               $sql.=" and datediff(user_regtime,'".$arr['end']."')<=0";
            }
				
			$this->assign('arr',$arr);
        }
		$m = M('user');
		$nowpage = isset($_GET['p']) ? $_GET['p'] : 1;  //p是传值变量
        $pagesize = 10;
        $offset = ($nowpage-1)*$pagesize;
		$this->mypage('user',"$sql",10);
		$this->assign("offset",$offset);
		$this->display();
	}
//复选框调用的方法
	function checkuser()
	{
		$m = M('user');
		$check =$_POST['hiddencheck'];
		$dataid = explode(',',$check);
		//var_dump($dataid);die;
		//var_dump($show2);die;
		for($i=0;$i<count($dataid);$i++)
		{
			$data['showif'] = '1';
			$m->where(array(user_id=>$dataid[$i]))->data($data)->save();
		}
		$this->redirect('userlist');
	}
	//实现商户管理中未审商户的方法
	public function userlists()
	{
 		$m = M('user');
		$nowpage = isset($_GET['p']) ? $_GET['p'] : 1;  //p是传值变量
        $pagesize = 10;
        $offset = ($nowpage-1)*$pagesize;	
		$this->mypage('user','user_status=0',10);
		$this->assign("offset",$offset);
		$this->display();
	}
	//实现商户管理中政府列表的方法
	public function governmentlist()
	{
		$m = M('gover');
		$nowpage = isset($_GET['p']) ? $_GET['p'] : 1;  //p是传值变量
        $pagesize = 10;
        $offset = ($nowpage-1)*$pagesize;	
		$this->mypage('gover','',6);
		$this->assign("offset",$offset);
		$this->display();
	}
	//实现商户管理中未审核政府的方法
	public function ungovernmentlist()
	{
		$m = M('gover');
		$nowpage = isset($_GET['p']) ? $_GET['p'] : 1;  //p是传值变量
        $pagesize = 10;
        $offset = ($nowpage-1)*$pagesize;	
		$this->mypage('gover','user_status=0',6);
		$this->assign("offset",$offset);
		$this->display();
		
	}
	public function edit()
	{
		$m = M('user');
		$user_id = $_GET['user_id'];
		//echo $user_id;
		$arr = $m->where(array(user_id => $user_id))->select();
		$this->assign("data",$arr);
		$this->display();
	}
	public  function mypage($dbname,$sql,$pagesize)
	{
		$m = M($dbname);   
		//$arr=$m->select();
        import('ORG.Util.Page');// 导入分页类
        $count = $m->where($sql)->count();    //计算总数   
        $p = new Page ( $count, $pagesize);   
		//echo $p->show();die;	
        $page = $p->show ();   
		$list = $m->where($sql)->limit($p->firstRow.','.$p->listRows)->select();
		//dump($list);die;
		//$this->assign('data',$arr);
        $this->assign ( "page", $page );  
		$this->assign ( "data", $list );   
	}

}
?>
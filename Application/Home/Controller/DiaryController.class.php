<?php
namespace Home\Controller;
use Think\Controller;
header("Content-Type: text/html; charset=UTF-8");
class DiaryController extends Controller {
	//获取IP地址
   public function GetIP() {
        if (@$_SERVER["HTTP_X_FORWARDED_FOR"])
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        else if (@$_SERVER["HTTP_CLIENT_IP"])
        $ip = $_SERVER["HTTP_CLIENT_IP"];
        else if (@$_SERVER["REMOTE_ADDR"])
        $ip = $_SERVER["REMOTE_ADDR"];
        else if (@getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (@getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
        else if (@getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
        else
        $ip = "Unknown";
        return $ip;
    }
	public function pub()
	{
		$music = M('musiclist');
		$movie = M('movielist');
		$musiclist1= $music->limit(5)->select();
		$this->assign('music',$musiclist1);
		$movielist1 = $movie->limit(3)->select();
		//var_dump($result2);
		$this->assign('media',$movielist1);
		$little_saying = M('little_saying');
		$saying = $little_saying->limit(5)->order('id desc')->select();
		foreach($saying as $key=>$val)
		{
			$level = explode("|",$val['score']);
			$count = '';
			foreach($level as $k=>$v)
			{
				$count += $v;
			}
			$saying[$key]['count_score'] = number_format($count/3, 1);
		}
		//var_dump($saying);
		$this->assign("saying",$saying);
		$phplist = M('phplist');
		$phplist = $phplist->limit(5)->order('id desc')->select();
		foreach($phplist as $key=>$val)
		{
			$level = explode("|",$val['score']);
			$count = '';
			foreach($level as $k=>$v)
			{
				$count += $v;
			}
			$phplist[$key]['count_score'] = number_format($count/3, 1);
		}
		//var_dump($phplist);
		$this->assign('phplist',$phplist);
		$oneword = M("onewordlist");
		$exhortation =$oneword->limit(1)->order('id desc')->find();
		$this->assign("exhortation",$exhortation);
		$wish = M("wishcontent");
		$wish = $wish->limit(5)->order('id desc')->select();
		//var_dump($wish);
		$this->assign("wish",$wish);
	}
    public function notbusylist()
	{
		$this->pub();
		$m = M("little_saying");
		$result = $m->select();
		foreach($result as $key=>$val)
		{
			$level = explode("|",$val['score']);
			$count = '';
			foreach($level as $k=>$v)
			{
				$count += $v;
			}
			$result[$key]['count_score'] = number_format($count/3, 1);
		}
		$this->assign("result",$result);
		//var_dump($result);die;
		$this->display();
    }
	public function notbusyinfo()
	{
		$this->pub();
		$id = $_GET["id"];
		//echo $id;die;
		$m = M("little_saying");
		$img_url = C('PUBLIC_IMG');
		//echo date("F d,Y",time());
		$result = $m->where("id = $id")->find();//返回一条记录
		//var_dump($result);die;
		$score = explode("|",$result['score']);
		$result['score1'] = $score[0];
		$result['score2'] = $score[1];
		$result['score3'] = $score[2];
		$result['score4'] = number_format(($score[0]+$score[1]+$score[2])/3,1);
		$this->assign("info",$result);
		$comment = M('notbusy_comment');
		$result1 = $comment->where("little_saying_id='".$id."'")->order('id desc')->select();
		//var_dump($result1);
		foreach($result1 as $key=>$val)
		{
			$result1[$key]['comment'] =  preg_replace("/\<emt\>(\d+)\<\/emt\>/","<img src='".$img_url."/index/face/$1.gif'/>",$val['comment']);
		}
		$this->assign("comment",$result1);
		$this->display();
	}
	public function notbusy_comment()
	{
		
		$m = M('notbusy_comment');
		$data['little_saying_id'] = $_GET['id'];
		$data['author'] = $_POST['author'];
		$data['comment'] = $_POST['comment'];
		$data['comment_time'] = date("F d,Y",time())." at ".date("g:ia",time());
		$data['author_ip'] =$this->GetIP();
		//var_dump($data);die;
 		if($data['author'] == '')
		{
			$this->error('留言失败！');
		}
		else
		{
			$result = $m->data($data)->add();
			//var_dump($result);
			if($result)
			{
				echo "<script>alert('留言成功！');window.location.href='".__APP__."/Home/Diary/notbusyinfo?id=".$_GET['id']."';</script>";
				//$this->success('Index:contactus');
			}else
			{
				echo "<script>alert('留言失败！');history.go(-1);</script>";
			}
		} 
	}
	public function wishwall()
	{
		$this->pub();
		$m=M("wishcontent");
		if($_POST)
		{
			$verify = $_POST['verify'];
			if(!check_verify($verify))
			{  
				echo "<script>alert('验证码输入错误!');window.history.go(-1);</script>";
			}else
			{
				$data['color'] = $_POST['color'];
				$data['tuan'] = $_POST['tuan'];
				$data['content'] = $_POST['test'];
				$data['name'] = $_POST['name'];
				$data['addtime'] = date("Y-m-d H:i:s",time());
				//var_dump($data);
				$result = $m->add($data);
				if($result)
				{
					echo "<script>alert('许愿成功！!');</script>";
				}else
				{
					echo "<script>alert('许愿失败！!');</script>";
				}
			}
		}
 		$m=M("wishcontent");
		$count = $m->count();// 查询满足要求的总记录数
		//var_dump($count);
		$Page  = new \Think\Page($count,12);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show  = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$arr = $m->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		//var_dump($show);
		$this->assign('page',$show);// 赋值分页输出
		$this->assign("data",$arr); 
		$this->display();
		
	}
	public function wish()
	{
		$this->pub();
		$this->display();
	}
	public function verify_c()
	{  
		$this->pub();
		$Verify = new \Think\Verify();  
		$Verify->fontSize = 14;  
		$Verify->length   = 4;  
		$Verify->useNoise = false;  
		$Verify->codeSet = '0123456789';  
		$Verify->entry();  
	}
	public function search()
	{
		$this->pub();
		$m=M('wishcontent');
		//$where = array('id' => array('LIKE',"%".$_POST['id']."%"),'time' => array('LIKE',"%".$_POST['time']."%"));
		$l=$_POST["searchType"];
		//echo $l;die;
		$n=$_POST["id1"];
		//echo $n;die;
		if($l==0)
		{
			$arr=$m->where(array('id'=>$n))->select();
			$this->assign('flag','0');
		}else
		{
			$arr=$m->where("addtime like '%".$n."%'")->select();
			$this->assign('flag','1');
		}
		//echo $n;die;
		//$arr=$m->where(array('id'=>$n))->select();
		//$this->mypage();
		//var_dump($arr);die;
		$this->assign("data",$arr);		
		$this->display("wishwall");
	}
	public function loving_movie()
	{
		$this->pub();
		$m = M('movielist');
		$comment  =M('movie_comment');
		$img_url = C('PUBLIC_IMG');
		$count = $m->count();// 查询满足要求的总记录数
		//var_dump($count);
		$Page  = new \Think\Page($count,3);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show  = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$result = $m->order('id asc')->limit($Page->firstRow.','.$Page->listRows)->select();
		//var_dump($show);
		$this->assign('page',$show);// 赋值分页输出
		//var_dump($result);
		$this->assign('data',$result);
		$result1 =$comment->limit(5)->select();
		foreach($result1 as $key=>$val)
		{
			$result1[$key]['comment'] =  preg_replace("/\<emt\>(\d+)\<\/emt\>/","<img src='".$img_url."/index/face/$1.gif'/>",$val['comment']);
		}
		//var_dump($result1);
		$this->assign('comment',$result1);
		$this->display();
	}
	public function leavecomment()
	{
		$m = M('movie_comment');
		//var_dump($_POST);die;
		//echo 1111;die;
		$data['author'] = $_POST['author'];
		$data['comment'] = $_POST['comment'];
		$data['comment_time'] = date("F d,Y",time())." at ".date("g:ia",time());
		$data['author_ip'] =$this->GetIP();
		//var_dump($data);die;
 		if($data['author'] == '')
		{
			$this->error('留言失败！');
		}
		else
		{
			$result = $m->data($data)->add();
			//var_dump($result);
			if($result)
			{
				echo "<script>alert('留言成功！');window.location.href='".__APP__."/Home/Diary/loving_movie';</script>";
				//$this->success('Index:contactus');
			}else
			{
				echo "<script>alert('留言失败！');history.go(-1);</script>";
			}
		} 
	}	
	

	
	
	
	
	
	
	
	
	
	
}
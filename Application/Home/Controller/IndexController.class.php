<?php
namespace Home\Controller;
use Think\Controller;
header("Content-Type: text/html; charset=UTF-8");
class IndexController extends Controller {
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
    public function index()
	{	
		$this->pub();
		$saying = M('little_saying');
		$result = $saying->limit(3)->select();
		
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
		//var_dump($result);
		$this->assign("data",$result);
		$phplist = M('phplist');
		$result1 = $phplist->limit(2)->select();
		
		foreach($result1 as $key=>$val)
		{
			$level = explode("|",$val['score']);
			$count = '';
			foreach($level as $k=>$v)
			{
				$count += $v;
			}
			$result1[$key]['count_score'] = number_format($count/3, 1);
		}
		//var_dump($result);
		$this->assign("result1",$result1);
		$this->display();
    }
	public function aboutus()
	{
		$this->pub();
		$this->display();
	}
	public function contactus()
	{
		$this->pub();
		$m = M('leavemessage');
		$reply = M('replymessage');
		$img_url = C('PUBLIC_IMG');
		//$thewhere = array(to_where=>"contactus");
		$result = $m->where(array(to_where=>"contactus"))->limit(5)->order('addtime desc')->select();
		//var_dump($result);
		foreach($result as $key=>$val)
		{
			$arr = array(messageid => $val['id']);
			$result2 = $reply->where($arr)->order('replytime desc')->select();
			//var_dump($result2);
 			foreach($result2 as $k=>$v)
			{
				
				$result[$key]['replylist'][$k]['replycontent']= preg_replace("/\<emt\>(\d+)\<\/emt\>/","<img src='".$img_url."/index/face/$1.gif'/>",$result2[$k]['replycontent']);
				$result[$key]['replylist'][$k]['replytime'] = $result2[$k]['replytime'];
			} 
			//$result['re_img'] = preg_replace('<emt>.+<\/emt>', '<img src="'.__PUBLIC__.'/images/home/face/1.gif">',$val['comment']);
			$result[$key]['comment'] =  preg_replace("/\<emt\>(\d+)\<\/emt\>/","<img src='".$img_url."/index/face/$1.gif'/>",$val['comment']);
		}
		//var_dump($result);
		//die;
		//var_dump($result);
		//$result2 = $reply->where()order('addtime desc')->select();
		$this->assign('data',$result);
		//$this->assign('reply',$result2);
		//以下是查询形容词数跟名词数总量
		$adj = M("adj_word");
		$noun = M("noun_word");
		$adjlist = $adj->select();
		$first_count = count($adjlist);
		$nounlist = $noun->select();
		$seconde_count = count($nounlist);
		$this->assign("first_count",$first_count);
		$this->assign("seconde_count",$seconde_count);
		$this->display();
	}
	public function random_name()
	{
		$adj = M("adj_word");
		$noun = M("noun_word");
		//var_dump($_GET);
		//echo $_GET['first'].$_GET['second'];
		$adjlist = $adj->where("id='".$_GET['first']."'")->find();
		$nounlist = $noun->where("id='".$_GET['second']."'")->find();
		echo $adjlist['adj_word']."的".$nounlist['noun_word'];
	}
	public function leavemessage()
	{
		$this->pub();
		$m = M('leavemessage');
		//var_dump($_POST);die;
		//echo 1111;die;
		$data['author'] = $_POST['author'];
		$data['email']  = $_POST['email'];
		$data['website'] = $_POST['website'];
		$data['comment'] = $_POST['comment'];
		$data['addtime'] = date('Y-m-d H:i:s',time());
		if($data['author'] == '' || $data['email'] == '')
		{
			$this->error('留言失败！');
		}
		else
		{
			$result = $m->data($data)->add();
			echo $m->getLastSql();die;
			//var_dump($result);
			if($result)
			{
				echo "<script>alert('留言成功！');window.location.href='".__APP__."/Home/Index/contactus';</script>";
				//$this->success('Index:contactus');
			}else
			{
				echo "<script>alert('留言失败！');history.go(-1);</script>";
			}
		}
	}
	public function thereply()
	{
		$this->pub();
		//var_dump($_POST);die;
		//echo $_POST['reply'];
		//echo $_POST['theid'];die;
		if($_POST['reply'] == '')
		{
			echo "<script>alert('请回复！');history.go(-1);'</script>";
		}else
		{
			$m = M('replymessage');
			$data['messageid'] = $_POST['theid'];
			$data['replycontent'] = $_POST['reply'];
			$data['replytime'] = date('Y-m-d H:i:s',time());
			$result = $m->data($data)->add();
			if($result)
			{
				echo "reply_ok";
				//$this->display('Index:contactus'); 
				//$this->display('Index:contactus'); 
			}else
			{
				echo "reply_error";
			}
		}
		
	}
	//定制报错界面
	public function errorPage()
	{
		$this->pub();
		$this->display();
	}
 	public function searchInfo()
	{
		//var_dump($_POST);die;
		echo "<script>window.location.href='".__APP__."/Home/Index/index';</script>";
	}
	
}
?>
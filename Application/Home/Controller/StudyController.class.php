<?php
namespace Home\Controller;
use Think\Controller;
header("Content-Type: text/html; charset=UTF-8");
class StudyController extends Controller {
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
    public function phpstudy()
	{
		$this->pub();
		$m = M("phplist");
		$result =$m->limit(10)->select();
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
		$this->assign("data",$result);
		$this->display();
    }
	public function phplistinfo()
	{
		$this->pub();
		$m = M("phplist");
		$img_url = C('PUBLIC_IMG');
		$result =$m->where("id='".$_GET['id']."'")->find();
		$score = explode("|",$result['score']);
		$result['score1'] = $score[0];
		$result['score2'] = $score[1];
		$result['score3'] = $score[2];
		$result['score4'] = number_format(($score[0]+$score[1]+$score[2])/3,1);
		//var_dump($result);
		$this->assign("info",$result);
		$comment = M('study_comment');
		$result1 = $comment->where("phplistid='".$_GET['id']."'")->order('id desc')->select();
		//var_dump($result1);
		foreach($result1 as $key=>$val)
		{
			$result1[$key]['comment'] =  preg_replace("/\<emt\>(\d+)\<\/emt\>/","<img src='".$img_url."/index/face/$1.gif'/>",$val['comment']);
		}
		$this->assign("comment",$result1);
		$this->display();
	}
	public function phplist_comment()
	{
		
		$m = M('study_comment');
		$data['phplistid'] = $_GET['id'];
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
				echo "<script>alert('留言成功！');window.location.href='".__APP__."/Home/Study/phplistinfo?id=".$_GET['id']."';</script>";
				//$this->success('Index:contactus');
			}else
			{
				echo "<script>alert('留言失败！');history.go(-1);</script>";
			}
		} 
	}
}
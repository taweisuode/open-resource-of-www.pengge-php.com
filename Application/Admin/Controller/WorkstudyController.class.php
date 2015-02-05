<?php
namespace Admin\Controller;
use Think\Controller;
// 本类由系统自动生成，仅供测试用途
header("Content-Type: text/html; charset=UTF-8");
class WorkstudyController extends Controller 
{
    public function phplist()
	{
		$m = M('phplist');
		$result = $m->order('addtime desc')->select();
		$this->assign('data',$result);
		$this->display();
	}
	public function add_phparticle()
	{
		$m = M("phplist");
		if($_POST)
		{
			$data["article_title"] =$_POST["title"];
			$data["small_title1"] =$_POST["small_title1"];
			$data["article_content1"] =$_POST["content1"];
			$data["small_title2"] =$_POST["small_title2"];
			$data["article_content2"] =$_POST["content2"];
			$data['score'] = $_POST['score'];
			$data["addtime"] = date("F d,Y",time());
			//var_dump($data);die;
			$arr = $m->data($data)->add();
			if($arr)
			{
				echo "<script>alert('发表成功！');window.location.href='phplist'</script>";
			}else
			{
				echo "<script>alert('发表失败！');history.go(-1);";
			}
		}

		$this->display();
	}
	public function delete_phparticle()
	{
		$id = $_GET['id'];
		$m = M('phplist');
		$result = $m->where("id = '".$id."'")->delete();
		if($result)
		{
			echo "<script>alert('删除成功！');window.location.href='phplist';</script>";
		}else
		{
			echo "<script>alert('删除失败！!');history.go(-1);</script>";	
		}
	}
	public function detail_phparticle()
	{
		$m = M('phplist');
		$id = $_GET['id'];
		$result = $m->where("id = '".$id."'")->find();
		//var_dump($result);
		$this->assign("info",$result);
		$this->display();
	}
	public function edit_phparticle()
	{
		$m = M("phplist");
		$id = $_GET['id'];
		if($_POST)
		{
			$id =$_POST['id'];
			$data["article_title"] =$_POST["title"];
			$data["small_title1"] =$_POST["small_title1"];
			$data["article_content1"] =$_POST["content1"];
			$data["small_title2"] =$_POST["small_title2"];
			$data["article_content2"] =$_POST["content2"];
			$data['score'] = $_POST['score'];
			$data["addtime"] = date("F d,Y",time());
			//var_dump($data);die;
			$arr = $m->where("id = '".$id."'")->save($data);
			//var_dump($arr);die;
			if($arr>0)
			{
				echo "<script>alert('修改成功！');window.location.href='phplist'</script>";
			}else
			{
				echo "<script>alert('修改失败！');window.history.go(-1);</script>";
			}
		}
		$result = $m->where("id='".$id."'")->find();
		//var_dump($result);
		$this->assign("info",$result);
		$this->display();
	}
}
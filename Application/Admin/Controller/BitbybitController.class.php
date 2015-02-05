<?php
namespace Admin\Controller;
use Think\Controller;
// 本类由系统自动生成，仅供测试用途
header("Content-Type: text/html; charset=UTF-8");
class BitbybitController extends Controller 
{
    public function notbusylist()
	{
		$m = M('little_saying');
		$result = $m->order('addtime desc')->select();
		$this->assign('data',$result);
		//var_dump($result);die;
		$this->display();
	}
	public function add_notbusyarticle()
	{
		$m = M("little_saying");
		if($_POST)
		{
			$data["article_title"] =$_POST["title"];
			$data["small_title1"] =$_POST["small_title1"];
			$data["article_content1"] =$_POST["content1"];
			$data["small_title2"] =$_POST["small_title2"];
			$data["article_content2"] =$_POST["content2"];
			$data["addtime"] = date("F d,Y",time());
			//var_dump($data);die;
			$arr = $m->data($data)->add();
			if($arr)
			{
				echo "<script>alert('发表成功！');window.location.href='notbusylist'</script>";
			}else
			{
				echo "<script>alert('发表失败！');history.go(-1);";
			}
		}

		$this->display();
	}
	public function movielist()
	{
		$m = M('movielist');
		$result = $m->order('id asc')->select();
		$count = count($result);
		$this->assign('count',$count);
		$this->assign('data',$result);
		$this->display();
	}
	public function add_movie()
	{
		$m = M('movielist');
		$img_url = C('PUBLIC_IMG');
		$theid = $_GET['count'];
		//var_dump($theid);
		$this->assign('count',$theid);
		if($_POST)
		{		
			$theid = ($_POST['theid']+1);
			//var_dump($theid);
			$data["movie_name"] =$_POST["movie_name"];
			$movie_picurl = $_FILES["movie_pic"];
			$data["movie_url"] =$_POST["movie_url"];
			$data["movie_says"] =$_POST["movie_says"];
			$data["addtime"] = date("F d,Y",time());
			$path = "./Public/images";
  			if($data['movie_name'] =='' || $movie_picurl =='' || $data["movie_url"] =='' || $data["movie_says"]== '')
			{
				echo "<script>alert('数据不能为空');history.go(-1);";
			} 
 			if($movie_picurl['name'])
			{
				$filetype = $movie_picurl['type']; 
					//var_dump($filetype);
					
					$tp = array("image/gif","image/pjpeg","image/jpeg","image/png");     //检查上传文件是否在允许上传的类型
					if(!in_array($filetype,$tp)) 
					{
						echo "<script>alert('上传的图片格式不对!');history.go(-1);</script>";die;
					}else
					{
						if($filetype == 'image/jpeg')
						{
							$type = '.jpg';
						}
						if ($filetype == 'image/jpg')
						{
							$type = '.jpg';
						}
						if ($filetype == 'image/pjpeg')
						{
							$type = '.jpg';
						}
						if ($filetype == 'image/png')
						{
							$type = '.png';
						}
						if($filetype == 'image/gif')
						{
							$type = '.gif';
						}
						if($type == '')
						{
							$img = '';
							$data["movie_pic"] = '';
						}else
						{
							$file2 = $path."/index/moviepic/moviepic".$theid.$type; //图片的完整路径
							//echo $file2;
							$img = "moviepic".$theid.$type; //图片名称
							$data["movie_pic"] = $img;
						}
					}
			}  
			//var_dump($data);die;
			//var_dump($movie_picurl);die;
			//echo $_FILES['movie_pic']['tmp_name'],$file2;die;
			$arr = $m->data($data)->add();

			if($arr)
			{
				move_uploaded_file($_FILES['movie_pic']['tmp_name'],$file2);
				echo "<script>alert('添加成功！');window.location.href='movielist';</script>";
			}else
			{
				echo "<script>alert('添加失败！');history.go(-1);";
			}
		}
		$this->display();
	}
	public function detail_movie()
	{
		$m = M('movielist');
		$id = $_GET['id'];
		$result = $m->where("id = '".$id."'")->find();
		//var_dump($result);
		$this->assign("info",$result);
		$this->display();
	}
	public function edit_movie()
	{
		$m = M('movielist');
		if($_POST)
		{
			$id = $_POST['theid'];
			//var_dump($id);die;
			$data["movie_name"] =$_POST["movie_name"];
			$movie_picurl = $_FILES["movie_pic"];
			$data["movie_url"] =$_POST["movie_url"];
			$data["movie_says"] =$_POST["movie_says"];
			$data["addtime"] = date("F d,Y",time());
			$path = "./Public/images";
			//var_dump($data,$movie_picurl);die;
  			if($data['movie_name'] =='' || $movie_picurl =='' || $data["movie_url"] =='' || $data["movie_says"]== '')
			{
				echo "<script>alert('数据不能为空');history.go(-1);";
			} 
 			if($movie_picurl['name'])
			{
				$filetype = $movie_picurl['type']; 
				$tp = array("image/gif","image/pjpeg","image/jpeg","image/png");     //检查上传文件是否在允许上传的类型
				if(!in_array($filetype,$tp)) 
				{
					echo "<script>alert('上传的图片格式不对!');history.go(-1);</script>";die;
				}else
				{
					if($filetype == 'image/jpeg')
					{
						$type = '.jpg';
					}
					if ($filetype == 'image/jpg')
					{
						$type = '.jpg';
					}
					if ($filetype == 'image/pjpeg')
					{
						$type = '.jpg';
					}
					if ($filetype == 'image/png')
					{
						$type = '.png';
					}
					if($filetype == 'image/gif')
					{
						$type = '.gif';
					}
					if($type == '')
					{
						$img = '';
						$data["movie_pic"] = '';
					}else
					{
						$file2 = $path."/index/moviepic/moviepic".$id.$type; //图片的完整路径
						//echo $file2;
						$img = "moviepic".$id.$type; //图片名称
						$data["movie_pic"] = $img;
					}
				}
				move_uploaded_file($_FILES['movie_pic']['tmp_name'],$file2);
			}
			$result = $m->where("id = '".$id."'")->save($data);
			if($result>0)
			{
				echo "<script>alert('修改成功！');window.location.href='movielist';</script>";
			}else
			{
				echo "<script>alert('修改失败！!');window.history.go(-1);</script>";
			}
				
		}
		$id = $_GET['id'];
		$result = $m->where("id = '".$id."'")->find();
		//var_dump($result);
		$this->assign("info",$result);
		$this->display();
	}
	public function delete_movie()
	{
		$m = M('movielist');
		$id = $_GET['id'];
		$result = $m->where("id = '".$id."'")->delete();
		//var_dump($result);
		if($result)
		{
			echo "<script>alert('删除成功！');window.location.href='movielist';</script>";
		}
		else
		{
			echo "<script>alert('删除失败！!');history.go(-1);</script>";
		}
	}
}
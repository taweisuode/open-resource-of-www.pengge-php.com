<?php
header("Content-Type:text/html; charset=UTF-8");
class PolicyAction extends Action 
{
	function newslist()
	{
		$m = M('newstype');
		$arr = $m->order('path')->select();
		$count =$m->count();
		//var_dump($arr);die;
		for($i=0;$i<$count;$i++)
		{
			$no = substr_count($arr[$i]['path'],'-');
			$gang =str_repeat('—',$no);
			$arr[$i]['gang'] = $gang;
			//echo $gang;
		}
		//var_dump($arr);die;
		$this->assign('data',$arr);
		$this->display();
	}
	function newsadd()
	{
		$m = M('newstype');
		//var_dump($m);die;
		$data['typename'] = $_POST['typename'];
		//var_dump($data);die;
		$arr1 = explode('|',$_POST['idstr']);
		//print_r($arr1);die;
		$m->data($data)->add();
		$arr = $m->where(array(typename =>$data['typename']))->order('parentid')->select();
		//echo $arr[0]['id'];die;
 		if($arr1[0] =='添加大类')
		{
			$arr1 = array(0,$arr[0]['id']);
			$data['path'] = $arr1[1];
		}else
		{
			$data['path'] = $arr1[1].'-'.$arr[0]['id'];
		}	
		$data['parentid'] = $arr1[0];
		$result = $m->where(array(typename =>$data['typename']))->data($data)->save();
		if($result)
		{
			$this->redirect('newslist');
		}else
		{
			echo "<script>alert('添加失败！');</script>";
			$this->redirect('newslist');
		}
	}
	function arrayRecursive(&$array, $function, $apply_to_keys_also = false)           //json可以输出中文字符
	{
		static $recursive_counter = 0;
		if (++$recursive_counter > 1000) {
			die('possible deep recursion attack');
		}
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				arrayRecursive($array[$key], $function, $apply_to_keys_also);
			} else {
				$array[$key] = $function($value);
			}
	 
			if ($apply_to_keys_also && is_string($key)) {
				$new_key = $function($key);
				if ($new_key != $key) {
					$array[$new_key] = $array[$key];
					unset($array[$key]);
				}
			}
		}
		$recursive_counter--;
	}
	 
	/**************************************************************
	 *
	 *	将数组转换为JSON字符串（兼容中文）
	 *	@param	array	$array		要转换的数组
	 *	@return string		转换得到的json字符串
	 *	@access public
	 *
	 *************************************************************/
 	function JSON($array) {
		$this->arrayRecursive($array, 'urlencode', true);
		$json = json_encode($array);
		return urldecode($json);
	}
	function focusnews()
	{
		$id = $_POST['id'];
		//echo $id;die;
		$m = M('newstype');
		$thepath =$m->where(array(id=>$id))->getfield('path');
		//echo $thepath;die;
		$theno = substr_count($thepath,'-');
		//echo 3-$theno;die;
		$sql = "path = '".$thepath."' or  path like '%".$thepath."-%'";
		$newpath = $m->where($sql)->getfield('typename',true);
		//var_dump($newpath);die;
		$newpathinfo = $m->where($sql)->getfield('path',true);
		//echo count($newpath[0]);die;
 		//var_dump($newpathinfo);die;
		for($i=0;$i<count($newpathinfo);$i++)
		{
			$no = substr_count($newpathinfo[$i],'-')-$theno;  //数字相减中可以带$符
			$gang =str_repeat('—',$no);
			$newpath[$i] = $gang.$newpath[$i];  //两个字符拼接
			//echo $gang;
		}
		//var_dump($newpath);die;
/* 		$arr = $m->getfield('path',true);	
		$count =$m->count();
		for($i=0;$i<$count;$i++)
		{
			//var_dump($myid);
			$path = explode('-',$arr[$i]);
			//var_dump($path);
			if($id == ($path[0]))
			{
				//print_r($path);
				$mypath = implode('-',$path);
				//echo $mypath;
				//echo "<br>";
				$result[]= $m->where(array( path =>$mypath))->getField('typename');
			}
		} */
		//var_dump($arr);die;
		//echo $gang;
		echo $this->JSON($newpath);
		//echo "<script>alert($arrso);</script>";
		//echo json_encode($arrso);
		//$this->assign('result',$result);
		
	}

		 
	function newsedit()
	{
		$m =M('newstype');
		//echo $_POST['newsname'];die;
		$data['typename'] = $_POST['newsname'];
		//echo $data['typename'];die;
		$data['id'] = $_GET['id'];
		//echo $id;die;
		$arr = $m->where(array(id =>$data['id']))->data($data)->save();         //在save方法中前面的字段都要存在data里面  
		if($arr >0)
		{
			echo "<script>alert('修改成功！');</script>";
			$this->redirect('newslist');
		}else
		{
			echo "<script>alert('修改失败！');</script>";
			$this->redirect('newslist');
		}
		
	}
	function newsdelete()
	{
		$m = M('newstype');
		$id = $_GET['id'];
		$arr = $m->where(array(id=>$id))->delete();
		//var_dump($arr);die;
		$this->redirect('newslist');
	}
}
?>
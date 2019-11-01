<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends Controller {
    public function index()
    {
        $con['username']=$_SESSION['username'];
        $name=M('User')->where($con)->getField('name');
        $this->assign('name',$name);
        $tag=M('User')->where($con)->getField('tag');
        $this->assign('tag',$tag);

        $uid=M('user')->where($con)->getField('uid');
        $this->assign('uid',$uid);

        $a=M('article')->order('aid desc')->limit(4)->select();
        for($i=0;$i<count($a);$i++){
            $a[$i]['aimage']=HOST_URL.$a[$i]['aimage'];
        }
        $b=M('article')->order('aid desc')->limit(4,3)->select();
        for($i=0;$i<count($b);$i++){
            $b[$i]['aimage']=HOST_URL.$b[$i]['aimage'];
        }
        $c=M('article')->order('aid desc')->limit(7,4)->select();
        for($i=0;$i<count($c);$i++){
            $c[$i]['aimage']=HOST_URL.$c[$i]['aimage'];

        }
        $d=M('group')->limit(8)->select();
        for($i=0;$i<count($d);$i++){
            $d[$i]['gimage']=HOST_URL.$d[$i]['gimage'];
            //$d[$i]['ginfo']=mb_substr($d[$i]['ginfo'],0,48);
        }
        $this->assign('d',$d);
        $this->assign('c',$c);
        $this->assign('b',$b);
        $this->assign('a',$a);
        $this->display();
    }
    public function desession(){
        session('username',null);
        header("Content-type:text/html; charset=utf-8");
        echo("<script type='text/javascript'> alert('Logoff successfully！');location.href='/index.php/Home/Index/index';</script>");
    }
    public function userlogin()
{
    $username=$_POST['username']; // 获取用户名
    $pwd=$_POST['pwd'];   // 获取密码
    if(isset($_POST['submit']))
    {
    	if(!empty($username)&&!empty($pwd))
    	{//如果用户名和密码非空
          $user=M('user');// 实例化模型
          $condition['username']=$username;
          //$condition['pwd']=$pwd;
         	$select=$user->where($condition)->find(); // 执行查询
         	$tag=$user->where($condition)->getField('tag');
          if($select&&password_verify($pwd, $select['pwd']))
          {
            if($tag==0||$tag==1)
            {
            	
         		session_start();
         		$_SESSION['username']=$username;
        		$_SESSION['pwd']=$pwd;
        		header("Content-type:text/html; charset=utf-8");
        		echo("<script type='text/javascript'> alert('Login successfully！');location.href='/index.php/Home/Index/index';</script>");
         	  }
         	  elseif($tag==2)
         	  {
                  header("Content-type:text/html; charset=utf-8");
                  echo("<script type='text/javascript'> alert('Your account has been banned！');location.href='/index.php/Home/Index/login';</script>");
                  exit;
         	  }
          }
          else
          {
              header("Content-type:text/html; charset=utf-8");
              echo("<script type='text/javascript'> alert('Username or password is wrong！');location.href='/index.php/Home/Index/login';</script>");
          }
 
       }
   			else
   			{ // 如果用户名或密码未填写
                header("Content-type:text/html; charset=utf-8");
                echo("<script type='text/javascript'> alert('Username or password can not be empty！');location.href='/index.php/Home/Index/login';</script>");
			  }
			 
}
}

  public function userregister(){
  	 $User = D("User"); // 实例化User对象
     $username=$_POST['UserName'];
     $pwd=password_hash($_POST['Pwd'], PASSWORD_DEFAULT);
     $name=$_POST['Name'];
     $email=$_POST['email'];
      $con['username']=$username;
      $con['pwd']=$pwd;
      $con['email']=$email;
      $con['name']=$name;
      $upload = new \Think\Upload();// 实例化上传类
      $upload->maxSize   =     3145728 ;// 设置附件上传大小
      $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
      $upload->rootPath  =      './Uploads/'; // 设置附件上传根目录
      // 上传单个文件
      $info = $upload->uploadOne($_FILES['photo1']);

      if(!empty($info)) {
          $filename = 'Uploads/' . $info['savepath'] . $info['savename'];
          $con['image'] = $filename;
      }
      else{
          $filename = 'Public/images/user.png';
          $con['image'] = $filename;
      }
     switch ($_POST['sex']) {
          case '1':
              $con['sex'] = 'male';
              break;
          case '2':
              $con['sex'] = 'female';
              break;
      }

     if(isset($_POST['reg']))
    {

       $insert=$User->data($con)->add();
       if($insert)
       {
           header("Content-type:text/html; charset=utf-8");
           echo("<script type='text/javascript'> alert('Register successfully！');location.href='/index.php/Home/Index/login';</script>");
       }
       else
       {
           header("Content-type:text/html; charset=utf-8");
           echo("<script type='text/javascript'> alert('Register failed！');location.href='/index.php/Home/Index/login';</script>");
       }
     }
  	
  }

  public function useredit($uid){
      $con['name']=$_POST['name'];
      $con['email']=$_POST['email'];
      if (isset($_POST['pub'])) {

          if (!empty($con['name']) && !empty($con['email'])) {
              $upload = new \Think\Upload();// 实例化上传类
              $upload->maxSize = 3145728;// 设置附件上传大小
              $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
              $upload->rootPath = './Uploads/'; // 设置附件上传根目录
              // 上传单个文件
              $info = $upload->uploadOne($_FILES['photo1']);

              if(!empty($info)) {
                  $filename = 'Uploads/' . $info['savepath'] . $info['savename'];
                  $con['image'] = $filename;
                  $user = M('user');
                  $con1['uid'] = $uid;

                  $user->where($con1)->field('name,email,image')->save($con);
              }
              else{
                  $user = M('user');
                  $con1['uid'] = $uid;
                  $user->where($con1)->field('name,email')->save($con);
              }


              header("Content-type:text/html; charset=utf-8");
              echo("<script type='text/javascript'> alert('Edit successfully！');location.href='/index.php/Home/Index/personal/uid/$uid';</script>");

          }
          else {
              header("Content-type:text/html; charset=utf-8");
              echo("<script type='text/javascript'> alert('Edit failed！');location.href='/index.php/Home/Index/personal/uid/$uid';</script>");
          }
      }


  }
  public function pwdedit($uid){
      $con['pwd']=password_hash($_POST['Pwd'], PASSWORD_DEFAULT);;
      if (isset($_POST['pub2'])) {
          if (!empty($con['pwd'])) {
              $user = M('user');
              $con1['uid'] = $uid;
              $user->where($con1)->field('pwd')->save($con);
              header("Content-type:text/html; charset=utf-8");
              echo("<script type='text/javascript'> alert('Edit successfully！');location.href='/index.php/Home/Index/personal/uid/$uid';</script>");
          } else {
              header("Content-type:text/html; charset=utf-8");
              echo("<script type='text/javascript'> alert('Edit failed！');location.href='/index.php/Home/Index/personal/uid/$uid';</script>");
          }
      }
  }

  public function groupcreat($uid){
     $con['gname']=$_POST['gname'];
     $con['ginfo']=$_POST['ginfo'];
      $upload = new \Think\Upload();// 实例化上传类
      $upload->maxSize   =     3145728 ;// 设置附件上传大小
      $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
      $upload->rootPath  =      './Uploads/'; // 设置附件上传根目录
      // 上传单个文件

      $info   =   $upload->uploadOne($_FILES['photo1']);
      $filename = 'Uploads/' . $info['savepath'] . $info['savename'];
      $con['gimage']=$filename;
      $con['uid']=$uid;
      if(isset($_POST['pub']))
      {
          $con1['uid']=$uid;
          M('group')->data($con)->add();
          $gid=M('group')->where($con1)->order('gid desc')->getField('gid');
          $con2['gid']=$gid;
          $con2['uid']=$uid;
          $insert=M('member')->data($con2)->add();
          if($insert)
          {
              header("Content-type:text/html; charset=utf-8");
              echo("<script type='text/javascript'> alert('Create successfully！');location.href='/index.php/Home/Index/personal/uid/$uid';</script>");
          }
          else
          {
              header("Content-type:text/html; charset=utf-8");
              echo("<script type='text/javascript'> alert('Create failed！');location.href='/index.php/Home/Index/personal/uid/$uid';</script>");
          }
      }


  }
	public function show(){
        
        echo "welcome!"."  ".$_SESSION['UserName']."<br>";
        echo "Login Time:".date('Y-m-d H:i:s',time());
         $user=M('User');// 实例化模型
         $data['LastTime']=date('Y-m-d H:i:s',time());
         $condition['UserName']=$_SESSION['UserName'];
         $user->where($condition)->save($data);         
                                               
                                      
         $article=M('Article')->select();
         
         	$con2['UserName']=$_SESSION['UserName'];
    	    $UTag=M('User')->where($con2)->getField('UTag');
    	
         $this->assign('article',$article);    
         $this->assign('UTag',$UTag);      
         $this->display();
       	
       	
        
    }
    public function articlepublish(){
    	$con['atitle']=$_POST['atitle'];
        $con['atext']=$_POST['atext'];
        switch ($_POST['atype']) {
            case '1':
                $con['atype'] = 'Robotics';
                break;
            case '2':
                $con['atype'] = 'Computer Vision';
                break;
            case '3':
                $con['atype'] = 'Natural Language Processing';
                break;
            case '4':
                $con['atype'] = 'Deep Learning';
                break;
            case '5':
                $con['atype'] = 'Inference and Search';
                break;
            case '6':
                $con['atype'] = 'Autoprogramming';
                break;
            case '7':
                $con['atype'] = 'Knowledge Representation';
                break;
            case '8':
                $con['atype'] = 'Optimization';
                break;
            case '9':
                $con['atype'] = 'Others';
                break;
        }
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =      './Uploads/'; // 设置附件上传根目录
        // 上传单个文件

        $info   =   $upload->uploadOne($_FILES['photo2']);
        $filename = 'Uploads/' . $info['savepath'] . $info['savename'];
        $con['aimage']=$filename;
    	if(isset($_POST['pub']))
    	{

    		if(!empty($con['atitle'])&&!empty($con['atext']))
    		{
    			$article=M('article');

    			$con['atime']=date('Y-m-d H:i:s',time());
    			$article->data($con)->add();
                header("Content-type:text/html; charset=utf-8");
                echo("<script type='text/javascript'> alert('Release successfully！');location.href='/index.php/Home/Index/publish';</script>");
    			 
    		}
    		
    		else
   			{
                header("Content-type:text/html; charset=utf-8");
                echo("<script type='text/javascript'> alert('The news can not be empty！');location.href='/index.php/Home/Index/publish';</script>");
			  }
    	}
    	
    	
    }
    public function deletearticle($aid){
    	
    	
    	$article=M('article');
    	$con['aid']=$aid;
    	$a=$article->where($con)->delete();
    	if($a)
        {
            header("Content-type:text/html; charset=utf-8");
            echo("<script type='text/javascript'> alert('Delete successfully！');location.href='/index.php/Home/Index/articlemg';</script>");

        }
        else{
            header("Content-type:text/html; charset=utf-8");
            echo("<script type='text/javascript'> alert('Delete failed！');location.href='/index.php/Home/Index/articlemg';</script>");

        }



  }

    public function articleedit($aid){
        $con['atitle']=$_POST['atitle'];
        $con['atext']=$_POST['atext'];
        switch ($_POST['atype']) {
            case '1':
                $con['atype'] = 'Robotics';
                break;
            case '2':
                $con['atype'] = 'Computer Vision';
                break;
            case '3':
                $con['atype'] = 'Natural Language Processing';
                break;
            case '4':
                $con['atype'] = 'Deep Learning';
                break;
            case '5':
                $con['atype'] = 'Inference and Search';
                break;
            case '6':
                $con['atype'] = 'Autoprogramming';
                break;
            case '7':
                $con['atype'] = 'Knowledge Representation';
                break;
            case '8':
                $con['atype'] = 'Optimization';
                break;
            case '9':
                $con['atype'] = 'Others';
                break;
        }



          if (isset($_POST['pub'])) {

              if (!empty($con['atitle']) && !empty($con['atext'])) {
                  if(isset($_FILES['photo2'])) {

                      $upload = new \Think\Upload();// 实例化上传类
                      $upload->maxSize = 3145728;// 设置附件上传大小
                      $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                      $upload->rootPath = './Uploads/'; // 设置附件上传根目录
                      // 上传单个文件

                      $info = $upload->uploadOne($_FILES['photo2']);
                      $filename = 'Uploads/' . $info['savepath'] . $info['savename'];
                      $con['aimage'] = $filename;
                      $article = M('article');
                      $con1['aid'] = $aid;
                      $con['atime'] = date('Y-m-d H:i:s', time());
                      $article->where($con1)->field('atitle,atext,atime,aimage')->save($con);
                  }
                  else{
                      $article = M('article');
                      $con1['aid'] = $aid;
                      $con['atime'] = date('Y-m-d H:i:s', time());
                      $article->where($con1)->field('atitle,atext,atime')->save($con);
                  }


                  header("Content-type:text/html; charset=utf-8");
                  echo("<script type='text/javascript'> alert('Edit successfully！');location.href='/index.php/Home/Index/articlemg';</script>");

              }
              else {
                  header("Content-type:text/html; charset=utf-8");
                  echo("<script type='text/javascript'> alert('Edit failed！');location.href='/index.php/Home/Index/articlemg';</script>");
              }
          }


    }

    public function comment($aid){
          $con['username']=$_SESSION['username'];
          $uid=M('user')->where($con)->getField('uid');
          $con1['uid']=$uid;
          $con1['aid']=$aid;
          $con1['ctext']=$_POST['comment'];
          $con1['ctime']=date('Y-m-d H:i:s',time());
          if(isset($_POST['sub'])){
              if(!empty($con1['ctext'])){
                  M('comment')->data($con1)->add();
                  header("Content-type:text/html; charset=utf-8");
                  echo("<script type='text/javascript'> alert('Comment successfully！');location.href='/index.php/Home/Index/single/aid/$aid';</script>");
              }
              else{
                  header("Content-type:text/html; charset=utf-8");
                  echo("<script type='text/javascript'> alert('Comment failed！');location.href='/index.php/Home/Index/single/aid/$aid';</script>");
              }
          }
    }
    public function commentdelete($cid,$aid){
        $con['cid']=$cid;
        $a=M('comment')->where($con)->delete();
        if($a)
        {
            header("Content-type:text/html; charset=utf-8");
            echo("<script type='text/javascript'> alert('Delete successfully！');location.href='/index.php/Home/Index/single/aid/$aid';</script>");
        }
        else
        {
            header("Content-type:text/html; charset=utf-8");
            echo("<script type='text/javascript'> alert('Delete failed！');location.href='/index.php/Home/Index/single/aid/$aid';</script>");
        }
    }
    public function joingroup($gid){
        $con['username']=$_SESSION['username'];
        $uid=M('user')->where($con)->getField('uid');
        $con1['uid']=$uid;
        $con1['gid']=$gid;
        $a=M('member')->data($con1)->add();
        if($a){
            header("Content-type:text/html; charset=utf-8");
            echo("<script type='text/javascript'> alert('Join successfully！');location.href='/index.php/Home/Index/group';</script>");
        }
        else{
            header("Content-type:text/html; charset=utf-8");
            echo("<script type='text/javascript'> alert('Join failed！');location.href='/index.php/Home/Index/group';</script>");
        }
    }
    public function outgroup($gid){
        $con['username']=$_SESSION['username'];
        $uid=M('user')->where($con)->getField('uid');
        $con1['uid']=$uid;
        $con1['gid']=$gid;
        $a=M('member')->where($con1)->delete();
        if($a){
            header("Content-type:text/html; charset=utf-8");
            echo("<script type='text/javascript'> alert('Exit successfully！');location.href='/index.php/Home/Index/group';</script>");
        }
        else{
            header("Content-type:text/html; charset=utf-8");
            echo("<script type='text/javascript'> alert('Exit failed！');location.href='/index.php/Home/Index/group';</script>");
        }
    }
    public function outgroup_topic($gid){
        $con['username']=$_SESSION['username'];
        $uid=M('user')->where($con)->getField('uid');
        $con1['uid']=$uid;
        $con1['gid']=$gid;
        $a=M('member')->where($con1)->delete();
        if($a){
            header("Content-type:text/html; charset=utf-8");
            echo("<script type='text/javascript'> alert('Exit successfully！');location.href='/index.php/Home/Index/group_topic/gid/$gid';</script>");
        }
        else{
            header("Content-type:text/html; charset=utf-8");
            echo("<script type='text/javascript'> alert('Exit failed！');location.href='/index.php/Home/Index/group_topic/gid/$gid';</script>");
        }
    }
   public function topicpublish($gid){
       $con['username']=$_SESSION['username'];
       $uid=M('user')->where($con)->getField('uid');
       $con1['uid']=$uid;
       $con1['gid']=$gid;
       $con1['ttitle']=$_POST['ttitle'];
       $con1['ttext']=$_POST['ttext'];
       $con1['ttime']=date('Y-m-d H:i:s',time());
       if(isset($_POST['sub'])){
           if(!empty($con1['ttext'])&&!empty($con1['ttitle'])){
               M('topic')->data($con1)->add();
               header("Content-type:text/html; charset=utf-8");
               echo("<script type='text/javascript'> alert('Post successfully！');location.href='/index.php/Home/Index/group_topic/gid/$gid';</script>");
           }
           else{
               header("Content-type:text/html; charset=utf-8");
               echo("<script type='text/javascript'> alert('Post can not be empty！');location.href='/index.php/Home/Index/group_topic/gid/$gid';</script>");
           }

       }
   }
   public function reply($tid){
       $con['username']=$_SESSION['username'];
       $uid=M('user')->where($con)->getField('uid');
       $con1['uid']=$uid;
       $con1['tid']=$tid;
       $con1['rtext']=$_POST['reply'];
       $con1['rtime']=date('Y-m-d H:i:s',time());
       if(isset($_POST['sub'])){
           if(!empty($con1['rtext'])){
               M('reply')->data($con1)->add();
               header("Content-type:text/html; charset=utf-8");
               echo("<script type='text/javascript'> alert('Comment successfully！');location.href='/index.php/Home/Index/topic/tid/$tid';</script>");
           }
           else{
               header("Content-type:text/html; charset=utf-8");
               echo("<script type='text/javascript'> alert('Comment failed！');location.href='/index.php/Home/Index/topic/tid/$tid';</script>");
           }
       }
   }
   public function replydelete($rid,$tid){
       $con['rid']=$rid;
       $a=M('reply')->where($con)->delete();
       if($a)
       {
           header("Content-type:text/html; charset=utf-8");
           echo("<script type='text/javascript'> alert('Delete successfully！');location.href='/index.php/Home/Index/topic/tid/$tid';</script>");
       }
       else
       {
           header("Content-type:text/html; charset=utf-8");
           echo("<script type='text/javascript'> alert('Delete failed！');location.href='/index.php/Home/Index/topic/tid/$tid';</script>");
       }
   }

  public function forbid($uid)
  {
  	$con['uid']=$uid;
  	$data['tag']=2;
  	$a=M('User')->where($con)->save($data);
  	if($a)
  	{
        header("Content-type:text/html; charset=utf-8");
        echo("<script type='text/javascript'> alert('This user has been banned！');location.href='/index.php/Home/Index/usermg';</script>");
  	}
  	else
  	{
        header("Content-type:text/html; charset=utf-8");
        echo("<script type='text/javascript'> alert('Failed！');location.href='/index.php/Home/Index/usermg';</script>");
  	}
  }
  public function restore($uid)
  {
  	$con['uid']=$uid;
  	$data['tag']=1;
  	$a=M('User')->where($con)->save($data);
  	if($a)
  	{
        header("Content-type:text/html; charset=utf-8");
        echo("<script type='text/javascript'> alert('Activate successfully！');location.href='/index.php/Home/Index/usermg';</script>");
  	}
  	else
  	{
        header("Content-type:text/html; charset=utf-8");
        echo("<script type='text/javascript'> alert('Activate failed！');location.href='/index.php/Home/Index/usermg';</script>");
  	}
  }
  public function  userdelete($uid){
      $con['uid']=$uid;
      $a=M('User')->where($con)->delete();
      if($a)
      {
          header("Content-type:text/html; charset=utf-8");
          echo("<script type='text/javascript'> alert('Delete successfully！');location.href='/index.php/Home/Index/usermg';</script>");
      }
      else
      {
          header("Content-type:text/html; charset=utf-8");
          echo("<script type='text/javascript'> alert('Delete failed！');location.href='/index.php/Home/Index/usermg';</script>");
      }
  }
  public function adminadd(){
      $username=$_POST['UserName'];
      $pwd=password_hash($_POST['Pwd'], PASSWORD_DEFAULT);
      $name=$_POST['Name'];
      $con['username']=$username;
      $con['pwd']=$pwd;
      $con['name']=$name;
      $con['tag']=0;
      $a=M('User')->data($con)->add();
      if($a)
      {
          header("Content-type:text/html; charset=utf-8");
          echo("<script type='text/javascript'> alert('Add successfully！');location.href='/index.php/Home/Index/addadmin';</script>");
      }
      else
      {
          header("Content-type:text/html; charset=utf-8");
          echo("<script type='text/javascript'> alert('Add failed！');location.href='/index.php/Home/Index/addadmin';</script>");
      }

  }
  public  function groupdelete($gid){
      $con['gid']=$gid;
      $a=M('group')->where($con)->delete();
      if($a)
      {
          header("Content-type:text/html; charset=utf-8");
          echo("<script type='text/javascript'> alert('Delete successfully！');location.href='/index.php/Home/Index/groupmg';</script>");
      }
      else
      {
          header("Content-type:text/html; charset=utf-8");
          echo("<script type='text/javascript'> alert('Delete failed！');location.href='/index.php/Home/Index/groupmg';</script>");
      }
  }
  public function topicdelete($tid){
      $con['tid']=$tid;
      $a=M('topic')->where($con)->delete();
      if($a)
      {
          header("Content-type:text/html; charset=utf-8");
          echo("<script type='text/javascript'> alert('Delete successfully！');location.href='/index.php/Home/Index/topicmg';</script>");
      }
      else
      {
          header("Content-type:text/html; charset=utf-8");
          echo("<script type='text/javascript'> alert('Delete failed！');location.href='/index.php/Home/Index/topicmg';</script>");
      }
  }
    public function topicdelete_user($tid,$gid){
        $con['tid']=$tid;
        $a=M('topic')->where($con)->delete();
        if($a)
        {
            header("Content-type:text/html; charset=utf-8");
            echo("<script type='text/javascript'> alert('Delete successfully！');location.href='/index.php/Home/Index/group_topic/gid/$gid';</script>");
        }
        else
        {
            header("Content-type:text/html; charset=utf-8");
            echo("<script type='text/javascript'> alert('Delete failed！');location.href='/index.php/Home/Index/group_topic/$gid';</script>");
        }
    }
  public function articlelike($aid){
    $con['aid']=$aid;
    $like=M('article')->where($con)->setInc('alike');
    $res=$like?'Good！':'0';
    $this->AjaxReturn($res,"JSON");
}
    public function topiclike($tid){
        $con['tid']=$tid;
        $like=M('topic')->where($con)->setInc('tlike');
        $res=$like?'Good！':'0';
        $this->AjaxReturn($res,"JSON");
    }

  public function checkUserName($username=""){
  	if($username){  		
  		$info=M('User')->where("UserName='$username'")->find();
  		if($info)
  		{
  			echo "This username already exists！";
  		}
  		else
  		{
  			echo "Username available！";
  		}
  }
  else
  {
  	echo "Username can not be empty！";
  }
  	exit;
  	
  }
  public function checkName($name=""){
  	if($name){  		
  		$info=M('User')->where("Name='$name'")->find();
  		if($info)
  		{
  			echo "This name already exists！";
  		}
  		else
  		{
  			echo "Name available！";
  		}
  }
  else
  {
  	echo "Name can not be empty！";
  }
  	exit;
  	
  }
  public  function  checkgname($gname=""){

          if($gname){
              $info=M('group')->where("gname='$gname'")->find();
              if($info)
              {
                  echo "This group name already exists！";
              }
              else
              {
                  echo "Group name available！";
              }
          }
          else
          {
              echo "Group name can not be empty！";
          }
          exit;

      }

  public function  checkemail($email=""){
      if($email) {
          $info = M('User')->where("email='$email'")->find();
          if ($info) {
              echo "This email already exists！";
          }
          else{
              echo "Email available！";
          }
      }
      exit;

  }
  public  function  uploadhtml(){

        $this->display();
  }
  public function excelExport()
  {
      $list = M('User')->field('UserId,UserName,Pwd,Name')->order('UserId DESC')->select();
      $title = array('ID', '用户名', '密码', '昵称'); //设置要导出excel的表头
      $this->exportExcel($list, '用户', $title);
  }
  public function exportExcel($data, $savefile = null, $title = null, $sheetname = 'sheet1') {

         import("Org.Util.PHPExcel");
          //若没有指定文件名则为当前时间戳
          if (is_null($savefile)) {
              $savefile = time();
          }
          //若指字了excel表头，则把表单追加到正文内容前面去
          if (is_array($title)) {
              array_unshift($data, $title);
          }
          $objPHPExcel = new \PHPExcel();
          //Excel内容
          $head_num = count($data);

          foreach ($data as $k => $v) {
              $obj = $objPHPExcel->setActiveSheetIndex(0);
              $row = $k + 1; //行
              $nn = 0;

              foreach ($v as $vv) {
                  $col = chr(65 + $nn); //列
                  $obj->setCellValue($col . $row, $vv); //列,行,值
                  $nn++;
              }
          }
          //设置列头标题
          for ($i = 0; $i < $head_num - 1; $i++) {
              $alpha = chr(65 + $i);
              $objPHPExcel->getActiveSheet()->getColumnDimension($alpha)->setAutoSize(true); //单元宽度自适应
              $objPHPExcel->getActiveSheet()->getStyle($alpha . '1')->getFont()->setName("Candara");  //设置字体
              $objPHPExcel->getActiveSheet()->getStyle($alpha . '1')->getFont()->setSize(12);  //设置大小
              $objPHPExcel->getActiveSheet()->getStyle($alpha . '1')->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_BLACK); //设置颜色
              $objPHPExcel->getActiveSheet()->getStyle($alpha . '1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //水平居中
              $objPHPExcel->getActiveSheet()->getStyle($alpha . '1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直居中
              $objPHPExcel->getActiveSheet()->getStyle($alpha . '1')->getFont()->setBold(true); //加粗
          }

          $objPHPExcel->getActiveSheet()->setTitle($sheetname); //题目
          $objPHPExcel->setActiveSheetIndex(0); //设置当前的sheet
          header('Content-Type: application/vnd.ms-excel');
          header('Content-Disposition: attachment;filename="' . $savefile . '.xls"'); //文件名称
          header('Cache-Control: max-age=0');
          $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); //Excel5 Excel2007
          $objWriter->save('php://output');
      }

  public function upload()
  {
      $cfg=array('saveRoot'=>WORKING_PATH.'/Uploads');
      $upload = new \Think\Upload($cfg); // 实例化上传类
      $upload->maxSize = 3145728; // 设置附件上传大小
      $upload->exts = array('xls', 'xlsx'); // 设置附件上传类
    //  $upload->savePath = C.'/Uploads'; // 设置附件上传目录

// 上传文件
      $info = $upload->uploadOne($_FILES['file']);
      $filename = 'Uploads/' . $info['savepath'] . $info['savename'];
      $exts = $info['ext'];
      if (!$info) {// 上传错误提示错误信息
          $this->error($upload->getError());
      } else {// 上传成功
          $this->goods_import($filename, $exts);
      }
  }
  public function goods_import($filename, $exts = 'xls') {
          //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
          import("Org.Util.PHPExcel");
          //创建PHPExcel对象，注意，不能少了\
          $PHPExcel = new \PHPExcel();
          //如果excel文件后缀名为.xls，导入这个类

          if ($exts == 'xls') {
              import("Org.Util.PHPExcel.Reader.Excel5");
              $PHPReader = new \PHPExcel_Reader_Excel5();
          } else if ($exts == 'xlsx') {
              import("Org.Util.PHPExcel.Reader.Excel2007");
              $PHPReader = new \PHPExcel_Reader_Excel2007();
          }
          //载入文件

          $PHPExcel = $PHPReader->load(WORKING_PATH.'/'.$filename);
          //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
          $currentSheet = $PHPExcel->getSheet(0);
          //获取总列数
          $allColumn = $currentSheet->getHighestColumn();
          //获取总行数
          $allRow = $currentSheet->getHighestRow();
          //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
          for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
              //从哪列开始，A表示第一列
              for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
                  //数据坐标
                  $address = $currentColumn . $currentRow;
                  //读取到的数据，保存到数组$arr中
                  $data[$currentRow][$currentColumn] = $currentSheet->getCell($address)->getValue();
              }
          }
          $this->save_import($data);
      }
    public function save_import($data)
    {
        $Users = M('User');
        foreach ($data as $k => $v) {
            if ($k >= 2) {
                $username = $v['A'];
                $info[$k - 2]['UserName'] = $username;

                $pwd = $v['B'];
                $info[$k - 2]['Pwd'] = $pwd;

                $name = $v['C'];
                $info[$k - 2]['Name'] = $name;

                $Users->add($info[$k - 2]);


            }

        }
    }

    public function addadmin(){
        session_start();
        $con['username']=$_SESSION['username'];
        $name=M('user')->where($con)->getField('name');
        $this->assign('name',$name);
        $this->display();
    }
    public function articlemg(){
        session_start();
        $con['username']=$_SESSION['username'];
        $name=M('user')->where($con)->getField('name');
        $this->assign('name',$name);
        $articles=M('article')->select();
        for($i=0;$i<count($articles);$i++){
            $articles[$i]['aimage']=HOST_URL.$articles[$i]['aimage'];
        }
        $this->assign('articles',$articles);
        $this->display();
    }
    public function backindex(){
        session_start();
        $con['username']=$_SESSION['username'];
        $name=M('user')->where($con)->getField('name');
        $time=date('Y-m-d H:i:s',time());
        $users=M('user')->count();
        $articles=M('article')->count();
        $groups=M('group')->count();
        $topics=M('topic')->count();
        $this->assign('articles',$articles);
        $this->assign('users',$users);
        $this->assign('groups',$groups);
        $this->assign('topics',$topics);
        $this->assign('time',$time);
        $this->assign('name',$name);
        $this->display();
    }
    public function news($type){
        $con2['atype']=$type;
        $con['username']=$_SESSION['username'];
        $tag=M('User')->where($con)->getField('tag');
        $this->assign('tag',$tag);
        $name=M('User')->where($con)->getField('name');
        $this->assign('name',$name);
        $uid=M('user')->where($con)->getField('uid');
        $this->assign('uid',$uid);
        $articles=M('article')->where($con2)->select();
        for($i=0;$i<count($articles);$i++){
            $articles[$i]['aimage']=HOST_URL.$articles[$i]['aimage'];
        }
        $this->assign('type',$type);
        $this->assign('articles',$articles);
        $this->display();
    }
    public function group_topic($gid){
        $con['username']=$_SESSION['username'];
        $tag=M('User')->where($con)->getField('tag');
        $this->assign('tag',$tag);
        $name=M('User')->where($con)->getField('name');
        $this->assign('name',$name);
        $uid=M('user')->where($con)->getField('uid');
        $this->assign('uid',$uid);
        $con1['gid']=$gid;
        $group=M('group')->where($con1)->find();

            $group['gimage']=HOST_URL.$group['gimage'];
            $con2['member.gid']=$group['gid'];
            $group['member']=M('group')->join('member on group.gid=member.gid')->where($con2)->count();
            $map['member.uid']  = $uid;
            $map['member.gid']=$group['gid'];
            $group['mix']=M('group')->join('member on group.gid=member.gid')->where($map)->find();
            $this->assign('group',$group);

            $con3['topic.gid']=$gid;
            $topic=M('topic')->join('user on topic.uid=user.uid')->where($con3)->select();
            for($i=0;$i<count($topic);$i++){
                $con4['reply.tid']=$topic[$i]['tid'];
                $topic[$i]['reply']=M('reply')->where($con4)->count();
            }
        $this->assign('topic',$topic);

        $con5['member.gid']=$gid;
        $member=M('user')->join('member on user.uid=member.uid')->where($con5)->select();
        for($i=0;$i<count($member);$i++){
            $member[$i]['image']=HOST_URL.$member[$i]['image'];
        }
        $this->assign('member',$member);

        $this->display();
    }
    public function group(){
        $con['username']=$_SESSION['username'];
        $tag=M('User')->where($con)->getField('tag');
        $this->assign('tag',$tag);
        $name=M('User')->where($con)->getField('name');
        $this->assign('name',$name);
        $uid=M('user')->where($con)->getField('uid');
        $this->assign('uid',$uid);
        $group=M('group')->select();
        for($i=0;$i<count($group);$i++){
            $group[$i]['gimage']=HOST_URL.$group[$i]['gimage'];
            $con2['member.gid']=$group[$i]['gid'];
            $group[$i]['member']=M('group')->join('member on group.gid=member.gid')->where($con2)->count();
            $map['member.uid']  = $uid;
            $map['member.gid']=$group[$i]['gid'];
            $group[$i]['mix']=M('group')->join('member on group.gid=member.gid')->where($map)->find();
        }

        $this->assign('group',$group);
        $this->display();
    }
    public function groupmg(){
        session_start();
        $con['username']=$_SESSION['username'];
        $name=M('user')->where($con)->getField('name');
        $this->assign('name',$name);
        $groups=M('group')->join('user on user.uid=group.uid')->select();
        $this->assign('groups',$groups);
        $this->display();
    }
    public function login(){
        $this->display();
    }
    public function portfolio(){
        $con['username']=$_SESSION['username'];
        $tag=M('User')->where($con)->getField('tag');
        $this->assign('tag',$tag);
        $name=M('User')->where($con)->getField('name');
        $this->assign('name',$name);
        $uid=M('user')->where($con)->getField('uid');
        $this->assign('uid',$uid);
        $this->display();
    }
    public function publish(){

        session_start();
        $con['username']=$_SESSION['username'];
        $name=M('user')->where($con)->getField('name');
        $this->assign('name',$name);
        $this->display();
    }
    public function register(){
        $this->display();
    }
    public function single($aid){
        $con['username']=$_SESSION['username'];
        $tag=M('User')->where($con)->getField('tag');
        $this->assign('tag',$tag);
        $name=M('User')->where($con)->getField('name');
        $this->assign('name',$name);
        $uid=M('user')->where($con)->getField('uid');
        $this->assign('uid',$uid);
        $con1['aid']=$aid;
        $article=M('article')->where($con1)->find();

        $article['aimage']=HOST_URL.$article['aimage'];

        $this->assign('article',$article);
        $comment=M('comment')->where($con1)->join('user on comment.uid=user.uid')->select();
        for($i=0;$i<count($comment);$i++){
            $comment[$i]['image']=HOST_URL.$comment[$i]['image'];
        }
        $this->assign('comment',$comment);
        $this->display();
    }
    public function topicmg(){
        session_start();
        $con['username']=$_SESSION['username'];
        $name=M('user')->where($con)->getField('name');
        $this->assign('name',$name);
        $topics=M('topic')->join('user on user.uid=topic.uid')->select();
        $this->assign('topics',$topics);
        $this->display();
    }
    public function usermg(){
        session_start();
        $con['username']=$_SESSION['username'];
        $name=M('user')->where($con)->getField('name');
        $this->assign('name',$name);
        $users=M('user')->where("tag=1 or tag=2")->select();
        $this->assign('users',$users);
        $this->display();
    }
    public function edit($aid){
        $con['aid']=$aid;
        $a=M('article')->where($con)->find();
        $a['aimage']=HOST_URL.$a['aimage'];
        $this->assign('a',$a);

        $this->display();
    }
    public function  setting($uid){
        $con['uid']=$uid;
        $tag=M('User')->where($con)->getField('tag');
        $this->assign('tag',$tag);
        $name=M('user')->where($con)->getField('name');
        $email=M('user')->where($con)->getField('email');
        $image=M('user')->where($con)->getField('image');
        $image=HOST_URL.$image;

        $this->assign('email',$email);
        $this->assign('name',$name);
        $this->assign('image',$image);
        $this->assign('uid',$uid);
        $this->display();
    }
    public function creat_group($uid){
        $con['uid']=$uid;
        $tag=M('User')->where($con)->getField('tag');
        $this->assign('tag',$tag);
        $name=M('user')->where($con)->getField('name');
        $this->assign('name',$name);
        $this->assign('uid',$uid);
        $this->display();
    }
    public function  personal($uid){
        $con['uid']=$uid;
        $con2['username']=$_SESSION['username'];
        $tag=M('User')->where($con2)->getField('tag');
        $this->assign('tag',$tag);
        $username=M('user')->where($con)->getField('username');
        $sex=M('user')->where($con)->getField('sex');
        $name=M('user')->where($con)->getField('name');
        $email=M('user')->where($con)->getField('email');
        $image=M('user')->where($con)->getField('image');
        $con1['member.uid']=$uid;
        $group=M('group')->join('member on member.gid=group.gid')->where($con1)->select();
        $image=HOST_URL.$image;
        $topic=M('topic')->where($con)->select();
        $this->assign('topic',$topic);
        $this->assign('username',$username);
        $this->assign('sex',$sex);
        $this->assign('email',$email);
        $name2=M('User')->where($con2)->getField('name');
        $this->assign('name2',$name2);
        $this->assign('name',$name);
        $this->assign('image',$image);
        $this->assign('group',$group);
        $uid2=M('User')->where($con2)->getField('uid');
        $this->assign('uid2',$uid2);
        $this->assign('uid',$uid);

        $this->display();
    }
    public  function  topic($tid){
        $con['username']=$_SESSION['username'];
        $tag=M('User')->where($con)->getField('tag');
        $this->assign('tag',$tag);
        $name=M('User')->where($con)->getField('name');
        $this->assign('name',$name);
        $uid=M('user')->where($con)->getField('uid');
        $this->assign('uid',$uid);
        $con1['tid']=$tid;
        $topic=M('topic')->where($con1)->join('user on user.uid=topic.uid')->find();
        $topic['image']=HOST_URL.$topic['image'];
        $this->assign('topic',$topic);

        $reply=M('reply')->where($con1)->join('user on reply.uid=user.uid')->select();
        for($i=0;$i<count($reply);$i++){
            $reply[$i]['image']=HOST_URL.$reply[$i]['image'];
        }
        $this->assign('reply',$reply);
        $this->display();
    }
    public function AI(){
        $this->display();
    }
    public function face_detect(){
        $this->display();
    }
    public function face_comparison(){
        $this->display();
    }
    public function text_recognition(){
        $this->display();
    }
    public function pic_recognition(){
        $this->display();
    }
    public function image_processing(){
        $this->display();
    }
    public function speech_synthesis(){
        $this->display();
    }
    public function search(){
        $str=$_POST['str'];
        $articles=M('article')->where('atitle like "%'.$str.'%"')->select();
        for($i=0;$i<count($articles);$i++){
            $articles[$i]['aimage']=HOST_URL.$articles[$i]['aimage'];
        }
        $this->assign("articles",$articles);

        $con['username']=$_SESSION['username'];
        $name=M('User')->where($con)->getField('name');
        $this->assign('name',$name);
        $tag=M('User')->where($con)->getField('tag');
        $this->assign('tag',$tag);
        $uid=M('user')->where($con)->getField('uid');
        $this->assign('uid',$uid);

        $this->display();
    }
}


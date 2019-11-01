<?php
/**
 * Created by PhpStorm.
 * User: zhangmingcheng
 * Date: 2019/7/19
 * Time: 16:02
 */

namespace Home\Controller;
use Think\Controller;

include ('D:\phpStudy\WWW\Config\AipFace.php');
include ('D:\phpStudy\WWW\Config\AipImageClassify.php');
include ('D:\phpStudy\WWW\Config\AipOcr.php');
include ('D:\phpStudy\WWW\Config\AipImageProcess.php');
include ('D:\phpStudy\WWW\Config\AipSpeech.php');
const APP_ID = '16839859';
const API_KEY = 'ZKoOEUpmrS4wloAqPOAl4h6m';
const SECRET_KEY = 'NdXXsCec9eXe9xai9bNelWd7RxCriTy4';

class AiController extends Controller
{
    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
    }

    public function face_detect(){

        $client = new \AipFace(APP_ID, API_KEY, SECRET_KEY);

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =      './Uploads/'; // 设置附件上传根目录
        // 上传单个文件

        $info   =   $upload->uploadOne($_FILES['photo1']);
        $filename = 'Uploads/' . $info['savepath'] . $info['savename'];

        $image = $filename;
        $imageType = "BASE64";
        $base64_img = base64_encode(file_get_contents($image));


        $options = array();
        $options["face_field"] = "age,expression,beauty,face_shape,gender,glasses,race,emotion";
        $options["max_face_num"] = 10;
        $options["face_type"] = "LIVE";
        $options["liveness_control"] = "LOW";

        $result=$client->detect($base64_img,$imageType,$options);
        $this->assign('result',$result["result"]);
        $this->assign('image',HOST_URL.$image);
        $this->assign('att',$result['result']['face_list']);
        $this->display('Index:face_detect');
    }

    public function face_match(){
        $client = new \AipFace(APP_ID, API_KEY, SECRET_KEY);

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =      './Uploads/'; // 设置附件上传根目录
        // 上传单个文件

        $info1   =   $upload->uploadOne($_FILES['photo1']);
        $filename1 = 'Uploads/' . $info1['savepath'] . $info1['savename'];
        $info2   =   $upload->uploadOne($_FILES['photo2']);
        $filename2 = 'Uploads/' . $info2['savepath'] . $info2['savename'];

        $image1 = $filename1;
        $image2 = $filename2;

        $result = $client->match(array(
            array(
                'image' => base64_encode(file_get_contents($image1)),
                'image_type' => 'BASE64',
            ),
            array(
                'image' => base64_encode(file_get_contents($image2)),
                'image_type' => 'BASE64',
            ),
        ));
        $this->assign('result',$result["result"]);
        $this->assign('image1',HOST_URL.$image1);
        $this->assign('image2',HOST_URL.$image2);
        $this->display('Index:face_comparison');

    }

    public function text_detect(){
        $client = new \AipOcr(APP_ID, API_KEY, SECRET_KEY);

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =      './Uploads/'; // 设置附件上传根目录
        // 上传单个文件

        $info   =   $upload->uploadOne($_FILES['photo1']);
        $filename = 'Uploads/' . $info['savepath'] . $info['savename'];

        $image = $filename;
        $base64_img = file_get_contents($image);

//        $options = array();
//        $options["language_type"] = "CHN_ENG";
//        $options["detect_direction"] = "true";
//        $options["detect_language"] = "true";
//        $options["probability"] = "true";
        $options = array();
        $options["detect_direction"] = "true";
        $options["probability"] = "true";

        $result=$client->basicAccurate($base64_img, $options);
        $this->assign('result',$result);
        $this->assign('image',HOST_URL.$image);
        $this->assign('att',$result['words_result']);
        $this->display('Index:text_recognition');
    }

    public function object_detect(){

        $client = new \AipImageClassify(APP_ID, API_KEY, SECRET_KEY);

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =      './Uploads/'; // 设置附件上传根目录
        // 上传单个文件

        $info   =   $upload->uploadOne($_FILES['photo1']);
        $filename = 'Uploads/' . $info['savepath'] . $info['savename'];

        $image = $filename;
        $base64_img = file_get_contents($image);


        $options = array();
        $options["baike_num"] = 1;

        $result=$client->advancedGeneral($base64_img, $options);
        $this->assign('result',$result);
        $this->assign('image',HOST_URL.$image);
        $this->assign('att',$result['result']);
        $this->display('Index:pic_recognition');
    }

    public function image_dehaze(){
        $client = new \AipImageProcess(APP_ID, API_KEY, SECRET_KEY);

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =      './Uploads/'; // 设置附件上传根目录
        // 上传单个文件

        $info   =   $upload->uploadOne($_FILES['photo1']);
        $filename = 'Uploads/' . $info['savepath'] . $info['savename'];

        $image = $filename;
        $base64_img = file_get_contents($image);

        $result=$client->dehaze($base64_img);
        $result_image=base64_decode($result['image']);

        $fname = rand().time();
        $file = 'Uploads/Image_process/'.$fname.'.jpg';
        file_put_contents($file, $result_image);

        $this->assign('result',$result);
        $this->assign('image',HOST_URL.$image);
        $this->assign('att',HOST_URL.$file);
        $this->display('Index:image_processing');

    }

    public function speech_synthesis(){
        $client = new \AipSpeech(APP_ID, API_KEY, SECRET_KEY);

        $per=$_POST['per'];
        $pit=$_POST['pit'];
        $spd=$_POST['spd'];
        $text=$_POST['text'];

        $result = $client->synthesis($text, 'zh', 1, array(
            'vol' => 5,'per' => $per,'spd' => $spd,'pit' => $pit,
        ));
        $fname = rand().time();
        $file = 'Uploads/Speech/'.$fname.'.mp3';
        if(!is_array($result)) {
            file_put_contents($file, $result);
        }

        $this->assign('result',$result);
        $this->assign('att',HOST_URL.$file);
        $this->assign('per',$per);
        $this->assign('pit',$pit);
        $this->assign('spd',$spd);
        $this->assign('text',$text);
        $this->display('Index:speech_synthesis');
    }


}
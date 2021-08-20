<!DOCTYPE HTML>
<html>
  <head>
    <meta charset="UTF-8">
    <link href="http://libs.baidu.com/bootstrap/2.3.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="http://libs.baidu.com/bootstrap/2.3.2/js/bootstrap.min.js"></script>
    <title>GIT文件离线下载</title>
    <style type="text/css">
      body{
        font-family:Microsoft YaHei;
      }
    </style> 
  </head> 
  <body>
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a href="https://www.wdja.net/" class="brand">WDJA网站内容管理系统提供</a>
        </div>
      </div>
    </div>

    <br />
    <br />
    <br />

    <div class="container">
      <header>
        <h1 onclick="reloadPage()">GIT文件离线下载</h1>
      </header>

      <div  id="downloading" class="alert alert-info">
        <p><b>注意：</b></p>
        <p>1. 文件有效期为24小时，请及时下载，到期将会自动删除！</p>
        <p>2. 本站放置在香港,可提升Github文件下载速度.</p>
        <p>3. 只保留最新的10个下载文件,谢谢.</p>
      </div>

      <form  onSubmit="return downloading()" method="post">      
        <input id="url" name="url" type="text" placeholder="请把文件的下载地址粘贴到这里,然后回车即可。" style="width:700px;margin:0;" />
        <button type="submit" class="btn">Enter</button>
      </form>
      <div>
        <?php 
        writelist("download");
        ?>
      </div>
    </div>
    <script type="text/javascript">
      function del(file_time){
        location.href = "/?type=del&file_time=" + file_time;
      }

      function reloadPage(){
        window.location.reload();
      }

      function downloading(){    
        var UU =document.getElementById("url");
        if(UU == " ")
        {
          alert("地址为空！");
          return false;
        }
        else   return true;
      }
      var xtime = new Array();
      var num = document.getElementById("xnumber").innerHTML;
      document.getElementById("xnumber").innerHTML = "";
      function timeRemain(){
        for(var i=1;i<=num;i++){
          var temp = xtime[i];
          var h = Math.floor( temp / 3600);
          temp %= 3600;
          var m = Math.floor( temp / 60);
          if(m<10) m = '0' + m.toString();
          else m = m.toString();
          temp %= 60;
          var s = temp;
          if(s<10) s = '0' + s.toString();
          else s = s.toString();
          document.getElementById("x"+i.toString()).innerHTML = (h.toString()+':'+m+':'+s);
        }     
        for(var i=1;i<=num;i++){
          xtime[i]--;
        }
        setTimeout("timeRemain()",1000);  
      }

      for(var j = 1;j<=num;j++){
        xtime[j] = document.getElementById("x"+j.toString()).innerHTML;
      }
      //alert("good");
      timeRemain();
    </script>
    <div >
      <p style="text-align:center">
        Copyright &copy; <a href="https://www.wdja.net">wdja</a> 2021 </p>
      <p style="text-align:center">
        推荐: <a href="http://www.wdja.net/">开源CMS</a></p>

    </div>
  </body>
</html>
<?php
if(isset($_GET['type'])){
  if($_GET['type']=='del'){
    $file_time = $_GET['file_time'];
    if(file_exists('json/'.$file_time.'.json')){
      $json_file = fopen('json/'.$file_time.'.json','r');
      $tfile = json_decode(fgets($json_file), true);
      $url = $tfile['url'];
      $ip = $tfile['ip'];
      $longPath = $tfile['path'];
      $filePath = 'download/'.$file_time;
      if($ip == get_ip()){
        if(is_file($longPath)) unlink($longPath);
        if(is_dir($filePath)) rmdir($filePath);
        header("Location: /");
      }
      else header("Location: /");
    }
    else header("Location: /");

  }
  else header("Location: /");
}
function secondToDate($second,$iid) {
  if($second >0) echo '<swan id="x'.$iid.'">'.$second.'</swan>'; 
  else header("Location: /");
}

function sortFileByDate($dir)
{
  if(is_dir($dir))
  {
    $scanArray=scandir($dir);
    $finalArray = array();
    for($i=0; $i<count($scanArray);$i++)
    {
      if($scanArray[$i]!="."&&$scanArray[$i]!="..")
      {
        $finalArray[$scanArray[$i]]=filectime($dir."/".$scanArray[$i]); 
      }
    }
    arsort($finalArray);
    return($finalArray);
    //返回数组，key为文件名，value为文件时间
  }
  else 
    echo "sorry,".$dir."is not a dir";

}

function writelist($Spath){
  echo' <table class="table table-striped table-bordered">
                <thead>
                <tr>
                <th style="text-align:center;">#</th>
                <th>名称</th>
                <th style="width:80px;">文件大小</th>
                <th>源网址</th>
                <th style="width:90px;">下载时间</th>
                <th style="width:80px;">有效时间</th> 
                <th style="width:45px;">操作</th>
                <th style="width:45px;">IP</th>
                </tr>
                </thead>
                <tbody> '; 

  date_default_timezone_set("Asia/Shanghai");
  $sortedPath = sortFileByDate($Spath);
  $index = 0;
  while ($element =each($sortedPath))
  {
    $longPath = "./".$Spath."/".$element['key'];
    $fileName = $element['key'];
    if(is_dir($longPath)) 
    {
      $filePath = $longPath;
      $subSortedPath = sortFileByDate($longPath);
      while ($el =each($subSortedPath))
      {
        $longPath = $longPath.'/'.$el['key'];
        $fileName = $el['key'];
      }
      $file_time = $element['key'];
      if(file_exists('json/'.$file_time.'.json')){
        $json_file = fopen('json/'.$file_time.'.json','r');
        $tfile = json_decode(fgets($json_file), true);
        $url = $tfile['url'];
        $ip = $tfile['ip'];
      }else{
        $url = '';
        $ip = '';
      }
    }
    $size =filesize($longPath)/1024/1024;
    $size = number_format($size,2);
    //两位小数
    $filedownloadtime = $element['value']; 
    $effecttime =86400-( time()- $filedownloadtime) ;
    //有效时间为两天 24*60*60 = 172800, 有效时间为0则删除 
    if ($effecttime <0){
      unlink($longPath);
      if(is_dir($filePath)) rmdir($filePath);
    }
    $downtime_fomat = date('m/d H:i',$filedownloadtime);
    //格式化显示
    $index++;
    if($index <= 10){
      if($ip == get_ip()) $admin_del = "<input class='btn btn-danger' type='submit' value='delete' onclick='del(".$file_time.");' style='padding:0 0 ;' />";
      else $admin_del = "<input class='btn btn-danger' type='submit' value='无权限' style='padding:0 0 ;' />";
      if(!file_exists('json/'.$file_time.'.json')) $url = '下载中......';
      echo '<tr>
                    <td style="text-align:center;width:60px;">'.$index.'</td>
                    <td><a target="_blank" title="点击下载" href='.$longPath.'>'.$fileName."</a></td>
                    <td>".$size."MB</td>
                    <td>".$url."</td>
                    <td>".$downtime_fomat."</td>
                    <td>";
      secondToDate($effecttime,$index);
      echo "</td>
                    <td>".$admin_del."</td>
                    <td>".$ip."</td>
                    </tr>
                    ";
    }else{
      //超过10文件则删除
      unlink($longPath);
      if(is_dir($filePath)) rmdir($filePath);
    }
  }
  echo "</table>";
  echo "<p id='xnumber'>".$index."</p>";
}

function safePost($str)
{
  $val = !empty($_POST["$str"]) ? $_POST[$str]:null;
  return trim($val);
}

$url = safePost("url");
$name = safePost("filename");
$allow_type=array("mp3","mp4","wmv","mov","apk","deb","iso","exe","pdf","xls","xlsx","doc","docx","ppt","pptx","txt","zip","rar","7z","jpeg","jpg","JPEG","png","gif","gz","js","css","pac","sqlite","sql","msi");
//允许的文件类型
$torrent = explode(".",$url);
$file_end = end($torrent);
$file_end = strtolower($file_end);
$file_time = time();
if(in_array($file_end,$allow_type))
{
  //shell_exec("wget -nc --restrict-file-names=nocontrol -P ./download/".$file_time."/ ".escapeshellarg($url)); 
  downFile($url,'download/'.$file_time.'/', get_filename($url));
  // -b 后台下载
  // -nc 文件已经存在时不覆盖
  // --restrict-file-names 解决中文地址导致文件名乱码的问题
  // -P 保存路径
  //echo "<script type='text/javascript'>
  //reloadPage();
  //</script>"; 
  $tarry['path'] = "download/".$file_time."/".get_filename($url);
  $tarry['url'] = $url;
  $tarry['ip'] = ip();
  $tarry['time'] = $file_time;
  $tjson = json_encode($tarry);
  if(!file_exists('json/'.$file_time.'.json')){
    savejson('json/'.$file_time.'.json',$tjson);
  }
  header("Location: /");
}
else if($file_end != null)
{
  echo '<div class="alert alert-danger"> 类型: '. $file_end . '<br />';
  echo"<button class='close' data-dismiss='alert'>x</button>";
  echo "这个文件类型不允许！";
  echo "允许的文件有：</br>";
  foreach($allow_type as $xxx)
    echo $xxx . "、 ";
  echo "</br> 如有需要，请与管理员联系！</div>";
}

function savejson($url,$str){
  fopen($url,'w');
  file_put_contents($url,$str);
}

function ip()
{
  $tclient_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  if(ii_isnull($tclient_ip))
  {
    $tclient_ip = $_SERVER['HTTP_CLIENT_IP'];
    if(ii_isnull($tclient_ip)) $tclient_ip = $_SERVER['REMOTE_ADDR'];
  }
  $tclient_ip = ii_get_safecode($tclient_ip);
  return $tclient_ip;
}

function ii_isnull($strers)
{
  if (trim($strers) == '') return true;
  else return false;
}

function ii_get_safecode($strers)
{
  if (!ii_isnull($strers))
  {
    $tstrers = $strers;
    $tstrers = str_replace('\'', '', $tstrers);
    $tstrers = str_replace(';', '', $tstrers);
    $tstrers = str_replace('--', '', $tstrers);
    return $tstrers;
  }
}

function get_ip(){
  $tclient_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  if(empty($tclient_ip))
  {
    $tclient_ip = $_SERVER['HTTP_CLIENT_IP'];
    if(empty($tclient_ip)) $tclient_ip = $_SERVER['REMOTE_ADDR'];
  }
  return $tclient_ip;
}

function get_filename($filepath){
  $fr=explode("/",$filepath);
  $count=count($fr)-1;
  return $fr[$count];
}

function downFile($url,$path,$fileName){
  $url = trim($url);
  if (trim($url) == '') {
    return false;
  }
  //创建保存目录
  if (!file_exists($path) && !mkdir($path, 0777, true)) {
    return false;
  }
  $curl = curl_init(); // 启动一个CURL会话
  //以下三行代码解决https图片访问受限问题
  $dir = pathinfo($url);//以数组的形式返回图片路径的信息
  $host = $dir['dirname'];//图片路径
  $ref = $host.'/';
  curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址    
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
  curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
  if($ref){
    curl_setopt($curl, CURLOPT_REFERER, $ref);//带来的Referer
  }else{
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
  }
  curl_setopt($curl, CURLOPT_HTTPGET, 1); // 发送一个常规的Post请求
  curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
  curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
  $tmpInfo = curl_exec($curl); // 执行操作
  if (curl_errno($curl)) {
    echo 'Errno'.curl_error($curl);
  }
  $data['head']=curl_getinfo($curl);
  curl_close($curl); // 关闭CURL会话
  $data['data']=$tmpInfo;
  if($data['head']['http_code'] == '200'){
    $tp = fopen($path.$fileName, 'w');
    fwrite($tp, $data['data']);//图片二进制数据写入图片文件
    fclose($tp);
  }
}

?>
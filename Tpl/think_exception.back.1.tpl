<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	
	<meta name="server" content="<?php echo $_SERVER["SERVER_ADDR"] ?>">
	
	<title>小助出错了...</title>
	
	<link rel="shortcut icon" href="__IMG__/favicon.ico">

	<script src="http://libs.useso.com/js/jquery/1.11.1/jquery.min.js"></script>
	<script src="http://libs.useso.com/js/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	<script src="http://libs.useso.com/js/json3/3.3.2/json3.min.js" ></script>
	

	<link rel="stylesheet" href="http://libs.useso.com/js/bootstrap/3.2.0/css/bootstrap.min.css" />
	<link rel="stylesheet" href="/Public/css/mxz-developer.css" />
	<link rel="stylesheet" href="/Public/css/font-awesome.min.css" />

</head>
<body>
	<div class="jumbotron">
		<h1><i class="icon-info-sign"></i>粗线错误啦~</h1>
		<p>错误信息已经提交到萌小助进行处理...</p>
	</div>
	<div class="container">
		<div class="alert alert-info">
			<p><?php echo strip_tags($e['message']);?></p>
		</div>
		
		<?php if(isset($e['file'])) {?>
			<div class="alert alert-info">
				<p style="word-wrap: break-word;">FILE: <?php echo $e['file'] ;?> &#12288;LINE: <?php echo $e['line'];?></p>
				<p><?php echo nl2br($e['trace']);?></p>
			</div>
		<?php }?>
		<div>
			<p class="strong">请<a href="http://mouge.udesk.cn/hc">点击这里</a>提交工单，问题解决后小助会联系你哟～</p>
		</div>
	</div>	
	<br>
	<div style=" border-top:1px solid #e5e5e5">
	  <div class="container" style="margin-top: 20px;">
	  <p class="text-center">University Service Engine 闽ICP备14008008号</p>
	  	<p class="text-right">交流QQ群:221540847</p>
	    <p class="text-right">厦门助梦网络科技有限公司</p>
	  </div>
	</div>
</body>
<script>

</script>
</html>
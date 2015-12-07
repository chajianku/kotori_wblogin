<?php
isset($_GET['action']) ? $action = $_GET['action'] : $action = 'none';
switch($action){
	case 'none':
	default:
	header("Location:https://api.imim.pw/tcswblogin?source=".$i['opt']['system_url']);
	break;

	case 'callback':
	isset($_GET['username']) ? $username = $_GET['username'] : die("授权后返回的参数无效:) 请重试。");
	isset($_GET['pwd']) ? $password = $_GET['pwd'] : die("授权后返回的参数无效:) 请重试。");
	/*先判断用户是否存在*/
	$user_exist = $m->query("SELECT COUNT(*) AS exist FROM `".DB_NAME."`.`".DB_PREFIX."users` WHERE `name` = '{$username}' ");
	$uearray = $m->fetch_array($user_exist);
	//print_r($uearray);
	/*如果不存在，要求用户留下新的邮箱*/
	if($uearray['exist'] == '0'){
		echo "<!DOCTYPE html>\n<html>\n<head>\n";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n";
		echo "<meta http-equiv=\"charset\" content=\"utf-8\">\n";
		echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
		echo "<title>\n".$title."</title>\n";
		echo "<meta name=\"generator\" content=\"Tieba-Cloud-Sign Ver.'.SYSTEM_VER.'\" />\n";
		echo "<link href=\"favicon.ico\" rel=\"shortcut icon\"/>\n";
		echo "<meta name=\"author\" content=\"God.Kenvix\'s Blog (http://zhizhe8.net) and StusGame GROUP (http://www.stus8.com)\" />\n";
		echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\" />\n";
		echo "<script src=\"source/js/jquery.min.js\"></script>\n";
		echo "<link rel=\"stylesheet\" href=\"source/css/bootstrap.min.css\">\n";
		echo "<script src=\"source/js/bootstrap.min.js\"></script>\n";
		echo "<style type=\"text/css\">body { font-family:\"微软雅黑\",\"Microsoft YaHei\";background: #eee; }</style>\n";
		echo "<script type=\"text/javascript\" src=\"source/js/js.js\"></script>\n";
		echo "<link rel=\"stylesheet\" href=\"source/css/ui.css\">\n";
		echo "<link rel=\"stylesheet\" href=\"source/css/my.css\">\n";
		echo "<script type=\"text/javascript\" src=\"source/js/my.js\"></script>\n";
		echo "<meta name=\"keywords\" content=\"'.option::get('system_keywords').'\" />\n";
		echo "<meta name=\"description\" content=\"'.option::get('system_description').'\" />\n";
		echo "</head><body>\n";
		?>
		<div class="container">
			<br/>
			<div class="panel panel-default">
				<div class="panel-heading"><?php echo $_GET['screenname'];?></div>
				<div class="panel-body">
					<form action="index.php?pub_plugin=kotori_wblogin&action=regpost" method="post">
						<h3>请完成最后一步（补充您的邮箱），然后就可以使用微博登录本站了。</h3>
						<br/>
						<div class="input-group">
							<span class="input-group-addon" id="basic-addon1">邮箱</span>
							<input name="mail" type="email" class="form-control" placeholder="格式如：someone@example.com，由于Kotori没有接口权限所以只能酱紫啦……" aria-describedby="basic-addon1">
							<input name="name" type="hidden" value="<?php echo $username;?>"></input>
							<input name="pwd" type="hidden" value="<?php echo $password;?>"></input>
						</div>
						<br/>
						<?php 
						$yr_reg = option::get('yr_reg');
						if (!empty($yr_reg)): ?>
						<div class="input-group">
							<span class="input-group-addon">邀请码</span>
							<input type="text" class="form-control" name="yr" id="yr" required>
						</div>
					<?php endif;?>
					<br/>
					<button class="btn btn-success" type="submit">还差最后一步，完成登录</button>
				</form>
			</div>
		</div>
	</div>
	<?php
	echo "</body>\n";
	echo "</html>\n";
}
/*否则，进行登录操作*/
else{
	$username = $_GET['username'];
	$uinfo = $m->once_fetch_array("SELECT * FROM `".DB_NAME."`.`".DB_PREFIX."users` WHERE `name` = '{$username}' ");
	if($_GET['pwd'] == $uinfo['pw']){
		$pwd = $_GET['pwd'];
		setcookie("uid",$uinfo['id'], time() + 999999);
		setcookie("pwd",substr(sha1(EncodePwd($pwd)) , 4 , 32), time() + 999999);
		header("Location:index.php");
	}
	else{
		header("Location:index.php?mod=login&error_msg=微博登录授权失败，密码错误 :) ");
	}
}
break;

case 'regpost':
$yr_reg = option::get('yr_reg');
if (!empty($yr_reg)){
	isset($_POST['yr']) ? $invite = $_POST['yr'] : die("缺少需要的数据哟 invite :)");
	if($invite != $yr_reg){
		header("Location:index.php?mod=login&error_msg=邀请码错误！");
	}
}
isset($_POST['mail']) ? $email = addslashes($_POST['mail']) : die("缺少需要的数据哟 mail :)");
isset($_POST['pwd']) ? $pwd = $_POST['pwd'] : die("缺少需要的数据哟 pwd :)");
isset($_POST['name']) ? $username = addslashes($_POST['name']) : die("缺少需要的数据哟 uname :)");

/*开始注册判定*/
if (option::get('enable_reg') != '1') {
	msg('注册失败：该站点已关闭注册');
}
$x=$m->once_fetch_array("SELECT COUNT(*) AS total FROM `".DB_NAME."`.`".DB_PREFIX."users` WHERE `name` = '{$username}' OR `email` = '{$email}' LIMIT 1");
$y=$m->once_fetch_array("SELECT COUNT(*) AS total FROM `".DB_NAME."`.`".DB_PREFIX."users`");
if ($x['total'] > 0) {
	msg('注册失败：用户名或邮箱已经被注册');
}
$m->query('INSERT INTO `'.DB_NAME.'`.`'.DB_PREFIX.'users` (`id`, `name`, `pw`, `email`, `role`, `t`) VALUES (NULL, \''.$username.'\', \''.$pwd.'\', \''.$email.'\', \'user\', \''.getfreetable().'\');');
$id = $m->once_fetch_array("SELECT * FROM `".DB_NAME."`.`".DB_PREFIX."users` WHERE `name` = '{$username}' ");

setcookie("uid",$id['id'], time() + 999999);
setcookie("pwd",substr(sha1(EncodePwd($pwd)) , 4 , 32), time() + 999999);
header("Location:index.php");

break;
}
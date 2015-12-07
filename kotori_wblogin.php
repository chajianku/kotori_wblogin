<?php
/*
Plugin Name: 微博登录插件
Version: 1.0
Plugin URL: https://www.imim.pw/
Description: 支持通过微博注册/登录云签
Author: 吟梦
Author Email: i@imim.pw
Author URL: https://www.imim.pw/
For: 4.0
*/

if (!defined('SYSTEM_ROOT')) die('Insufficient Permissions :)');

function kotori_weibo_login_menu(){
	?>
	<li class="<?php checkIfActive('kotori_wblogin') ?>" ><a href="index.php?pub_plugin=kotori_wblogin"><span class="glyphicon glyphicon-eye-open"></span> 微博账号登录</a></li>
	<?php
}
addAction('navi_10','kotori_weibo_login_menu');
addAction('navi_11','kotori_weibo_login_menu');
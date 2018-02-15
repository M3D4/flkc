<?php
if ( !defined('__TYPECHO_ROOT_DIR__') ) exit;

class flkc_Action implements Widget_Interface_Do {

	public function __construct($request, $response, $params = NULL) {
		// 载入模板函数文件
		if ( !function_exists('cache') ) require Helper::options()->themeFile('flkc', 'functions.php');
		// 载入函数文件
		if ( !function_exists('json') ) require __DIR__ . '/functions.php';
	}

	public function execute() {}

	public function action() {}

	// ajax操作
	public function ajax() {
		// 获取请求对象
		$request = Typecho_Request::getInstance();
		// 是否ajax
		if ( $request->isajax() ) {
			// 获取act
			$act = $request->filter('xss')->get('act');
			// 验证操作
			if ( !isset($act) || empty($act) ) json(['error' => 1, 'msg' => '未获取到正确的操作！']);
			// 操作类型判断
			switch ( $act ) {
				case 'notice': // 公告
					$id = $request->filter('int')->get('id'); // 获取编号
					// 验证编号
					if ( 0 === $id ) json(['error' => 1, 'msg' => '动态编号获取错误！']);
					// 获取文章
					$archive = get_notice($id);
					// 验证获取结果
					if ( !$archive ) json(['error' => 1, 'msg' => '动态获取失败！']);
					// 设置标题
					$name = $archive['title'];
					// 设置内容
					$content = '<br><br><div style="text-align:center;font-size:16px;"><p style="color:#d75544;font-weight:bold;">' . $name . '</p><br><p>' . date('Y-m-d H:i:s', $archive['created']) . '</p><br><p>' . nl2br($archive['text']) . '</p>';
					json(['error' => 0, 'msg' => ['name' => $name, 'content' => $content]]);
				case 'add': // 自助收录
					// 站点名称
					$sitename = $request->filter('xss')->get('sitename');
					// 站点地址
					$siteurl = $request->filter('url')->get('siteurl');
					// 分类编号
					$cate = $request->filter('int')->get('category');
					// 验证请求
					if ( empty($sitename) || empty($siteurl) || 0 === $cate ) json(['error' => 1, 'msg' => '数据提交不完整，请重新提交！']);
					// 解析url
					$uri = parse_url($siteurl);
					// 验证地址
					if ( !isset($uri['host']) || !isset($uri['scheme']) ) json(['error' => 1, 'msg' => '抱歉，您所提交的网址不符合规范，请不要忘记添加 http:// 或 https://']);
					$host = $uri['host']; // 获取host
					// 验证是否添加过
					if ( !verify_host($host) ) json(['error' => 1, 'msg' => '您所提交的网站已经收录在我们的数据库中，请不要重复添加！']);
					$flag = verify_link($siteurl); // 验证链接
					// 根据验证结果进行不同的确认
					if ( 0 === $flag ) {
						if ( add_link($sitename, $siteurl, $cate, $host, '', 'publish') ) {
							// 清除缓存
							cache('links_mid_' . $cate, null);
							json(['error' => 0, 'msg' => '贵站链接已添加成功，感谢您的支持！']);
						}
						json(['error' => 1, 'msg' => '系统出现未知错误，链接无法添加，请联系管理员！']);
					} else if ( 1 === $flag ) json(['error' => 1, 'msg' => '贵站还未添加本站友情链接，请先添加后再申请！']);
					else if ( 2 === $flag ) json(['error' => 1, 'msg' => '暂时无法访问贵站，请检查贵站访问是否有问题或地址是否正确？若一直无法访问请联系站长！']);
					else json(['error' => 1, 'msg' => '出现未知错误，请确认您所填写的网站地址正确并可正常访问！']);
				default:
					json(['error' => 1, 'msg' => '未知的操作！']);
			}
		}
		exit('请勿非法请求！');
	}

	// 检测链接
	public function check() {
		// 初始化用户对象
		$user = Typecho_Widget::widget('Widget_User');
		// 验证用户登录
		if ( !$user->hasLogin() ) json(['error' => 1, 'msg' => '您无权进行此操作！']);
		// 获取请求对象
		$request = Typecho_Request::getInstance();
		// 是否ajax请求
		if ( $request->isajax() ) {
			// 获取编号
			$cid = $request->filter('int')->get('cid');
			// 获取url
			$url = $request->filter('url')->get('url');
			if ( 0 === $cid ) json(['error' => 1, 'msg' => '链接编号获取错误！']);
			// 检查网址
			if ( empty($url) ) json(['error' => 1, 'msg' => '链接地址获取错误！']);
			// 反编译网址
			$url = urldecode($url);
			// 检测网址
			$flag = verify_link($url);
			// 设置链接状态
			set_state($cid, $flag);
			// 根据验证结果进行不同的确认
			if ( 0 === $flag ) json(['error' => 0, 'msg' => '<font style="color:#1E90FF">链接正常</font>']);
			else if ( 1 === $flag ) json(['error' => 0, 'msg' => '<font style="color:#FF0000">链接不存在</font>']);
			else if ( 2 === $flag ) json(['error' => 0, 'msg' => '<font style="color:#EE7600">无法访问</font>']);
			json(['error' => 1, 'msg' => '<font style="color:#FF00FF">未知错误</font>']);
		}
		exit('请勿恶意提交！');
	}

	// 删除链接
	public function delete() {
		// 初始化用户对象
		$user = Typecho_Widget::widget('Widget_User');
		// 验证用户登录
		if ( !$user->hasLogin() ) json(['error' => 1, 'msg' => '您无权进行此操作！']);
		// 获取请求对象
		$request = Typecho_Request::getInstance();
		// 是否ajax请求
		if ( $request->isajax() ) {
			// 获取cid
			$cid = $request->filter('int')->get('cid');
			// 检查编号
			if ( 0 === $cid ) json(['error' => 1, 'msg' => '链接编号获取错误！']);
			// 获取db对象
			$db = Typecho_Db::get();
			// 获取文章
			$archive = $db->fetchRow($db->select()->from('table.contents')->where('cid = ?', $cid));
			// 获取分类编号
			$mid = $db->fetchObject($db->select('mid')->from('table.relationships')->where('cid = ?', $cid))->mid;
			// 删除文章
			if ( $db->query($db->delete('table.contents')->where('cid = ?', $cid)) ) {
				// 分类自减
				$db->query($db->update('table.metas')->expression('count', 'count - 1')->where('mid = ?', $mid));
				// 删除关系
				$db->query($db->delete('table.relationships')->where('cid = ?', $cid));
				// 删除自定义字段
				$db->query($db->delete('table.fields')->where('cid = ?', $cid));
				// 清空缓存
				cache(null);
				json(['error' => 0, 'msg' => '链接已成功删除！']);
			} else json(['error' => 1, 'msg' => '链接删除失败，请在数据库中自行删除！']);
		}
		exit('请勿恶意提交！');
	}

	// js请求
	public function js() {
?>
$(function(){setInterval(function(){doscroll()},2000);$('.list .item').hover(function(a){$(this).addClass("bg")},function(a){$('.list .item').removeClass('bg')});$('.sidebar ul li a').on('click',function(a){a.preventDefault();$('html,body').animate({scrollTop:$(this.hash).offset().top},200)});$('#submit').click(function(){var b=$('#sitename').val();var c=$('#siteurl').val();var e=$('#category option:selected').val();if(b==''||c==''||e==''||e==0)return layer.msg('请完成所有内容的填写！');var f=new RegExp('^((https|http|ftp|rtsp|mms)?://)'+'?(([0-9a-z_!~*\'().&=+$%-]+: )?[0-9a-z_!~*\'().&=+$%-]+@)?'+'(([0-9]{1,3}.){3}[0-9]{1,3}'+'|'+'([0-9a-z_!~*\'()-]+.)*'+'([0-9a-z][0-9a-z-]{0,61})?[0-9a-z].'+'[a-z]{2,6})'+'(:[0-9]{1,4})?'+'((/?)|'+'(/[0-9a-z_!~*\'().;?:@&=+$,%#-]+)+/?)$');isok=f.test(c);if(!isok)return layer.msg('网站地址填写错误，请重新填写！');var g=layer.load(1,{shade:[0.1,'#FFF']});$.ajax({url:'/ajax.do?act=add',type:'POST',dataType:'json',data:{sitename:b,siteurl:c,category:e},success:function(a){layer.close(g);if(a.error==0)return layer.msg('网站提交成功，本站设置有缓存，若未显示，请过会儿再来！');return layer.msg(a.msg)},error:function(a){layer.close(g);layer.msg('数据提交失败，请检查您的网络！')}})})});function doscroll(){var a=$('.news');var b=a.find('li:first');var c=b.height();b.animate({height:0},1000,function(){b.css('height',c).appendTo(a)})}function show(){layer.open({type:1,title:['<?php Helper::options()->title();?> - 精品福利导航第一站'],area:['980px','300px'],content:'<div style="height:140px; text-align:center; background:#FFF;"><a style="display:inline-block; line-height:40px; font-size:40px; padding:25px; color:#F00; font-weight:bolder;" href="<?php Helper::options()->link_url();?>" target="_blank"><?php Helper::options()->link_url();?></a><br/><span>（ 本站永久域名,同时请记住下面的发布页地址）</span><br><a style="display:inline-block; line-height:40px; font-size:40px; padding:25px; color:#F00; font-weight:bolder;" href="<?php Helper::options()->link_pub();?>" target="_blank"><?php Helper::options()->link_pub();?></a></div>'})}function notice(b){var c=layer.load(1,{shade:[0.1,'#FFF']});$.ajax({url:'/ajax.do?act=notice',type:'GET',dataType:'json',data:{id:b},success:function(a){layer.close(c);layer.open({type:1,title:[a.msg.name],area:['980px','600px'],content:a.msg.content})},error:function(a){layer.close(c);layer.msg('公告获取失败，请检查您的网络！')}})}
<?php
	}
}
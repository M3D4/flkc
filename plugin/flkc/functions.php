<?php
if ( !defined('__TYPECHO_ROOT_DIR__') ) exit;

// 输出json
function json($data) {
	// 初始化Response
    $response = Typecho_Response::getInstance();
    // 输出json
    $response->throwJson($data);
}

// 获取公告
function get_notice($id) {
	// 获取db对象
	$db = Typecho_Db::get();
	// 获取文章信息
	$post = $db->fetchRow($db->select()->from('table.contents')->where('cid = ?', $id));
	// 验证获取结果
	if ( !isset($post['text']) || empty($post) ) return false;
	// 获取文章对象
	$archive = Typecho_Widget::widget('Widget_Abstract_Contents')->filter($post);
	return $archive;
}

// 验证host
function verify_host($host) {
	// 为空直接返回
	if ( empty($host) ) return true;
	// 获取db对象
	$db = Typecho_Db::get();
	// 获取统计
	$h = $db->fetchObject($db->select(['COUNT(cid)' => 'num'])->from('table.fields')->where('str_value = ?', $host))->num;
	// 存在返回失败
	if ( 0 < $h ) return false;

	return true;
}

// 验证链接
function verify_link($uri) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 不直接输出
	curl_setopt($ch, CURLOPT_HEADER, false); // 不返回header信息
	curl_setopt($ch, CURLOPT_NOBODY, 0); // 返回Body
	curl_setopt($ch, CURLOPT_URL, $uri);
	curl_setopt($ch, CURLOPT_TIMEOUT, 3); // 设置超时时间
	curl_setopt($ch, CURLOPT_NOSIGNAL, true);
	curl_setopt($ch, CURLOPT_TIMEOUT_MS, 3000);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.85 Safari/537.36');
	if ( 0 === strpos($uri, 'https') ) {
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	}
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 执行301跳转
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
	$response = curl_exec($ch); // 获取结果
	$errno = curl_errno($ch); // 读取错误编号
	$error = curl_error($ch); // 读取错误信息
	curl_close($ch); // 关闭curl
	if ( 0 === $errno ) // 是否成功
		if ( strstr($response, Helper::options()->link_url) )
			return 0;
		else return 1;
	else return 2;

	return 2;
}

// 添加链接
function add_link($name, $uri, $mid, $host = '', $color = '', $status = 'private', $trade = '', $modified = 0) {
	// 获取db对象
	$db = Typecho_Db::get();
	// 临时变量
	$cid = 0;
	if ( 0 === $modified ) $modified = time();
	// 构造文章结构
	$archive = [
		'title' => $name,
		'created' => time(),
		'modified' => $modified,
		'text' => '<!--markdown-->',
		'authorId' => '1',
		'type' => 'post',
		'status' => $status,
		'allowComment' => 0,
		'allowPing' => 0,
		'allowFeed' => 0
	];
	// 订单号
	if ( !empty($trade) ) $archive['slug'] = $trade;
	// 插入文章
	$cid = $db->query($db->insert('table.contents')->rows($archive));
	// 如果插入成功
	if ( $cid ) {
		if ( empty($trade) )
			// 更新文章slug
			$db->query($db->update('table.contents')->rows(['slug' => $cid])->where('cid = ?', $cid));
		// 插入url
		$db->query($db->insert('table.fields')->rows(['cid' => $cid, 'name' => 'url', 'type' => 'str', 'str_value' => $uri]));
		// 插入颜色
		$db->query($db->insert('table.fields')->rows(['cid' => $cid, 'name' => 'color', 'type' => 'str', 'str_value' => $color]));
		// 插入host
		$db->query($db->insert('table.fields')->rows(['cid' => $cid, 'name' => 'host', 'type' => 'str', 'str_value' => $host]));
		// 插入关系
		$db->query($db->insert('table.relationships')->rows(['cid' => $cid, 'mid' => $mid]));
		// 增加分类文章总数
		$db->query($db->update('table.metas')->expression('count', 'count + 1')->where('mid = ?', $mid));
	}
	// 判断是否成功
	if ( $cid ) return true;
	// 删除文章
	$db->query($db->delete('table.contents')->where('cid = ?', $cid));
	// 删除自定义字段
	$db->query($db->delete('table.fields')->where('cid = ?', $cid));
	// 删除关系
	$db->query($db->delete('table.relationships')->where('cid = ?', $cid));
	// 自减文章数量
	$db->query($db->update('table.metas')->expression('count', 'count - 1')->where('mid = ?', $mid));
	return false;
}

// 设置链接状态
function set_state($cid, $state) {
	// 获取db对象
	$db = Typecho_Db::get();
	// 更改状态
	$db->query($db->update('table.contents')->rows(['state' => $state + 1])->where('cid = ?', $cid));
}

// 获取链接状态显示
function get_state($state) {
	switch ( $state ) {
		case 1: return '<font style="color:#1E90FF">链接正常</font>';
		case 2: return '<font style="color:#FF0000">链接不存在</font>';
		case 3: return '<font style="color:#EE7600">无法访问</font>';
		default: return '暂未检查';
	}
}
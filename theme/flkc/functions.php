<?php
if ( !defined('__TYPECHO_ROOT_DIR__') ) exit;

// 缓存管理
function cache($name, $value = '', $options = null) {
	// 载入缓存类
	if ( !class_exists('Cache') ) require __DIR__ . '/cache.php';
	// 缓存初始化
	$cache = new Cache;
	if ( is_null($name) ) return $cache->clear(); // 清空缓存
	else if ( '' === $value ) return 0 === strpos($name, '?') ? $cache->has(substr($name, 1)) : $cache->get($name); // 获取缓存
	else if ( is_null($value) ) return $cache->rm($name); // 删除缓存
	else if ( 0 === strpos($name, '?') && '' !== $value ) {
		$expire = is_numeric($options) ? $options : null;
		return $cache->remember(substr($name, 1), $value, $expire);
	} else {
		$expire = is_numeric($options) ? $options : null;
		return $cache->set($name, $value, $expire);
	}
}

// 获取导航分类列表
function get_categories() {
	// 获取缓存
	$res = cache('categories');
	// 无缓存
	if ( !$res ) {
		// 获取db对象
		$db = Typecho_Db::get();
		// 获取导航分类列表
		$res = $db->fetchAll($db->select()->from('table.metas')->where('parent = ?', Helper::options()->parent));
		// 设置缓存
		cache('categories', $res);
	}

	return $res;
}

// 获取链接列表
function get_links($mid) {
	// 验证mid
	if ( !is_numeric($mid) || !$mid ) return null;
	// 获取缓存
	$res = cache('links_mid_' . $mid);
	// 无缓存
	if ( !$res ) {
		// 获取db对象
		$db = Typecho_Db::get();
		// 获取链接列表
		$res = $db->fetchAll($db->select()->from('table.contents')->join('table.relationships', 'table.contents.cid = table.relationships.cid', Typecho_Db::LEFT_JOIN)->where('table.relationships.mid = ?', $mid)->order('table.contents.referers', Typecho_Db::SORT_DESC));
		// 设置缓存
		cache('links_mid_' . $mid, $res);
	}

	return $res;
}

// 获取自定义字段
function get_fields($cid) {
	// 验证cid
	if ( !is_numeric($cid) || !$cid ) return null;
	// 获取缓存
	$fields = cache('fields_cid_' . $cid);
	// 无缓存
	if ( !$fields ) {
		// 获取db对象
		$db = Typecho_Db::get();
		// 获取自定义字段
		$tmp = $db->fetchAll($db->select()->from('table.fields')->where('cid = ?', $cid));
		// 临时数组
		$fields = [];
		// 循环自定义字段
		foreach ( $tmp as $f )
			$fields[$f['name']] = $f['str_value']; // 设置数组
		// 设置缓存
		cache('fields_cid_' . $cid);
	}

	return $fields;
}

// 设置referer
function set_referer($host) {
	// 获取db对象
	$db = Typecho_Db::get();
	// 获取cid
	$cid = $db->fetchObject($db->select('cid')->from('table.fields')->where('str_value = ?', $host))->cid;
	// 验证cid
	if ( !$cid ) return false;
	// referer自增
	return $db->query($db->update('table.contents')->expression('referers', 'referers + 1')->where('cid = ?', $cid));
}

// 修改来源数
function referer() {
	// 获取请求对象
	$request = Typecho_Request::getInstance();
	// 验证来源
	if ( empty($request->getReferer()) ) return false;
	// 获取完整来路URL
    $url = $request->getReferer();
    // 解析域名
    $uri = parse_url($url);
    // 验证host
    if ( !isset($uri['host']) ) return false;
    // 取host
    $host = $uri['host'];
    // 获取cookie
    $cookie = Typecho_Cookie::get('flkc_referer_' . $host, 'no');
    // 如果不存在
    if ( 'no' === $cookie )
    		set_referer($host); // 访问自增
    	else {
    		// 获取当前时间
    		$now = time();
    		// 设置最后访问时间
    		$last = $cookie + (60 * 60 * 24);
    		// 如果没有超过一天
    		if ( $now <= $last ) return false;
    		// 访问自增
    		set_referer($host);
    	}

    	// 设置cookie
    	Typecho_Cookie::set('flkc_referer_' . $host, time());
}

// 后台配置面板
function themeConfig($form) {
	$shortcut = new Typecho_Widget_Helper_Form_Element_Text('shortcut', NULL, NULL, _t('站点图片地址'), _t('在这里填入一个图片URL地址，以便在浏览器标题栏显示图标'));
	$form->addInput($shortcut);
	$intro = new Typecho_Widget_Helper_Form_Element_Text('intro', NULL, NULL, _t('站点描述文本', _t('请输入一段文字，将在LOGO旁显示')));
	$form->addInput($intro);
	$xuanyan = new Typecho_Widget_Helper_Form_Element_Text('xuanyan', NULL, NULL, _t('站点宣言', _t('将展示为站点宣言')));
	$form->addInput($xuanyan);
	$topads = new Typecho_Widget_Helper_Form_Element_Text('topads', NULL, NULL, _t('顶部广告分类编号'), _t('请建立一个分类作为顶部广告分类，并填入编号'));
	$form->addInput($topads);
	$mingzhanads = new Typecho_Widget_Helper_Form_Element_Text('mingzhanads', NULL, NULL, _t('名站广告分类编号'), _t('请建立一个分类作为名站广告分类，并填入编号'));
	$form->addInput($mingzhanads);
	$parent = new Typecho_Widget_Helper_Form_Element_Text('parent', NULL, NULL, _t('导航父分类编号列表'), _t('请建立一个分类作为导航链接父分类，在此分类下再创建需要作为导航分类的所有分类'));
	$form->addInput($parent);
	$news = new Typecho_Widget_Helper_Form_Element_Text('news', NULL, NULL, _t('动态信息分类编号'), _t('请建立一个分类作为动态信息分类，并填入编号'));
	$form->addInput($news);
	$yj_url = new Typecho_Widget_Helper_Form_Element_Text('yj_url', NULL, NULL, _t('永久地址'), _t('请填入网站永久地址'));
	$form->addInput($yj_url);
	$link_name = new Typecho_Widget_Helper_Form_Element_Text('link_name', NULL, NULL, _t('链接名称'), _t('要求对方网站添加的本站链接名称'));
	$form->addInput($link_name);
	$link_url = new Typecho_Widget_Helper_Form_Element_Text('link_url', NULL, NULL, _t('链接地址'), _t('要求对方网站添加的本站链接地址'));
	$form->addInput($link_url);
	$link_pub = new Typecho_Widget_Helper_Form_Element_Text('link_pub', NULL, NULL, _t('地址发布页地址', _t('请填入地址发布页地址')));
	$form->addInput($link_pub);
	$email = new Typecho_Widget_Helper_Form_Element_Text('email', NULL, NULL, _t('联系邮箱', _t('填入联系邮箱，将显示为广告、收录联系邮箱')));
	$form->addInput($email);
	$isauto = new Typecho_Widget_Helper_Form_Element_Radio('isauto', ['yes' => _t('是'), 'no' => _t('不是')], 'yes', _t('是否添加自助收录功能'), _t('将在导航栏中添加自助收录功能，若不添加，则显示为联系邮箱！'));
	$form->addInput($isauto);
}

// 文章页自定义字段
function themeFields($layout) {
	$url = new Typecho_Widget_Helper_Form_Element_Text('url', NULL, NULL, _t('链接地址'), _t('在这里填入网站地址'));
	$layout->addItem($url);
	$color = new Typecho_Widget_Helper_Form_Element_Text('color', NULL, NULL, _t('链接颜色'), _t('在此处填入链接展示颜色'));
	$layout->addItem($color);
	$host = new Typecho_Widget_Helper_Form_Element_Text('host', NULL, NULL, _t('主机名称'), _t('填入主机名称，避免重复添加！'));
	$layout->addItem($host);
}
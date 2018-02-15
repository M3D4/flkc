<?php
/**
 * 福利开车网 Typecho 导航站主题配套插件
 * 
 * @package flkc
 * @author 福利开车网
 * @version 0.1
 * @link https://www.fulikaiche.com
 */

if ( !defined('__TYPECHO_ROOT_DIR__') ) exit;

class flkc_Plugin implements Typecho_Plugin_Interface {
	// 激活插件
	public static function activate() {
		// ajax请求路由
		Helper::addRoute('flkc_ajax', '/ajax.do', 'flkc_Action', 'ajax');
		// js路由
		Helper::addRoute('flkc_js', '/js.do', 'flkc_Action', 'js');
		// 删除路由
		Helper::addRoute('flkc_delete', '/flkc/delete.do', 'flkc_Action', 'delete');
		// 链接检测路由
		Helper::addRoute('flkc_check', '/flkc/check.do', 'flkc_Action', 'check');
		// 添加面板
		Helper::addPanel(1, basename(dirname(__FILE__)) . '/check.php', '链接检查', '导航链接检查', 'administrator');
		// 挂载保存接口
		Typecho_Plugin::factory('Widget_Contents_Post_Edit')->finishPublish = [__CLASS__, 'render'];
		// 获取db对象
		$db = Typecho_Db::get();
		// 获取表前缀
		$prefix = $db->getPrefix();
		// 获取资源
		$res = $db->fetchRow($db->select()->from('table.contents'));
		// 添加referers字段
		if ( !array_key_exists('referers', $res) )
			$db->query('ALTER TABLE `' . $prefix . 'contents` ADD `referers` BIGINT(20) DEFAULT 0;');
		// 添加state字段
		if ( !array_key_exists('state', $res))
			$db->query('ALTER TABLE `' . $prefix . 'contents` ADD `state` TINYINT(1) DEFAULT 0;');
	}

	// 禁用插件
	public static function deactivate() {
		Helper::removeRoute('flkc_ajax');
		Helper::removeRoute('flkc_js');
		Helper::removeRoute('flkc_delete');
		Helper::removeRoute('flkc_check');
		Helper::removePanel(1, basename(dirname(__FILE__)) . '/check.php');
	}

	public static function config(Typecho_Widget_Helper_Form $form) {}

	public static function personalConfig(Typecho_Widget_Helper_Form $form) {}

	// 写入host
	public static function render($contents, $edit) {
		if ( 'publish' !== $contents['visibility'] ) return;
		// 如果没设置host
		if ( empty($edit->fields->host) ) {
			// 获取子分类列表
			$children = Typecho_Widget::widget('Widget_Metas_Category_List')->getAllChildren(Helper::options()->parent);
			// 获取db对象
			$db = Typecho_Db::get();
			// 临时变量
			$flag = false;
			if ( is_array($edit->category) ) {
				foreach ( $edit->category as $slug ) {
					// 获取分类名称
					$cat = $db->fetchAll($db->select('mid')->from('table.metas')->where('slug = ?', $slug));
					foreach( $cat as $mid )
						if ( in_array($mid, $children) ) $flag = true;
				}
			} else {
				// 获取分类名称
					$cat = $db->fetchObject($db->select('mid')->from('table.metas')->where('slug = ?', $edit->category));
					if ( in_array($cat->mid, $children) ) $flag = true;
			}
			if ( $flag ) {
				// 解析url
				$uri = parse_url($edit->fields->url);
				if ( isset($uri['host']) ) $edit->setField('host', 'str', $uri['host'], $edit->cid);
			}
		}
		// 载入模板函数文件
		if ( !function_exists('cache') ) require Helper::options()->themeFile('flkc', 'functions.php');
		// 清空缓存
		cache(null);
	}
}
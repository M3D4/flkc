<?php
if ( !defined('__TYPECHO_ROOT_DIR__') ) exit;

set_time_limit(0); // 设置超时时间
ob_end_clean();
ob_implicit_flush();
header("Content-Encoding: none\r\n");
header('Content-type:text/html;charset=utf-8'); // 设置页面编码
header('Cache-Control: no-cache');
header('X-Accel-Buffering: no');

include 'header.php';
include 'menu.php';

if ( !function_exists('verify_link') ) require __DIR__ . '/functions.php';

// 获取db对象
$db = Typecho_Db::get();
?>
<div class="main">
    <div class="body container">
        <div class="typecho-page-title"><h2>链接检测<a href="javascript:check();">检测</a></h2></div>
        <div class="row typecho-page-main" role="main">
            <div class="col-mb-12 typecho-list">
                <div class="typecho-table-wrap">
                    <table class="typecho-list-table">
                        <thead>
                            <tr>
                                <th>序号</th>
                                <th>网站名称</th>
                                <th>链接地址</th>
                                <th>所属分类</th>
                                <th>链接状态</th>
                                <th>连接操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $children = Typecho_Widget::widget('Widget_Metas_Category_List')->getAllChildren(Helper::options()->parent);?>
                            <?php foreach ( $children as $mid ) :?>
                            <?php $cat = Typecho_Widget::widget('Widget_Metas_Category_List')->getCategory($mid);?>
                            <?php $res = $db->fetchAll($db->select()->from('table.contents')->join('table.relationships', 'table.contents.cid = table.relationships.cid', Typecho_Db::LEFT_JOIN)->where('table.relationships.mid = ?', $mid));?>
                            <?php foreach ( $res as $post ) :?>
                            <?php $post['url'] = $db->fetchObject($db->select('str_value')->from('table.fields')->where('cid = ?', $post['cid'])->where('name = ?', 'url'))->str_value;?>
                            <tr id="post-<?php echo $post['cid'];?>">
                                <td><?php echo $post['cid'];?></td>
                                <td><?php echo $post['title'];?></td>
                                <td><?php echo $post['url'];?></td>
                                <td><?php echo $cat['name'];?></td>
                                <td><?php echo get_state($post['state']);?></td>
                                <td><a href="javascript:del('<?php echo $post['cid'];?>');">删除链接</a></td>
                            </tr>
                            <?php endforeach;?>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'copyright.php';?>
<script src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="//apps.bdimg.com/libs/layer/2.1/layer.js"></script>
<script>
// 循环变量
var current = 1;
// 获取行数
var rows = $('table tr').length;

// 检测网址
function check() {
    // 结束则返回
    if ( current >= rows ) return layer.msg('链接已全部检测完成！');
    // 获取编号
    var cid = $('table tr').eq(current).children().eq(0).text();
    // 获取网址
    var url = $('table tr').eq(current).children().eq(2).text();
    // 加载
    var index = layer.load(1, {shade: [0.1, '#FFF']});
    // 显示第几条
    layer.msg('正在验证序号为第 ' + cid + ' 的链接数据！');
    // 数据提交
    $.ajax({
        url: '/flkc/check.do',
        type: 'POST',
        dataType: 'json',
        data: {cid: cid, url: encodeURIComponent(url)},
        success: function(data) {
            // 关闭加载
            layer.close(index);
            // 正确
            if ( data.error == 0 ) {
                // 修改结果
                $('table tr').eq(current).children().eq(4).html(data.msg);
            } else { // 错误
                // 显示错误信息
                layer.msg(data.msg);
            }
            // 循环自增
            current++;
            // 下一条
            check();
        },
        error: function() {
            // 关闭加载
            layer.close(index);
            // 输出错误
            layer.msg('链接编号：' + cid + '，网址：' + url + ' 的链接提交错误！');
            // 循环自增
            current++;
            // 下一条
            check();
        }
    });
}

// 删除链接
function del(cid) {
    if ( cid == '' ) return layer.msg('链接编号获取错误，无法删除！');
    // 加个锁防止重复提交
    var lock = false;
    layer.confirm('您确定要删除该链接么？', {
        btn: ['确定', '取消']
    }, function(index){
        // 判断锁状态
        if ( lock ) return;
        // 设置锁定
        lock = true;
        // 加载
        var i = layer.load(1, {shade: [0.1, '#FFF']});
        $.ajax({
            url: '/flkc/delete.do',
            type: 'POST',
            dataType: 'json',
            data: {cid: cid},
            success: function(data) {
                layer.close(i);
                if(data.error == 0) {
                    layer.msg('删除成功', {icon: 1});
                    return $('#post-'+cid).remove();
                }
                return layer.msg(data.msg, {icon: 2});
            },
            error: function(data) {
                layer.close(i);
                layer.msg('删除失败，提交失败！', {icon: 5});
            }
        });
        layer.close(index);
    });
}

// 输出表格
function show(msg) {
    $('tbody').append(msg);
}
</script>
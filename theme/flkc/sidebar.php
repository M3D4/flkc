<?php if ( !defined('__TYPECHO_ROOT_DIR__') ) exit;?>
<div class="sidebar">
    <ul>
        <li><a href="<?php $this->options->siteUrl();?>" rel="nofollow"><i class="fa fa-home pad5" aria-hidden="true"></i> 首页</a></li>
        <?php $children = $this->widget('Widget_Metas_Category_List')->getAllChildren($this->options->parent);?>
        <?php foreach ( $children as $mid ) :?>
        <?php $cat = $this->widget('Widget_Metas_Category_List')->getCategory($mid); ?>
        <li><a href="#<?php echo $cat['slug'];?>" title="<?php echo $cat['name'];?>" rel="nofollow"><?php echo $cat['name'];?></a></li>
        <?php endforeach; ?>
        <li><a href="#top" rel="nofollow"><i class="fa fa-angle-up" aria-hidden="true"></i> 顶部</a></li>
    </ul>
    <div><p></p></div>
</div>
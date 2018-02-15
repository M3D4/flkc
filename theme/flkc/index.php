<?php
/**
 * 福利开车网 Typecho 导航站主题
 *
 * @package flkc
 * @author 福利开车网
 * @version 0.1
 * @link https://www.fulikaiche.com
 */
if ( !defined('__TYPECHO_ROOT_DIR__') ) exit;
$this->need('header.php');
?>
		<div class="block" id="mingzhan">
			<div class="title">
				<h2>名站推荐</h2>
				<div></div>
			</div>
			<ul class="list">
				<?php $i = 1;?>
				<?php $this->widget('Widget_Archive@mingzhan-ad', 'pageSize=30&type=category&page=1', 'mid=' . $this->options->mingzhanads)->to($mingzhanads); ?>
				<?php if ( $mingzhanads->have() ) :?>
				<?php while ( $mingzhanads->next() ) :?>
				<li><a href="<?php $mingzhanads->fields->url();?>" title="<?php $mingzhanads->title();?>" rel="external nofollow noopener" target="_blank"<?php if ( isset($mingzhanads->fields->color) ) :?> style="color:#<?php $mingzhanads->fields->color();?>"<?php endif;?>><?php $mingzhanads->title();?></a></li>
				<?php $i++;?>
				<?php endwhile;?>
				<?php endif;?>
				<?php if ( $i < 31 ) :?>
				<?php for ( $i; $i <= 30; $i++ ) :?>
				<li><a href="/ad.html" title="期待您的加入" rel="external nofollow noopener" target="_blank">期待您的加入</a></li>
				<?php endfor;?>
				<?php endif;?>
			</ul>
		</div>
		<?php $metas = get_categories();?>
		<?php foreach ( $metas as $meta ) :?>
		<div class="block" id="<?php echo $meta['slug'];?>">
			<div class="title">
				<h2><?php echo $meta['name'];?></h2>
				<div></div>
			</div>
			<ul class="list">
				<?php $i = 1;?>
				<?php $links = get_links($meta['mid']);?>
				<?php foreach ( $links as $post ) :?>
				<?php $post['fields'] = get_fields($post['cid']);?>
				<li><a<?php if ( 1000 < $post['referers'] ) :?> class="hot"<?php endif;?><?php if ( !empty($post['fields']['color']) ) :?> style="color:<?php echo $post['fields']['color'];?>"<?php endif;?> href="<?php echo $post['fields']['url'];?>" title="<?php echo $post['title'];?>" rel="external nofollow noopener" target="_blank"><?php echo $post['title'];?></a></li>
				<?php $i++;?>
				<?php endforeach;?>
				<?php $ct = $i % 6;?>
				<?php while ( $ct <= 6 ) :?>
				<li><a href="/add.html" title="虚位以待" rel="external nofollow noopener" target="_blank">虚位以待</a></li>
				<?php $ct++;?>
				<?php endwhile;?>
			</ul>
			<div class="clearfix"></div>
		</div>
		<?php endforeach;?>
	</div>
	<?php $this->need('sidebar.php');?>
</div>
<?php $this->need('footer.php');?>
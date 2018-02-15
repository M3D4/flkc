<?php if ( !defined('__TYPECHO_ROOT_DIR__') ) exit;?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php $this->options->title();?></title>
<?php $this->header('rss1=&rss2=&atom=&generator=&template=&pingback=&xmlrpc=&wlw=&commentReply=&antiSpam='); ?>
<link rel="shortcut icon" href="<?php $this->options->shortcut();?>">
<link rel="stylesheet" href="<?php $this->options->themeUrl('style.css');?>">
<link rel="stylesheet" href="//apps.bdimg.com/libs/fontawesome/4.4.0/css/font-awesome.min.css">
</head>
<body>
<div class="head">
	<div class="header">
		<div class="logo" id="top"><a href="<?php $this->options->siteUrl();?>" title="<?php $this->options->title();?>"><?php $this->options->title();?></a></div>
		<p class="pc"><?php $this->options->intro();?></p>
		<p class="wap"><a href="<?php $this->options->siteUrl();?>" title="<?php $this->options->title();?>"><?php $this->options->title();?> - 手机版</a></p>
	</div>
</div>
<div class="clearfix"></div>
<div class="wrap">
	<div class="nav pc">
		<ul class="menu">
			<li><a<?php if ( $this->is('index') ) : ?> class="active"<?php endif;?> href="<?php $this->options->siteUrl();?>" title="首页">首页</a></li>
			<li><a href="https://www.fulikaiche.com/" target="_blank" rel="external" title="福利开车网">福利博客</a></li>
			<?php if ( 'no' === $this->options->isauto ) :?>
			<li><a<?php if ( $this->is('page', 'add') ) :?> class="active"<?php endif;?> href="/add.html" title="广告收录">广告收录</a></li>
			<?php else :?>
			<li><a<?php if ( $this->is('page', 'add') ) : ?> class="active"<?php endif;?> href="/add.html" title="自助收录">自助收录</a></li>
			<li><a<?php if ( $this->is('page', 'ad') ) : ?> class="active"<?php endif;?> href="/ad.html" title="广告合作">广告合作</a></li>
			<?php endif;?>
		</ul>
		<p class="xuanyan"><?php $this->options->xuanyan();?></p>
	</div>
	<div class="clearfix"></div>
	<div class="main">
		<div class="top">
			<div class="left">
				<?php $this->widget('Widget_Archive@top-ad', 'pageSize=20&type=category&page=1', 'mid=' . $this->options->topads)->to($topads); ?>
				<?php if ( $topads->have() ) :?>
				<?php while ( $topads->next() ) :?>
				<a href="<?php $topads->fields->url();?>" target="_blank" rel="external nofollow noopener" title="<?php $topads->title();?>"<?php if ( isset($topads->fields->color) ) :?> style="color:#<?php $topads->fields->color();?>"<?php endif;?>><?php $topads->title();?></a>
				<?php endwhile;?>
				<?php endif;?>
				<?php $i = $topads->getTotal();?>
				<?php if ( $i < 20 ) :?>
				<?php for ( $i; $i < 20; $i++ ) :?>
				<a href="/ads" target="_blank" rel="external nofollow noopener" title="黄金广告位招租">黄金广告位招租</a>
				<?php endfor;?>
				<?php endif;?>
			</div>
			<div class="right">
				<div class="title"><h2>本站动态</h2><p></p></div>
				<ul class="news">
					<?php $this->widget('Widget_Archive@news', 'type=category', 'mid=' . $this->options->news)->to($news); ?>
					<?php if ( $news->have() ) :?>
					<?php while ( $news->next() ) :?>
					<li><span><?php $news->date('Y-m-d');?></span> <a href="javascript:notice('<?php $news->cid();?>');" title="<?php $news->title();?>"><?php $news->title();?></a></li>
					<?php endwhile;?>
					<?php endif;?>
				</ul>
				<a class="siteurl" href="javascript:show();" rel="external nofollow"><div>永久网址</div><span><?php $this->options->yj_url();?></span></a>
			</div>
		</div>
		<div class="clearfix"></div>
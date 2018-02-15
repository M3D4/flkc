<?php if ( !defined('__TYPECHO_ROOT_DIR__') ) exit;?>
<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>您访问的页面已丢失 | <?php $this->options->title();?></title>
  <style>a,abbr,acronym,address,applet,article,aside,audio,b,big,blockquote,body,canvas,caption,cite,code,dd,del,details,dfn,div,dl,dt,em,embed,fieldset,figcaption,figure,footer,form,h1,h2,h3,h4,h5,h6,header,hgroup,html,i,iframe,img,ins,kbd,label,legend,mark,menu,nav,nav li,nav ul,object,ol,output,p,pre,q,ruby,s,samp,section,small,span,strike,strong,sub,summary,sup,table,tbody,td,tfoot,th,thead,time,tr,tt,u,var,video{margin:0;padding:0;border:0;vertical-align:baseline;font:inherit;font-size:100%}article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section{display:block}ol,ul{margin:0;padding:0;list-style:none}blockquote,q{quotes:none}blockquote:after,blockquote:before,q:after,q:before{content:'';content:none}table{border-collapse:collapse;border-spacing:0}a{text-decoration:none}.txt-rt{text-align:right}.txt-lt{text-align:left}.txt-center{text-align:center}.float-rt{float:right}.float-lt{float:left}.clear{clear:both}.pos-relative{position:relative}.pos-absolute{position:absolute}.vertical-base{vertical-align:baseline}.vertical-top{vertical-align:top}.underline{margin:0 0 20px;padding-bottom:5px;border-bottom:1px solid #eee}nav.vertical ul li{display:block}nav.horizontal ul li{display:inline-block}img{max-width:100%}body{background:url(<?php $this->options->themeUrl('img/404-bg.png');?>);font-family:Century Gothic Geneva Helvetica,sans-serif}.wrap{margin:0 auto;width:96%}h1{margin-top:20px;color:#603813;text-transform:uppercase;font-weight:700;font-size:3em}.banner{margin-top:30px;text-align:center}.banner img{margin-top:0}.page{text-align:center;font-family:Century Gothic}.page h2{color:#632c25;font-weight:700;font-size:3em}.footer{position:absolute;right:30px;bottom:20px;font-family:Century Gothic}.footer p{color:#603813;font-size:1em}.footer a{color:#f9614d}.footer a:hover{text-decoration:underline}@media all and (max-width:1366px) and (min-width:1280px){.wrap{width:90%}.banner{margin-top:30px}}@media all and (max-width:1280px) and (min-width:1024px){.wrap{width:90%}}@media all and (max-width:1024px) and (min-width:800px){.wrap{width:90%}h1{font-size:2em}.banner{margin-top:0}.page h2{font-size:2em}}@media all and (max-width:800px) and (min-width:640px){.wrap{width:90%}h1{font-size:2em}.banner{margin-top:10px}.banner img{width:34%}.page h2{font-size:2em}}@media all and (max-width:640px) and (min-width:480px){.wrap{width:90%}h1{font-size:1.6em}.banner{margin-top:0}.banner img{width:32%}.page h2{font-size:1.6em}}@media all and (max-width:480px) and (min-width:320px){.wrap{width:90%}h1{font-size:1.4em}.banner{margin-top:0}.banner img{width:32%}.page h2{font-size:1.4em}.footer p{font-size:.9em}}@media all and (max-width:320px){.wrap{width:90%}h1{font-size:1.4em}.banner{margin-top:10px}.banner img{width:80%}.page h2{font-size:1.4em}.footer{bottom:10px}.footer p{font-size:.9em}}</style>
</head>
<body>
<div class="wrap">
  <a href="<?php $this->options->siteUrl();?>" title="<?php $this->options->title();?>"><h1><?php $this->options->title();?></h1></a>
  <div class="banner"><img src="<?php $this->options->themeUrl('img/404.png');?>" alt="404"></div>
  <div class="page">
    <h2>非常抱歉，您所查找的页面已丢失，请返回首页重新访问！</h2>
  </div>
  <div class="footer">
    <p>Copyright &copy;2017 <?php $this->options->yj_url();?> <a href="<?php $this->options->siteUrl();?>" title="<?php $this->options->title();?>"><?php $this->options->title();?></a></p>
  </div>
</div>
</body>
</html>
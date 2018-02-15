<?php
/**
 * 自助收录
 *
 * @package custom
 */
if ( !defined('__TYPECHO_ROOT_DIR__') ) exit;
$this->need('header.php');
?>
<?php if ( 'yes' === $this->options->isauto ) :?>
		<div class="block">
			<div class="title">
				<h2>自助收录</h2>
				<div></div>
			</div>
			<div class="add">
				<div class="intro">
					<div class="row">
						<p>自助收录使用说明，请务必认真阅读！</p>
						<ol>
							<li>请先在贵站显眼位置添加本站友情链接，网站名称：<?php $this->options->link_name();?>，网站地址：<?php $this->options->link_url();?></li>
							<li>返回本页面填写贵站信息并提交</li>
							<li>系统自动查询贵站链接是否正确，正确则自动添加，否则无法添加</li>
							<li>填写的链接地址必须包含 http:// 或 https:// 否则不通过</li>
						</ol>
					</div>
					<div class="row">
						<p>关于本站收录相关说明</p>
						<ol>
							<li>本站将不定时检查贵站友情链接，若未通知站长情况下将本站链接删除，则贵站将不再收录</li>
							<li>本站链接使用来路排名进行排序，来路越多则排名越高，但禁止出现刷排名情况，一经发现永不收录</li>
							<li>本站会不定时抽查贵站，若出现弹窗广告、病毒等，或被网友举报，则立即删除贵站收录</li>
						</ol>
					</div>
				</div>
				<div class="form">
					<div class="line">
						<label>网站名称：</label>
						<input type="text" id="sitename" required>
					</div>
					<div class="line">
						<label>网站地址：（包含 http:// 或 https://）</label>
						<input type="text" id="siteurl" required>
					</div>
					<div class="line">
						<label>网站类型：</label>
						<select id="category">
							<option value="0">请选择网站类型</option>
							<?php $children = $this->widget('Widget_Metas_Category_List')->getAllChildren($this->options->parent);?>
							<?php foreach ( $children as $mid ) :?>
							<?php $cat = $this->widget('Widget_Metas_Category_List')->getCategory($mid); ?>
							<option value="<?php echo $cat['mid'];?>"><?php echo $cat['name'];?></option>
							<?php endforeach;?>
						</select>
					</div>
					<div class="line"><button id="submit">提交收录</button></div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php else :?>
		<div class="block">
			<div class="title">
				<h2>网站收录</h2>
				<div></div>
			</div>
			<div class="add">
				<div class="intro">
					<div class="row">
						<p>网站收录说明，请务必认真阅读！</p>
						<ol>
							<li>请先在贵站显眼位置添加本站友情链接，网站名称：<?php $this->options->link_name();?>，网站地址：<?php $this->options->link_url();?></li>
							<li>使用邮箱发布贵站信息至管理员邮箱</li>
							<li>管理员在审核通过后将贵站链接添加至本站导航中</li>
						</ol>
					</div>
					<div class="row">
						<p style="text-align:center">联系邮箱<br><br><?php $this->options->email();?></p>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php endif;?>
<?php $this->need('footer.php');?>
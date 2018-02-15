<?php
/**
 * 广告合作
 *
 * @package custom
 */
if ( !defined('__TYPECHO_ROOT_DIR__') ) exit;
$this->need('header.php');
?>
        <div class="block">
            <div class="title">
                <h2>广告合作</h2>
                <div></div>
            </div>
            <div class="add">
                <div class="intro">
                    <div class="row">
                        <p style="text-align:center">联系邮箱<br><br><?php $this->options->email();?></p>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<?php $this->need('footer.php');?>
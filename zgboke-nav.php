<?php 
/*
Plugin Name: ZgBoke Nav
Plugin URI: http://zhangge.net/4750.html
Description: <strong>中国博客联盟-成员展示导航Wordpress插件</strong>，通过这个插件可以快速部署中国博客联盟成员展示页面，方便联盟成员互相访问，同时可获得中国博客联盟荣誉成员称号，并得到各种优先展示的权利！首次启用请进入插件设置界面进行喜好设定。
Version: 1.2.2
Author: 张戈
Author URI: http://zhangge.net/
Copyright: 中国博客联盟原创插件，任何个人或团体不可擅自更改版权。
*/
class zgboke_nav{
  function __construct(){
    add_shortcode( 'zgboke.com', array( $this, 'zgboke_nav_page_sc' ) );
  }
  function zgboke_nav_page_sc( $atts, $content){
    include('zgboke-nav-page.php');
    return $content;
  }
}
register_activation_hook(__FILE__, 'zgboke_nav_install');
function zgboke_nav_install() {
    add_option("fav_display", "display", '', 'yes');
    add_option("auto_load", "m_load", '', 'yes');
    add_option("nav_position", "before", '', 'yes');
}
add_filter('plugin_action_links', 'zgboke_nav_plugin_action_links', 10, 3);
function zgboke_nav_plugin_action_links($action_links, $plugin_file, $plugin_info) {
    $this_file = basename(__FILE__);
    if(substr($plugin_file, -strlen($this_file))==$this_file) {
        $new_action_links = array(
        "<a href='options-general.php?page=zgboke_nav'>设置</a>"
        );
        foreach($action_links as $action_link) {
        if (stripos($action_link, '>Edit<')===false) {
            if (stripos($action_link, '>Deactivate<')!==false) {
                $new_action_links[] = $action_link;
            } else {
                $new_action_links[] = $action_link;
                    }
                }
            }
    return $new_action_links;
        }
  return $action_links;
        }
new zgboke_nav();
?>
<?php   
if( is_admin() ) {   
    add_action('admin_menu', 'display_zgboke_nav_menu');   
}   
function display_zgboke_nav_menu() {   
    add_options_page('中国博客联盟插件设置', 'Zgboke-Nav','administrator','zgboke_nav', 'display_zgboke_nav_page');
}   
function display_zgboke_nav_page() {   
?>
<style type="text/css">
#setting_page {position:relative;}
h3{margin-top: 30px;}
#num{ margin-left:20px;}
#year_num,#mon_num,#week_num{width: 30px;height: 20px;text-align: center;margin: 10px 0 0 0;}
#readers_page_Id{width: 60px;height: 25px;text-align: center;font-weight: normal;}
#m_load,#hidden{margin-left: 45px;}
#nav_after{margin-left: 31px;}
#m_load_info{height: 95px;}
#readers_after{margin-left: 32px;}
#page_list{padding: 0px 0px 0 17px;
margin: -250px 0 0 376px;
border: 1px solid #ccc;
width: 175px;
height: auto;
position: fixed;
}
</style>
<div id="setting_page"> 
    <div style="width: 492px;"><h2>中国博客联盟插件设置</h2>
    <form accept-charset="GBK" action="https://shenghuo.alipay.com/send/payment/fill.htm" method="POST" target="_blank"><input name="optEmail" type="hidden" value="ge@zhangge.net" />
    <input name="payAmount" type="hidden" value="0" />
    <input id="title" name="title" type="hidden" value="赞助张戈博客" />
    <input name="memo" type="hidden" value="请填写您的联系方式，以便张戈答谢。" />
    <input title="如果好用，您可以赞助张戈博客" name="pay" src="<?php echo plugins_url('payment.png',__FILE__);?>" type="image" value="捐赠共勉" style="float: right;margin-top: -43px;"/>
    </form>
</div>
<form method="post" action="options.php" onsubmit="return checkTab('submit');">   
    <?php 
        wp_nonce_field('update-options');
        if (get_option('fav_display')=="hidden"){
            $fav_hidden='checked="checked"';
        } else {
            $fav_display='checked="checked"';
        }
        if (get_option('auto_load')=="auto_load"){
            $auto_load='checked="checked"';
        } else {
            $m_load='checked="checked"';
        }
        if (get_option('nav_position')=="after"){
            $nav_after='checked="checked"';
        } else {
            $nav_before='checked="checked"';
        }
?> 
<p><h3>一、是否显示网站图标</h3>
    <input type="radio" name="fav_display" id="display" value="display" <?php echo $fav_display;?>/>
    <label for="display" style="cursor: pointer;">显示</label>
    <input type="radio" name="fav_display" id="hidden" value="hidden" <?php echo $fav_hidden;?>/>
    <label for="hidden" style="cursor: pointer;">隐藏</label>
</p>
<p><h3>二、加载设置</h3>
<h4>①、加载模式</h4>
    <input type="radio" name="auto_load" id="auto_load" onclick="checkTab(true)" value="auto_load" <?php echo $auto_load;?>/>
    <label for="auto_load" style="cursor: pointer;">自动</label>
    <input type="radio" name="auto_load" id="m_load" onclick="checkTab(false)" value="m_load" <?php echo $m_load;?>/>
    <label for="m_load" style="cursor: pointer;">手动</label>
</p>
<?php if(get_option('auto_load')=="auto_load"){ ?>
    <div id="pageid" style="display:block;">
<?php }else { ?>
    <div id="pageid" style="display:none;">
<?php } ?>
<h4>②、加载位置</h4>
    <input type="radio" name="nav_position" id="nav_before" value="before" <?php echo $nav_before;?>/>
    <label for="nav_before" style="cursor: pointer;">文章前</label>
    <input type="radio" name="nav_position" id="nav_after" value="after" <?php echo $nav_after;?>/>
    <label for="nav_after" style="cursor: pointer;">文章后</label>
<div id="page_list">    
<h3>页面列表(名称：ID)</h3>
<span style="color: #080;">
<ol>
<?php
/* 单页面 */ 
$mypages = get_pages();
if(count($mypages) > 0) {
    foreach($mypages as $page) { ?>
      <li><?php echo get_the_title($page->ID); ?> : <?php echo $page->ID; ?></li>
<?php }} /* 单页面结束 */ ?> 
</ol>
</span>
</div>
<p><label for="page_Id"><b>请输入需要部署导航的页面ID：</b></label><input type="text" name="page_Id" id="page_Id" value="<?php echo  get_option('page_Id');?>" style="width: 60px;text-align: center;"/>
<p> 
</div>
<?php if(get_option('auto_load')=="m_load"){ ?>
    <div id="m_info" style="display:block;">
<?php }else { ?>
    <div id="m_info" style="display:none;">
<?php } ?>
<h4>手动部署方法：</h4>
    <p>方法①、后台编辑页面内容，在任意位置插入短代码“[zgboke.com]”,保存更新即可（短代码无法自定义位置）；</p>
    <p>方法②、若需要自定义导航出现的位置，请编辑主题页面模板(比如：gueskgook.php)，在合适的位置插入：&lt;?php zgboke_nav_page();?&gt;并保存。</p>
</div>
    <input type="hidden" name="action" value="update" />   
    <input type="hidden" name="page_options" value="fav_display,auto_load,page_Id,nav_position" />
    <input type="submit" value="保存设置" class="button-primary" />
</p>   
</form>
<script src="http://lib.sinaapp.com/js/jquery/1.9.1/jquery-1.9.1.min.js"></script>
<script type="text/javascript">
function checkTab(bool){
    if(bool && bool != 'submit'){
        $('#pageid').show();
        $('#m_info').hide();
        $("#pageid").css("display","block");
    }else if(bool != 'submit'){
        $('#pageid').hide();
       
         $('#m_info').show();
        $("#pageid").css("display","none");
    }
  var page_Id = document.getElementsByName("page_Id")[0].value;
  if(page_Id == '' && bool=='submit'){
            alert('请正确输入需要自动加载导航的页面ID!');
            return false;
    } 
}
</script>
</div>
<?php }
if(get_option('auto_load') == "auto_load") {
       add_filter('the_content',  'zgboke_nav_page');
    }
function zgboke_nav_page($content){
    $zgboke_nav='<style type="text/css">
        #zgboke-nav{
            margin-top:0px !important;
            margin-bottom:0px !important;
            margin:0px !important;
            text-indent: 0px !important;
        }
        #zgboke-nav p{
            line-height:0px !important;
            padding:0px !important;
            text-indent: 0px !important;
        }
        </style>
<div id="zgboke-nav">
<p>';
if (get_option('fav_display')=="hidden"){
    $zgboke_nav.='<script type="text/javascript" src="http://static.zgboke.com/hutui.js?delico" id="zgboke-nav-js"></script>';
} else {
    $zgboke_nav.='<script type="text/javascript" src="http://static.zgboke.com/hutui.js"></script></p>';
}
    $zgboke_nav.='</p>';
    $zgboke_nav.='</div>';

if(get_option('auto_load') == 'auto_load' && get_option('page_Id') == get_the_id()) {
   if (get_option('nav_position')=="after"){
        $content.="<br />".$zgboke_nav; 
    } else {
       $content=$zgboke_nav.$content; 
    }
    return $content;
} else if(get_option('auto_load') == 'm_load'){
    echo $zgboke_nav;
    } else {
       return $content;   
    }
}
?>
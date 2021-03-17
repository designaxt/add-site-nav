<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Add-Site-Nav
 *
 * @wordpress-plugin
 * Plugin Name:       Add Site Nav
 * Plugin URI:        http://www.designaxt.com/add-site-nav/
 * Description:       帮助在wordpress页面中建立一个收藏网址导航页面
 * Version:           1.0.0
 * Author:            designaxt
 * Author URI:        http://designaxt.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       add-site-nav
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ADD_SITE_NAV_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-add-site-nav-activator.php
 */


function addsitenav_enqueue() {
    wp_enqueue_script(
        'add-site-nav-script',
        plugins_url( 'add-site-nav.js', __FILE__ ),
        array( 'wp-blocks' )
    );
}
add_action( 'enqueue_block_editor_assets', 'addsitenav_enqueue' );


function addsitenav_stylesheet() {
    wp_enqueue_style( 'add-site-nav-style', plugins_url( 'style.css', __FILE__ ) );
}
add_action( 'enqueue_block_assets', 'addsitenav_stylesheet' );

// 获取网络图片保存到本地方法

function getImage($url, $save_dir = '', $filename = '', $type = 0)
    {
        if (trim($url) == '') {
            return array('file_name' => '', 'save_path' => '', 'error' => 1);
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
        }
        if (trim($filename) == '') {//保存文件名
            $ext = strrchr($url, '.');
            if ($ext != '.gif' && $ext != '.jpg') {
                return array('file_name' => '', 'save_path' => '', 'error' => 3);
            }
            $filename = time() . $ext;
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir .= '/';
        }
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return array('file_name' => '', 'save_path' => '', 'error' => 5);
        }
        //获取远程文件所采用的方法
        if ($type) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $img = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $img = ob_get_contents();
            ob_end_clean();
        }
        //$size=strlen($img);
        //文件大小
        $fp2 = @fopen($save_dir . $filename, 'a');
        fwrite($fp2, $img);
        fclose($fp2);
        unset($img, $url);
        return array('file_name' => $filename, 'save_path' => $save_dir . $filename, 'error' => 0);
    }


 // 新建一个post类别 网址

function addsitenav_setup_post_type() {
    register_post_type('website',
        array(
            'labels'      => array(
                'name'          => __('Site library', 'post type 名称'),
                'singular_name' => __('Site', '单个item名称'),
				'add_new'       => _x( 'Add site', '添加新内容的链接名称' ),
				'add_new_item'       => __( 'Add a new site' ),
    			'edit_item'          => __( 'Edit site' ),
    			'new_item'           => __( 'New site' ),
    			'all_items'          => __( 'All site' ),
    			'view_item'          => __( 'View site' ),
    			'search_items'       => __( 'Search site' ),
    			'not_found'          => __( 'Found no site' ),
    			'not_found_in_trash' => __( 'Not site in trash' ),
    			'parent_item_colon'  => '',
   				'menu_name'          => 'Site'
            ),
                'public'      => true,
				'supports'      => array( 'title', 'excerpt'),
                'has_archive' => false,
				'exclude_from_search' => true,
				'menu_icon' => 'dashicons-admin-site-alt3',
        )
    ); 
} 
add_action( 'init', 'addsitenav_setup_post_type' );


// 为网址添加分类体系

function taxonomies_site() {
	$labels = array(
	  'name'              => _x( 'Site category', 'taxonomy 名称' ),
	  'singular_name'     => _x( 'Site category', 'taxonomy 单数名称' ),
	  'search_items'      => __( 'Search site category' ),
	  'all_items'         => __( 'All site category' ),
	  'parent_item'       => __( 'Parent' ),
	  'parent_item_colon' => __( 'Parent：' ),
	  'edit_item'         => __( 'Edit site category' ),
	  'update_item'       => __( 'Update site category' ),
	  'add_new_item'      => __( 'Add a new site category' ),
	  'new_item_name'     => __( 'New site category' ),
	  'menu_name'         => __( 'Site category' ),
	);
	$args = array(
	  'labels' => $labels,
	  'hierarchical' => true,
	);
	register_taxonomy( 'site_category', 'website', $args );
  }
  add_action( 'init', 'taxonomies_site', 0 );


// 为网址添加metabox - url地址表单

function addsitenav_add_custom_box() {
    $screens = [ 'website' ];
    foreach ( $screens as $screen ) {
        add_meta_box(
            'nav_url_meta',                 // Unique ID
            'Site URL',      // Box title
            'addsitenav_html',  // Content callback, must be of type callable
            $screen                            // Post type
        );
    }
}

add_action( 'add_meta_boxes', 'addsitenav_add_custom_box' );

function addsitenav_html( $post ) {
	wp_nonce_field( 'addsitenav_html', 'addsitenav_html_nonce' );
    // 获取之前存储的值
    $value = get_post_meta( $post->ID, '_nav_url_meta', true );

    ?>
    <label for="nav_url_meta"></label>
	<input type="text" id="nav_url_meta" name="nav_url_meta" value="<?php echo esc_attr( $value ); ?>" placeholder="http(s)://" >
    <?php
}


// 为网址添加metabox - 权重表单

function addsitenav_add_weight_custom_box() {
    $screens = [ 'website' ];
    foreach ( $screens as $screen ) {
        add_meta_box(
            'nav_weight_meta',                 // Unique ID
            'Weight',      // Box title
            'addsitenav_weight',  // Content callback, must be of type callable
            $screen                            // Post type
        );
    }
}

add_action( 'add_meta_boxes', 'addsitenav_add_weight_custom_box' );

function addsitenav_weight( $post ) {
	wp_nonce_field( 'addsitenav_weight', 'addsitenav_weight_nonce' );
    // 获取之前存储的值
    $value = get_post_meta( $post->ID, '_nav_weight_meta', true );

    ?>
    <label for="nav_weight_meta"></label>
	<input type="number" id="nav_weight_meta" name="nav_weight_meta" value="<?php echo esc_attr( $value ); ?>" placeholder="网站权重" >
    <?php
}


// 存储meta表单内容

add_action( 'save_post', 'nav_weight_save_meta_box' );
function nav_weight_save_meta_box($post_id){

    // 安全检查
    // 检查是否发送了一次性隐藏表单内容（判断是否为第三者模拟提交）
    if ( ! isset( $_POST['addsitenav_weight_nonce'] ) ) {
        return;
    }
    // 判断隐藏表单的值与之前是否相同
    if ( ! wp_verify_nonce( $_POST['addsitenav_weight_nonce'], 'addsitenav_weight' ) ) {
        return;
    }
    // 判断该用户是否有权限
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // 判断 Meta Box 是否为空
    if ( ! isset( $_POST['nav_weight_meta'] ) ) {
        return;
    }
	
    $nav_weight_meta = sanitize_text_field( $_POST['nav_weight_meta'] );
    update_post_meta( $post_id, '_nav_weight_meta', $nav_weight_meta );
}

add_action( 'save_post', 'nav_url_save_meta_box' );
function nav_url_save_meta_box($post_id){

    // 安全检查
    // 检查是否发送了一次性隐藏表单内容（判断是否为第三者模拟提交）
    if ( ! isset( $_POST['addsitenav_html_nonce'] ) ) {
        return;
    }
    // 判断隐藏表单的值与之前是否相同
    if ( ! wp_verify_nonce( $_POST['addsitenav_html_nonce'], 'addsitenav_html' ) ) {
        return;
    }
    // 判断该用户是否有权限
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // 判断 Meta Box 是否为空
    if ( ! isset( $_POST['nav_url_meta'] ) ) {
        return;
    }
	
    $nav_url_meta = sanitize_text_field( $_POST['nav_url_meta'] );
    update_post_meta( $post_id, '_nav_url_meta', $nav_url_meta );

	// 获取网站的icon文件并保存到本地，命名为website_name.png

	$cache_url = WP_CONTENT_DIR.'/uploads/icon-cache';

	$img_success=getImage($url='https://api.faviconkit.com/'.preg_replace('#^(http(s?))?(://)#','', $_POST['nav_url_meta']).'/144', $save_dir = $cache_url, $filename = $_POST['post_title'].'.png', $type = 0);
	if($img_success['error']!=0){
		$img_try = getImage($url='https://api.faviconkit.com/'.preg_replace('#^(http(s?))?(://)#','', $_POST['nav_url_meta']), $save_dir = $cache_url, $filename = $_POST['post_title'].'.png', $type = 0);
	}
	if($img_try['error']!=0){
		getImage($url='https://api.clowntool.cn/getico/?url='.$_POST['nav_url_meta'], $save_dir = $cache_url, $filename = $_POST['post_title'].'.png', $type = 0);
	}
}


// 在全部网址页添加链接与分类标签栏
add_action("manage_website_posts_custom_column",  "nav_url_custom_columns",10,2);
add_filter("manage_website_posts_columns", "nav_url_edit_columns");
function nav_url_custom_columns($column){
    global $post;
    switch ($column) {
        case "nav_url_meta":{
            echo get_post_meta( $post->ID, '_nav_url_meta', true );
            break;}
		case "nav_weight_meta":{
			echo get_post_meta( $post->ID, '_nav_weight_meta', true );
			break;}
    }
}
function nav_url_edit_columns($columns){

    $columns['nav_url_meta'] = '链接';
	$columns['nav_weight_meta'] = '权重';

    return $columns;
}

add_action("manage_website_posts_custom_column",  "addsitenav_category_custom_columns");
add_filter("manage_website_posts_columns", "addsitenav_category_edit_columns");
function addsitenav_category_custom_columns($column){
    global $post;
    switch ($column) {
        case "site_category":
            $terms = get_the_term_list( $post_id , 'site_category' , '' , ',' , '' );
            if ( is_string( $terms ) )
                echo $terms;
            else
                _e( 'Unable to get category', 'your_text_domain' );
            break;
    }
}
function addsitenav_category_edit_columns($columns){

	$columns['site_category'] = '类别';

    return $columns;
}


// 添加快速编辑表单

add_action('quick_edit_custom_box',  'nav_add_quick_edit', 10, 2);
function nav_add_quick_edit($column_name, $post_type) {
    if ($column_name == 'nav_weight_meta') {//值与前方代码对应
        //请注意：<fieldset>类可以是：
        //inline-edit-col-left，inline-edit-col-center，inline-edit-col-right
        //所有列均为float：left，
        //因此，如果要在左列，请使用clear：both元素
        echo '
        <fieldset class="inline-edit-col-left" style="clear: both;">
            <div class="inline-edit-col"> 
                <label class="alignleft">
                    <span class="title">权重</span>
                    <span class="input-text-wrap"><input type="number" name="nav_weight_meta" class="ptitle" value=""></span>
                </label> 
                <em class="alignleft inline-edit-or"> 越大越靠前</em>
            </div>
        </fieldset>';
    }
}

add_action('save_post', 'nav_save_quick_edit_data');
function nav_save_quick_edit_data($post_id) {
    //如果是自动保存日志，并非我们所提交数据，那就不处理
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        return $post_id;
    // 验证权限，'sites' 为文章类型，默认为 'post' ,这里为我自定义的文章类型'sites'
    if ( 'website' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return $post_id;
    } else {
        if ( !current_user_can( 'edit_post', $post_id ) )
        return $post_id;
    }  
    $post = get_post($post_id); 
    // 'ordinal' 与前方代码对应
    if (isset($_POST['nav_weight_meta']) && ($post->post_type != 'revision')) {
        $nav_weight_meta = esc_attr($_POST['nav_weight_meta']);
        if ($nav_weight_meta)
            update_post_meta( $post_id, '_nav_weight_meta', $nav_weight_meta);// ‘_sites_order’为自定义字段
    } 
}


// 实时编辑
//add_action('admin_footer', 'nav_quick_edit_javascript');
// function nav_quick_edit_javascript() {
//     $current_screen = get_current_screen(); 
//     //条件判断，注意修改为对应值
//     if (!is_object($current_screen) || ($current_screen->post_type != 'website'))return;
//     if($current_screen->id == 'edit-website'){
//     //修改下方 js 代码中的 ordinal 为前方代码对应的值
//     echo"
//     <script type='text/javascript'>
//     jQuery(function($){
//         var wp_inline_edit_function = inlineEditPost.edit;
//         inlineEditPost.edit = function( post_id ) {
//             wp_inline_edit_function.apply( this, arguments );
//             var id = 0;
//             if ( typeof( post_id ) == 'object' ) {
//                 id = parseInt( this.getId( post_id ) );
//             }
//             if ( id > 0 ) {
//                 var specific_post_edit_row = $( '#edit-' + id ),
//                     specific_post_row = $( '#post-' + id ),
//                     product_price = $( '.column-nav_url_meta', specific_post_row ).text(); 
//                 $('input[name=\"nav_url_meta\"]', specific_post_edit_row ).val( product_price ); 
//             }
//         }
//     });
//     </script>";
//     } 
// } 



// 添加分类筛选

add_action('restrict_manage_posts','io_post_type_filter',10,2);
function io_post_type_filter($post_type, $which){
    if('website' !== $post_type){ //这里为自定义文章类型，需修改
      return; //检查是否是我们需要的文章类型
    }
    $taxonomy_slug     = 'site_category'; //这里为自定义分类法，需修改
    $taxonomy          = get_taxonomy($taxonomy_slug);
    $selected          = '';
    $request_attr      = 'site_category'; //这里为自定义分类法，需修改
    if ( isset($_REQUEST[$request_attr] ) ) {
      $selected = $_REQUEST[$request_attr];
    }
    wp_dropdown_categories(array(
      'show_option_all' =>  __("所有{$taxonomy->label}"),
      'taxonomy'        =>  $taxonomy_slug,
      'name'            =>  $request_attr,
      'orderby'         =>  'name',
      'selected'        =>  $selected,
      'hierarchical'    =>  true,
      'depth'           =>  5,
      'show_count'      =>  true,  
      'hide_empty'      =>  false,  
    ));
}
//此部分功能是列出指定分类下的所有文章
add_filter('parse_query','io_work_convert_restrict'); 
function io_work_convert_restrict($query) {  
    global $pagenow;  
    global $typenow;  
    if ($pagenow=='edit.php') {  
        $filters = get_object_taxonomies($typenow);  
        foreach ($filters as $tax_slug) {  
            $var = &$query->query_vars[$tax_slug];  
            if ( isset($var) && $var>0) {  
                $term = get_term_by('id',$var,$tax_slug);  
                $var = $term->slug;  
            }  
        }  
    }  
    return $query;  
}

// 在本地新建文件保存地址

register_activation_hook( __FILE__, 'addsitenav_plugin_activate' );
function addsitenav_plugin_activate() {
	$upload = wp_upload_dir();
	$upload_dir = $upload['basedir'];
	$upload_dir = $upload_dir . '/icon-cache';
	if (! is_dir($upload_dir)) {
		mkdir( $upload_dir, 0700 );
	}
}

 function activate_add_site_nav() {

	 // Trigger our function that registers the custom post type plugin.
	 addsitenav_setup_post_type(); 
	 // Clear the permalinks after the post type has been registered.
	flush_rewrite_rules(); 
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-add-site-nav-deactivator.php
 */
function deactivate_add_site_nav() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-add-site-nav-deactivator.php';
	Add_Site_Nav_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_add_site_nav' );
register_deactivation_hook( __FILE__, 'deactivate_add_site_nav' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-add-site-nav.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_add_site_nav() {

	$plugin = new Add_Site_Nav();
	$plugin->run();

}
run_add_site_nav();

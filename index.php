<?php
/*
Plugin Name: Newest Scrapes
Plugin URI:
Description: Copy content from websites into WordPress automatically.
Version:  1.0.1
Author: Johnjackmarshel
Author URI: 
Text Domain: ns-scrapes
Domain Path: /languages
*/

add_action('init', 'register_new_post_type');
add_action('admin_enqueue_scripts', "init_admin_js_css");
add_action("admin_head", "echo_js_vars");
add_action('manage_ns_scrape_posts_columns','add_post_column');
add_action('manage_ns_scrape_posts_custom_column','custom_columnset',10,2);
// add_filter('the_posts','get_post_data');
require(plugin_dir_path(__FILE__) . "classes/scrape-class.php");

function custom_columnset($column,$post_id){

    $post_status = get_post_status($post_id);
    $post_title = get_post_field('post_title',$post_id);
    $scrape_work_status = get_post_meta($post_id,'scrape_work_status',true);
    $run_count = get_post_meta($post_id,'scrape_run_count',true);
    $run_limit = get_post_meta($post_id,'scrape_run_limit',true);
    $run_unlimit = get_post_meta($post_id,'scrape_run_unlimit',true);
    $class = '';  $state_text = "";
    switch($column)
    {
        case 'status':
            if($post_status == 'trash')
            {
                $state_text = 'deactivated';
                $class = 'deactivated';
            }
            else if($scrape_work_status == 'running')
            {
                $status = 'running';
                $class = 'running';
            }
            else
            {
                $status = 'waiting';
                $class = 'waiting';
            }
            echo '<span class="ol_status ol_status_"' . $class . '_class">' . $status . "</span>";
            break;

        case 'schedules':
            $lateststart_time = get_post_meta($post_id,'scrape_start_time');
            $latestend_time = get_post_meta($post_id,'scrape_end_time');
            $count = get_post_meta($post_id,'scrape_run_count');

            if($scrape_work_status == 'running')
                $start_time = get_post_meta($post_id,'scrape_run_time');
            echo '<p>last start time:' . $lateststart_time[0] . '</p>';
            echo '<p>last Complete time:' . $latestend_time[0] . '</p>';
            
            if($scrape_work_status == 'running')
                echo '<p>start time:' . $start_time[0]  . '</p>';
            
            echo '<p>run count:' . $count[0] . '</p>';
            break;

        case 'actions':
            $wp_nonce = wp_create_nonce('scrape_action');
            if($post_status != 'trash')
            {
                 echo '<a href = "'.get_edit_post_link($post_id).'" class="button edit"><i class="icon ion-android-create"></i>Edit</a>';

               if($scrape_work_status == 'running')
               {
                    echo '<a href="'.admin_url('edit.php?post_type=ns_scrape&action=pause&post_id='.$post_id.'&_wpnonce='.$wp_nonce).'" class="button pause"><i class="icon ion-pause"></i>Pause</a>';
                    echo '<a href="'.admin_url('edit.php').'" class="button view"><i class="icon ion-eye"></i>View</a>';
               }
               else
               {
                   echo '<a href="'.admin_url('edit.php?post_type=ns_scrape&action=play&post_id='.$post_id.'&_wpnonce='.$wp_nonce).'" class="button run"><i class="icon ion-play"></i>Play</a>';
               }

               echo '<a href="'.admin_url('edit.php?post_type=ns_scrape&action=copy&post_id='.$post_id.'&_wpnonce='.$wp_nonce).'" class="button copy"><i class="ion-ios-copy"></i>Copy</a>';
               echo '<a href="'.get_delete_post_link($post_id).'" class="button trash"><i class="icon ion-trash-a"></i>Trash</a>';
            }
            else
            {
                $untrash = wp_create_nonce('untrash-post_'.$post_id);
                echo '<a href="'.admin_url('post.php?post='.$post_id.'&action=untrash&_wpnonce='.$untrash).'" class="button untrash"><i class="icon ion-forward"></i>UnTrash</a>';
            }
            break;
        
    }
}

function add_post_column($columns){
    unset($columns['date']);
    return array_merge($columns,array('status'=>__('Status'),'schedules'=>__('Schedules'),'actions'=>__('Actions')));
}

                                                
function register_new_post_type() {
    register_post_type("ns_scrape", array(
        'labels' => array(
            'name' => 'nsScrapes',
            'add_new' => __('Add New', 'ns_scrape'),
            'all_items' => __('All nsScrapes', 'ns_scrape')
        ),
        'public' => true,
        'publicly_queriable' => true,
        'show_ui' => true,
        'menu_position' => 24,
        'menu_icon' => 'dashicons-editor-paste-text',
        'supports' => array('custom-fields'),
        'register_meta_box_cb' => 'register_scrape_meta_boxes',
        'has_archive' => true,
        'rewrite'       => array(
                  'slug'   => $testSlug,
                  'pages'  => false,
                  'feeds'  => false,
              ),
        'capability_type' => 'post',
        'can_export'    =>  true,
        'filter'    =>  'raw'
    ));

      getWP()->add_query_var('ns_scrape_passing');
      $rewrite = getRewrite();
      $prefix  = implode('/', array_filter(array(
              trim($rewrite->root,  '/'),
              trim($rewrite->front, '/'),
              $testSlug,
      )));
      $rewrite->add_rule(
          $prefix . '/([^/]+)/([a-z0-9]+[a-f0-9]{32})/?(.*)$',
          $rewrite->index . '?ns_scrpae=$matches[1]&ns_scrape_passing=$matches[2]&post_type=wpt_test&$matches[3]',
          'top'
      );
    //var_dump(get_posts(array('post_type'=>'ns_scrape')));exit;
}


function getRewrite()
{
    return $GLOBALS['wp_rewrite'];
}

 function getWP()
{
    return $GLOBALS['wp'];
}

 function echo_js_vars() {
    echo "<script>var plugin_path = '" . plugins_url() . "';</script>";
}

function init_admin_js_css($hook_suffix) {
    wp_enqueue_script("ns_admin_jquery", plugins_url("assets/js/myadminjs.js", __FILE__), null, OL_VERSION);
    wp_enqueue_script("ns_jquery", plugins_url("libraries/jquery-2.2.4/jquery-2.2.4.min.js", __FILE__), null, OL_VERSION);
    wp_enqueue_script("ns_jquery_ui", plugins_url("libraries/jquery-ui-1.12.1.custom/jquery-ui.min.js", __FILE__), null, OL_VERSION);
    wp_enqueue_script("ns_bootstrap", plugins_url("libraries/bootstrap-3.3.7-dist/js/bootstrap.min.js", __FILE__), null, OL_VERSION);
    wp_enqueue_script("ns_angular", plugins_url("libraries/angular-1.5.8/angular.min.js", __FILE__), null, OL_VERSION);
    wp_enqueue_script("ns_main_js", plugins_url("assets/js/main.js", __FILE__), null, OL_VERSION);
    wp_enqueue_style("ns_main_css", plugins_url("assets/css/main.css", __FILE__));

}

function register_scrape_meta_boxes() {
    add_action("edit_form_after_title","show_scrape_options_html");
}

function show_action_html(){
   
    require plugin_dir_path(__FILE__) . "views/main.php";
}
function show_scrape_options_html() {
    if($_GET['action'] == 'edit')
    {
        global $post, $wpdb;
        $post_object = $post;
        $post_meta = get_post_meta($post_object->ID);
        $post_data = array();
        $post_data1 = $post_object->to_array(); 
        $get = $_GET['action'];
        $post_meta['scrape_post_custom_field'] = json_encode(unserialize($post_meta['scrape_post_custom_field'][0]));

        $post_meta['scrape_post_category'] = json_encode(unserialize($post_meta['scrape_post_category'][0]));
        $post_data = array_merge($post_data1,$post_meta);

       
    }
   

	require plugin_dir_path(__FILE__) . "views/main.php";
}






function system_info() {
    global $wpdb;

    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $system_info = "";
    $system_info .= "Website Name: " . get_bloginfo() . PHP_EOL;
    $system_info .= "Wordpress URL: " . site_url() . PHP_EOL;
    $system_info .= "Site URL: " . home_url() . PHP_EOL;
    $system_info .= "Wordpress Version: " . get_bloginfo('version') . PHP_EOL;
    $system_info .= "Multisite: " . (is_multisite() ? "yes" : "no") . PHP_EOL;
    $system_info .= "Theme: " . wp_get_theme() . PHP_EOL;
    $system_info .= "PHP Version: " . phpversion() . PHP_EOL;
    $system_info .= "PHP Extensions: " . json_encode(get_loaded_extensions()) . PHP_EOL;
    $system_info .= "MySQL Version: " . $wpdb->db_version() . PHP_EOL;
    $system_info .= "Server Info: " . $_SERVER['SERVER_SOFTWARE'] . PHP_EOL;
    $system_info .= "WP Memory Limit: " . WP_MEMORY_LIMIT . PHP_EOL;
    $system_info .= "WP Admin Memory Limit: " . WP_MAX_MEMORY_LIMIT . PHP_EOL;
    $system_info .= "PHP Memory Limit: " . ini_get('memory_limit') . PHP_EOL;
    $system_info .= "Wordpress Plugins: " . json_encode(get_plugins()) . PHP_EOL;
    $system_info .= "Wordpress Active Plugins: " . json_encode(get_site_option('active_plugins')) . PHP_EOL;
    return $system_info;
}


$scrape = new Scrape();
$scrape->initajax();
 
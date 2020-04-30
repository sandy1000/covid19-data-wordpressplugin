<?php

namespace PThemes_Covid19\admin;

use PThemes_Covid19\Plugin;
use PThemes_Covid19\Remote;

class Init{
    const PAGE_ID = 'pthemes-covid-19';

    public function __construct() {
        add_action('admin_init', [$this, 'admin_init']);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        add_action('init', [$this, 'covid_post_type']);
        add_action('after_setup_theme', [$this, 'register_shortcode']);
        add_action('add_meta_boxes', [$this,'covid_metaboxes']);
        add_action( 'save_post', array( $this, 'save_covidmeta') );
        add_filter( 'manage_covid_shortcode_posts_columns', array($this,'set_custom_edit_covid_shortcode_columns') );
        add_action( 'manage_covid_shortcode_posts_custom_column' , array($this,'custom_covid_shortcode_column'), 10, 2 );
        $this->setup_admin_page();
    }

    public function admin_init(){
       
    }

    public function register_shortcode() {
        add_shortcode("covid19charts", [$this, 'render_charts']);
    }

    public function render_charts($atts){
        if($atts['cid']){
            $pid = $atts['cid'];
            wp_enqueue_script('pthemes-covid19-js');

            $display = get_post_meta($pid, '_covid_chart_Display',true);
            $country = get_post_meta($pid,'_covid_chart_Country',true);

            if(get_post_meta($pid,'_covid_confirm_color',true)):
                $cnfrm_color = get_post_meta($pid,'_covid_confirm_color',true);
            else:
                $cnfrm_color = '#F4C363';
            endif;
        
            if(get_post_meta($pid,'_covid_rec_color',true)):
                $rec_color = get_post_meta($pid,'_covid_rec_color',true);
            else:
                $rec_color = '#60BB69';
            endif;
        
            if(get_post_meta($pid,'_covid_death_color',true)):
                $death_color = get_post_meta($pid,'_covid_death_color',true);
            else:
                $death_color = '#767676';
            endif;


            ob_start();
            echo '<div class="covid19-wdiget-tracker" data-color="'.$cnfrm_color.', '.$rec_color.', '.$death_color.'" data-theme="'.$display.'" data-timeout="100" data-country="'.$country.'"></div>';
            $content = ob_get_clean();
            return $content;
        }
    }

//register post type to generate short
    public function covid_post_type(){
         //register post type
         $labels = array(
            'name'                  => _x( 'Covid Shortcode', 'Post type general name', 'textdomain' ),
            'singular_name'         => _x( 'Covid Shortcode', 'Post type singular name', 'textdomain' ),
            'menu_name'             => _x( 'Covid-19 Data', 'Admin Menu text', 'textdomain' ),
        );
     
        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => false,
            'rewrite'            =>false,
            'capability_type'    => 'page',
            'has_archive'        => false,
            'hierarchical'       => false,
            'supports'           => array('title'),
            'menu_icon'           => 'dashicons-chart-pie',
        );
     
        register_post_type( 'covid_shortcode', $args );
        remove_post_type_support( 'covid_shortcode', 'editor');
    }

 //adding required metabox for shortcode generator
 
 public function covid_metaboxes()
{
    $screens = ['covid_shortcode'];
    foreach ($screens as $screen) {
        add_meta_box(
            'pthemes_covid',          
            __('Select Options','protech-covid19'),  
            array($this, 'render_covid_metalayout'), 
            $screen                  
        );

        add_meta_box(
            'pthemes_covid_shortcode',          
            __('Shortcode','protech-covid19'),  
            array($this, 'covid_show_shortcode'), 
            $screen,
            'side' ,
            'high'              
        );
    }
}

public function covid_show_shortcode($post){
    if(isset($post->ID)):
        $shortcode = '[covid19charts cid="'.$post->ID.'"]';
        echo '<span style="background: #f1f1f1;
        border: 1px solid #ded8d8;
        padding: 10px 5px;
        margin: 5px 0;
        display: block;
        width: 200px;">'.$shortcode.'</span>';
        echo '</br ><em>'.__('Copy shortcode','protech-covid19').'</em>';
    endif;
}

public function render_covid_metalayout($post){
    // Add an nonce field so we can check for it later.
    wp_nonce_field( 'covid_metalayout_box', 'covid_metalayout_box_nonce' );

    $opt_country = get_option( 'pthemes_covid19_countries' );

    $displayCharts = array('Card' => 'card', 'Line Graph' => 'line-chart', 'Bar Graph' => 'bar-chart');

    $chart_display = get_post_meta($post->ID,'_covid_chart_Display',true);
    $chart_country = get_post_meta($post->ID,'_covid_chart_Country',true);

    if(get_post_meta($post->ID,'_covid_confirm_color',true)):
        $cnfrm_color = get_post_meta($post->ID,'_covid_confirm_color',true);
    else:
        $cnfrm_color = '#F4C363';
    endif;

    if(get_post_meta($post->ID,'_covid_rec_color',true)):
        $rec_color = get_post_meta($post->ID,'_covid_rec_color',true);
    else:
        $rec_color = '#60BB69';
    endif;

    if(get_post_meta($post->ID,'_covid_death_color',true)):
        $death_color = get_post_meta($post->ID,'_covid_death_color',true);
    else:
        $death_color = '#767676';
    endif;

    
    echo '<div class="covid_metabox">
            <div style="width:40%; float:left; margin:10px 2%;">
                <label>'.__('Data Type','protech-covid19').'</label>
                <select name="_covid_chart_Display">';
                foreach($displayCharts as $key=>$value){
                    echo '<option '.selected($chart_display, $value, false ).' value='.$value.'>'.$key.'</option>';
                }
    echo '</select>
            </div>
            
            <div style="width:40%; float:left; margin:10px 2%;">
                <label>'.__('Location','protech-covid19').'</label>
                <select name="_covid_chart_Country">
                <option '.selected($chart_country,'global',false).' value="global" >World</option>
                ';
                if(!empty($opt_country)):
                    foreach($opt_country->countries  as $optc){
                        echo '<option '.selected($chart_country, $optc->iso2, false).' value='.$optc->iso2.'>'.$optc->name.'</option>';
                    }
                endif;

    echo '
    </select>
            </div>
            <div style="clear:both"></div>
            </br >
        </div>

        <div style="border-top:1px solid #ccc; class="covid_metabox">
                <div style="width:25%; float:left; margin:10px 2%;">
                <label for="favcolor">Confirmed:</label>
                <input type="color" id="favcolor" name="_covid_confirm_color" value="'.$cnfrm_color.'">
                </div>

                <div style="width:25%; float:left; margin:10px 2%;">
                <label for="favcolor">Recovered:</label>
                <input type="color" id="favcolor" name="_covid_rec_color" value="'.$rec_color.'">
                </div>

                <div style="width:25%; float:left; margin:10px 2%;">
                <label for="favcolor">Death:</label>
                <input type="color" id="favcolor" name="_covid_death_color" value="'.$death_color.'">
                </div>
                <div style="clear:both"></div>
        </div>
    ';
}

    public function save_covidmeta($post_id){
 
        // Check if our nonce is set.
        if ( ! isset( $_POST['covid_metalayout_box_nonce'] ) ) {
            return $post_id;
            
        }
 
        $nonce = $_POST['covid_metalayout_box_nonce'];
 
        if ( ! wp_verify_nonce( $nonce, 'covid_metalayout_box' ) ) {
            return $post_id; 
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
           
        }
 
        // Check the user's permissions.
        if ( 'covid_shortcode' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
                
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }
        
        // Sanitize the user input.
        $chart_display = sanitize_text_field($_POST['_covid_chart_Display']);
        $chart_country = sanitize_text_field($_POST['_covid_chart_Country']);
        $confirm_color = sanitize_text_field($_POST['_covid_confirm_color']);
        $recov_color = sanitize_text_field($_POST['_covid_rec_color']);
        $death_color = sanitize_text_field($_POST['_covid_death_color']);

        update_post_meta ( $post_id, '_covid_chart_Display', $chart_display );
        update_post_meta ( $post_id, '_covid_chart_Country', $chart_country );
        update_post_meta ( $post_id, '_covid_confirm_color', $confirm_color );
        update_post_meta ( $post_id, '_covid_rec_color', $recov_color );
        update_post_meta ( $post_id, '_covid_death_color', $death_color );
    }



    public function admin_enqueue_scripts(){
        
    }

    public function setup_admin_page() {
        // $admin = new \Sandy\Admin_Settings();
        // $admin->set_page_id(self::PAGE_ID)
        //     ->set_capability('edit_posts')
        //     ->set_page_title(esc_html__('COVID-19 Data', 'protech-covid19'))
        //     ->set_menu_title(esc_html__('COVID-19 Data', 'protech-covid19'))
        //     ->set_setting_key(Plugin::SETTING_KEY)
        //     ->set_icon('dashicons-chart-pie');


    //setup countries option
    if(get_option( 'pthemes_covid19_countries' ) === FALSE):
        $countries = Remote::get('https://covid19.mathdro.id/api/countries');
        if (!is_wp_error($countries)) {
            update_option('pthemes_covid19_countries', $countries);
        }
    endif;

    }


    // Add the custom columns to the covid shortcode post type:

public function set_custom_edit_covid_shortcode_columns($columns) {

    $newCol = array();
    unset($columns['author']);

    foreach($columns as $key=>$value){
        if($key == 'date'){
            $newCol['shortcode'] = __( 'Shortcode', 'protech-covid19' );
        }
        $newCol[$key] = $value;
    }

    return $newCol;
}

// Add the data to the custom columns for the covid shortcode post type:
public function custom_covid_shortcode_column( $column, $post_id ) {
    switch ( $column ) {
        case 'shortcode' :
        $shortcode = '[covid19charts cid="'.$post_id.'"]';
        echo '<span style="background: #f1f1f1;
        border: 1px solid #ded8d8;
        padding: 10px 5px;
        margin: 5px 0;
        display: block;
        width: 200px;">'.$shortcode.'</span>';
            break;
    }
}



}
<?php

namespace PThemes_Covid19;
class Plugin{
    
    const SETTING_KEY = "protech_covid19";
    const TEXTDOMAIN = 'protech-covid19';
    const VERSION = '1.0.0';



     /**
     * @var mixed
     */
    static $instance = null;


    public function __construct()
    {
        self::$instance = new \stdClass();
        self::$instance->admin = new admin\Init();
        add_action('wp_enqueue_scripts', [$this,
        'wp_enqueue_scripts',
    ], 500);
        add_action('init', [$this, 'init']);
    }


    public function init(){

    }

    public function wp_enqueue_scripts() {
        wp_register_script('pthemes-covid19-js', esc_url( plugins_url( 'public/js/bundle.js', __FILE__ ) )  , '', self::VERSION, true);
    }
}
<?php

namespace Sandy;

if(!class_exists("Sandy\Admin-settings")){
    class Admin_Settings{

         /**
         * @var mixed
         */
        public $page = null;

        /**
         * @var string
         */
        private $capability = "manage_options";

        /**
         * @var string
         */
        private $menu_icon = "";

        /**
         * @var mixed
         */
        private $menu_parent = false;

        /**
         * @var mixed
         */
        private $menu_position = null;

        /**
         * @var mixed
         */
        private $menu_title;

        /**
         * @var mixed
         */
        private $page_id = "sandy-settings";

        /**
         * @var mixed
         */
        private $page_title = "Settings";

        /**
         * @var string
         */
        private $setting_key = "sandy_setting";

        /**
         * @var mixed
         */
        private $tabs = null;

        /**
         * @param $page_id
         * @return mixed
         */
        public function __construct($page_id = false, $setting_key = false, $page_title = false, $menu_title = false) {
            add_action('admin_menu', [$this, 'admin_menu'], 20);
            add_action('admin_init', [$this, 'register_settings_fields']);
            if ($page_id !== false) {
                $this->set_page_id($page_id);
            }
            if ($page_title !== false) {
                $this->set_page_title($page_title);
            }
            if ($menu_title !== false) {
                $this->set_menu_title($menu_title);
            }
            if ($setting_key !== false) {
                $this->set_setting_key($setting_key);
            }
            add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);

            return $this;
        }

        
        public function admin_menu(){
            $this->page = add_menu_page(
                $this->page_title,
                $this->menu_title,
                $this->capability,
                $this->page_id,
                [$this, 'display_settings_page'],
                $this->menu_icon,
                $this->menu_position
            );
        }



        public function display_settings_page(){
            
        }

        public function register_settings_fields(){

        }


        public function admin_enqueue_scripts(){

        }


        /**
         * @param $capability
         * @return mixed
         */
        public function set_capability($capability) {
            $this->capability = $capability;
            return $this;
        }

        /**
         * @param $icon
         * @return mixed
         */
        public function set_icon($icon) {
            $this->menu_icon = $icon;
            return $this;
        }

        /**
         * @param $parent
         * @return mixed
         */
        public function set_menu_parent($parent) {
            $this->menu_parent = $parent;
            return $this;
        }

        /**
         * @param $position
         * @return mixed
         */
        public function set_menu_position($position) {
            $this->menu_position = $position;
            return $this;
        }

        /**
         * @param $title
         * @return mixed
         */
        public function set_menu_title($title) {
            $this->menu_title = $title;
            return $this;
        }

        /**
         * @param $id
         * @return mixed
         */
        public function set_page_id($page_id) {
            $this->page_id = $page_id;
            return $this;
        }

        /**
         * @param $title
         * @return mixed
         */
        public function set_page_title($title) {
            $this->page_title = $title;
            return $this;
        }

        /**
         * @param $key
         * @return mixed
         */
        public function set_setting_key($key) {
            $this->setting_key = $key;
            return $this;
        }
         

    }
}

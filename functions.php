<?php
class WPhelpers {
    protected static $instance = null;
    private $welcomePanelContent;
    private $loginLogo='';

    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function _remove_dashboard_widgets() {
        global $wp_meta_boxes;
        // Removing some dashboard mess
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    } 


    function _welcome_panel() {
        echo '<div class="welcome-panel-content">'.$this->welcomePanelContent.'</div>';
    }


    function welcomePanel($content) {
        $this->welcomePanelContent = $content; 
        remove_action('welcome_panel','wp_welcome_panel');
        add_action('welcome_panel', array($this, '_welcome_panel'));
    }

    function removeDashboardWidgets() {
        add_action('wp_dashboard_setup', array($this, '_remove_dashboard_widgets'));
    }

    function _changeLoginLogo() {
    ?>
        <style type="text/css">
            .login h1 a {
                background-image: url(<?php echo $this->loginLogo; ?>);
                background-size: auto;
                width:auto;
                padding-bottom: 30px;
            }
        </style>
    <?php 
    }

    function changeLoginLogo($logo) {
        $this->loginLogo = $logo;
        add_action( 'login_enqueue_scripts', array($this, '_changeLoginLogo'));
    }



}
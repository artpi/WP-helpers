<?php
class WPhelpers {
    protected static $instance = null;
    private $welcomePanelContent;
    private $loginLogo='';
    private $adminFooter='';

    /**
     * Singleton pattern. 
     * @return self
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Remove unnecessary dashboard widgets in Admin panel
     */
    public function removeDashboardWidgets() {
        add_action('wp_dashboard_setup', array($this, '_remove_dashboard_widgets'));
    }
    function _remove_dashboard_widgets() {
        global $wp_meta_boxes;
        // Removing some dashboard mess
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    } 


    /**
     * Add welcome panel to admin panel - used mostly for documentation.
     * @param  [HTML] $content
     */
    function welcomePanel($content) {
        $this->welcomePanelContent = $content; 
        remove_action('welcome_panel','wp_welcome_panel');
        add_action('welcome_panel', array($this, '_welcome_panel'));
    }
    function _welcome_panel() {
        echo '<div class="welcome-panel-content">'.$this->welcomePanelContent.'</div>';
    }


    /**
     * Change logo in login site
     * @param  [url] $logo
     */
    function changeLoginLogo($logo) {
        $this->loginLogo = $logo;
        add_action( 'login_enqueue_scripts', array($this, '_changeLoginLogo'));
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


    /**
     * Change footer in admin panel
     * @param  [type] $footer [description]
     */
    function adminChangeFooter($footer) {
        $this->adminFooter = $footer;
        add_filter('admin_footer_text', array($this, '_adminChangeFooter'));
    }
    function _adminChangeFooter () {
        echo $this->adminFooter;
    } 

    

}
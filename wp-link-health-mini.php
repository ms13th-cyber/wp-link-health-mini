<?php
/**
 * Plugin Name: WP Link Health Mini
 * Description: 軽量なリンク死活監視プラグイン。外部リンクの404/403/timeoutをチェックします。
 * Version: 1.0.2
 * Tested up to: 7.0.0
 * Requires PHP: 8.3.23
 * Author: masato shibuya(Image-box Co., Ltd.)
 * Text Domain: wp-link-health-mini
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WP_Link_Health_Mini {

    private static $instance = null;

    public static function get_instance() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {

        // 定数定義
        $this->define_constants();

        // ファイル読み込み
        $this->includes();

        // 管理画面メニュー
        add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );

        // プラグイン有効化時
        register_activation_hook( __FILE__, [ 'WP_Link_Health_Mini_Cron', 'activate' ] );

        // プラグイン停止時
        register_deactivation_hook( __FILE__, [ 'WP_Link_Health_Mini_Cron', 'deactivate' ] );
    }

    private function define_constants() {
        define( 'WLHM_PATH', plugin_dir_path( __FILE__ ) );
        define( 'WLHM_URL', plugin_dir_url( __FILE__ ) );
        define( 'WLHM_VERSION', '1.0.0' );
    }

    private function includes() {
        require_once WLHM_PATH . 'includes/class-admin-page.php';
        require_once WLHM_PATH . 'includes/class-cron-handler.php';
        require_once WLHM_PATH . 'includes/class-link-checker.php';
        require_once WLHM_PATH . 'includes/class-results-table.php';
        require_once WLHM_PATH . 'includes/helpers.php';
    }

    public function register_admin_menu() {
        add_menu_page(
            'Link Health',
            'Link Health',
            'manage_options',
            'wp-link-health-mini',
            [ 'WP_Link_Health_Mini_Admin_Page', 'render_settings_page' ],
            'dashicons-admin-links'
        );

        add_submenu_page(
            'wp-link-health-mini',
            'チェック結果',
            'チェック結果',
            'manage_options',
            'wp-link-health-mini-results',
            [ 'WP_Link_Health_Mini_Admin_Page', 'render_results_page' ]
        );
    }
}

WP_Link_Health_Mini::get_instance();


require_once __DIR__ . '/plugin-update-checker/plugin-update-checker.php';

$updateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
    'https://github.com/ms13th-cyber/wp-link-health-mini/',
    __FILE__,
    'wp-link-health-mini'
);

$updateChecker->setBranch('main');
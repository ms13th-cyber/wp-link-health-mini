<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WP_Link_Health_Mini_Admin_Page {

    const OPTION_NAME = 'wlhm_settings';

    /**
     * 設定ページの登録
     */
    public static function init() {
        add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
    }

    /**
     * 設定の登録
     */
    public static function register_settings() {

        register_setting(
            'wlhm_settings_group',
            self::OPTION_NAME,
            [
                'type' => 'array',
                'sanitize_callback' => [ __CLASS__, 'sanitize_settings' ],
                'default' => [
                    'frequency' => 'daily',
                    'target'    => 'external',
                    'notify'    => 'admin_bar',
                    'timeout'   => 3,
                ]
            ]
        );
    }

    /**
     * サニタイズ
     */
    public static function sanitize_settings( $input ) {

        $output = [];

        // チェック頻度
        $allowed_frequency = [ 'daily', 'weekly', 'manual' ];
        $output['frequency'] = in_array( $input['frequency'], $allowed_frequency, true )
            ? $input['frequency']
            : 'daily';

        // チェック対象
        $allowed_target = [ 'external', 'all' ];
        $output['target'] = in_array( $input['target'], $allowed_target, true )
            ? $input['target']
            : 'external';

        // 通知方法
        $allowed_notify = [ 'admin_bar', 'dashboard', 'none' ];
        $output['notify'] = in_array( $input['notify'], $allowed_notify, true )
            ? $input['notify']
            : 'admin_bar';

        // タイムアウト
        $timeout = intval( $input['timeout'] );
        $output['timeout'] = ( $timeout >= 1 && $timeout <= 10 ) ? $timeout : 3;

        return $output;
    }

    /**
     * 設定画面の描画
     */
    public static function render_settings_page() {

        $settings = get_option( self::OPTION_NAME );

        include WLHM_PATH . 'admin/views/settings-page.php';
    }

	/**
	 * 手動チェック処理
	 */
	public static function maybe_run_manual_check() {

		if ( ! isset($_GET['action']) || $_GET['action'] !== 'wlhm_run_check' ) {
			return;
		}

		// 権限チェック
		if ( ! current_user_can('manage_options') ) {
			return;
		}

		// チェック実行
		WP_Link_Health_Mini_Link_Checker::run_check();

		// 完了メッセージ
		add_action('admin_notices', function() {
			echo '<div class="notice notice-success"><p>リンクチェックが完了しました。</p></div>';
		});
	}

	public static function render_results_page() {

		self::maybe_run_manual_check();

		include WLHM_PATH . 'admin/views/results-page.php';
	}
}

WP_Link_Health_Mini_Admin_Page::init();


/**
 * ダッシュボード通知
 */
add_action( 'admin_notices', function() {

    // 設定取得
    $settings = get_option( 'wlhm_settings', [] );
    $notify   = $settings['notify'] ?? 'admin_bar';

    // 通知設定が dashboard 以外なら表示しない
    if ( $notify !== 'dashboard' ) {
        return;
    }

    // 結果取得
    $results = WP_Link_Health_Mini_Link_Checker::get_results();
    if ( empty( $results ) ) {
        return;
    }

    // エラー系だけ抽出
    $errors = array_filter( $results, function( $item ) {
        if ( $item['status'] === 'timeout' ) return true;
        if ( intval( $item['status'] ) >= 400 ) return true;
        return false;
    });

    if ( empty( $errors ) ) {
        return;
    }

    $count = count( $errors );
    $url   = admin_url( 'admin.php?page=wp-link-health-mini-results' );

    ?>
    <div class="notice notice-error">
        <p>
            <strong>WP Link Health Mini:</strong>
            <?php echo esc_html( $count ); ?> 件のリンクエラーが見つかりました。
            <a href="<?php echo esc_url( $url ); ?>">チェック結果を見る</a>
        </p>
    </div>
    <?php
});


/**
 * 管理バー通知（バッジ表示）
 */
add_action( 'admin_bar_menu', function( $wp_admin_bar ) {

    // 設定取得
    $settings = get_option( 'wlhm_settings', [] );
    $notify   = $settings['notify'] ?? 'admin_bar';

    // 通知設定が admin_bar 以外なら表示しない
    if ( $notify !== 'admin_bar' ) {
        return;
    }

    // 結果取得
    $results = WP_Link_Health_Mini_Link_Checker::get_results();
    if ( empty( $results ) ) {
        return;
    }

    // エラー系だけ抽出
    $errors = array_filter( $results, function( $item ) {
        if ( $item['status'] === 'timeout' ) return true;
        if ( intval( $item['status'] ) >= 400 ) return true;
        return false;
    });

    if ( empty( $errors ) ) {
        return;
    }

    $count = count( $errors );
    $url   = admin_url( 'admin.php?page=wp-link-health-mini-results' );

    // 管理バーに追加
    $wp_admin_bar->add_node([
        'id'    => 'wlhm_admin_bar',
        'title' => sprintf(
            'Link Health <span style="background:#d63638;color:#fff;padding:2px 6px;border-radius:10px;font-size:11px;">%d</span>',
            $count
        ),
        'href'  => $url,
        'meta'  => [
            'title' => 'リンクエラーを確認',
        ]
    ]);

}, 100 );

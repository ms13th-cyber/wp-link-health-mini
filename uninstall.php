<?php
/**
 * uninstall.php
 * プラグイン削除時のクリーンアップ
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// 削除するオプション名
$settings_option = 'wlhm_settings';
$results_option  = 'wlhm_results';

// 設定削除
delete_option( $settings_option );

// 結果データ削除
delete_option( $results_option );

// 念のため Cron イベントも削除
wp_clear_scheduled_hook( 'wlhm_cron_event' );

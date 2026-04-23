<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WP_Link_Health_Mini_Cron {

    const CRON_HOOK = 'wlhm_cron_event';

    /**
     * プラグイン有効化時
     */
    public static function activate() {

        $settings = get_option( 'wlhm_settings', [] );
        $frequency = $settings['frequency'] ?? 'daily';

        // 手動以外なら Cron 登録
        if ( $frequency !== 'manual' ) {
            self::schedule_event( $frequency );
        }
    }

    /**
     * プラグイン停止時
     */
    public static function deactivate() {
        wp_clear_scheduled_hook( self::CRON_HOOK );
    }

    /**
     * Cron イベント登録
     */
    public static function schedule_event( $frequency ) {

        // 既存イベント削除
        wp_clear_scheduled_hook( self::CRON_HOOK );

        if ( $frequency === 'daily' ) {
            wp_schedule_event( time() + 60, 'daily', self::CRON_HOOK );
        }
        elseif ( $frequency === 'weekly' ) {
            wp_schedule_event( time() + 60, 'weekly', self::CRON_HOOK );
        }
        // manual の場合は登録しない
    }

    /**
     * Cron 実行時の処理
     */
    public static function run_cron() {

        // リンクチェック本体を呼ぶ
        if ( class_exists( 'WP_Link_Health_Mini_Link_Checker' ) ) {
            WP_Link_Health_Mini_Link_Checker::run_check();
        }
    }
}

// Cron フックに処理を紐づける
add_action( WP_Link_Health_Mini_Cron::CRON_HOOK, [ 'WP_Link_Health_Mini_Cron', 'run_cron' ] );

// 設定変更時に Cron を再登録
add_action( 'update_option_wlhm_settings', function( $old, $new ) {
    if ( isset( $new['frequency'] ) ) {
        WP_Link_Health_Mini_Cron::schedule_event( $new['frequency'] );
    }
}, 10, 2 );

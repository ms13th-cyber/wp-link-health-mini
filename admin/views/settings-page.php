<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap">
    <h1>WP Link Health Mini – 設定</h1>

    <form method="post" action="options.php">
        <?php
            settings_fields( 'wlhm_settings_group' );
            $settings = get_option( 'wlhm_settings' );
        ?>

        <table class="form-table" role="presentation">

            <!-- チェック頻度 -->
            <tr>
                <th scope="row"><label for="wlhm_frequency">チェック頻度</label></th>
                <td>
                    <select name="wlhm_settings[frequency]" id="wlhm_frequency">
                        <option value="daily"  <?php selected( $settings['frequency'], 'daily' ); ?>>毎日（推奨）</option>
                        <option value="weekly" <?php selected( $settings['frequency'], 'weekly' ); ?>>週1回</option>
                        <option value="manual" <?php selected( $settings['frequency'], 'manual' ); ?>>手動のみ</option>
                    </select>
                    <p class="description">Cron による自動チェックの頻度を選択します。</p>
                </td>
            </tr>

            <!-- チェック対象 -->
            <tr>
                <th scope="row"><label for="wlhm_target">チェック対象</label></th>
                <td>
                    <select name="wlhm_settings[target]" id="wlhm_target">
                        <option value="external" <?php selected( $settings['target'], 'external' ); ?>>外部リンクのみ（推奨）</option>
                        <option value="all"      <?php selected( $settings['target'], 'all' ); ?>>内部リンクも含める</option>
                    </select>
                    <p class="description">内部リンクを含めると負荷が増える場合があります。</p>
                </td>
            </tr>

            <!-- 通知方法 -->
            <tr>
                <th scope="row"><label for="wlhm_notify">通知方法</label></th>
                <td>
                    <select name="wlhm_settings[notify]" id="wlhm_notify">
                        <option value="admin_bar" <?php selected( $settings['notify'], 'admin_bar' ); ?>>管理バーにバッジ表示</option>
                        <option value="dashboard" <?php selected( $settings['notify'], 'dashboard' ); ?>>ダッシュボードに警告表示</option>
                        <option value="none"      <?php selected( $settings['notify'], 'none' ); ?>>通知しない</option>
                    </select>
                    <p class="description">リンク切れが見つかった際の通知方法を選択します。</p>
                </td>
            </tr>

            <!-- タイムアウト秒数 -->
            <tr>
                <th scope="row"><label for="wlhm_timeout">タイムアウト秒数</label></th>
                <td>
                    <select name="wlhm_settings[timeout]" id="wlhm_timeout">
                        <option value="1" <?php selected( $settings['timeout'], 1 ); ?>>1秒</option>
                        <option value="3" <?php selected( $settings['timeout'], 3 ); ?>>3秒（推奨）</option>
                        <option value="5" <?php selected( $settings['timeout'], 5 ); ?>>5秒</option>
                    </select>
                    <p class="description">レスポンスが遅いサイトではタイムアウトを長めに設定してください。</p>
                </td>
            </tr>

        </table>

        <?php submit_button(); ?>
    </form>
</div>

<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap">
    <h1>リンクチェック結果</h1>

    <p>
        最新のリンクチェック結果を表示します。
        自動チェックは設定画面で頻度を変更できます。
    </p>

    <!-- 手動チェックボタン -->
	<p class="wlhm-run-check">
		<a href="<?php echo admin_url('admin.php?page=wp-link-health-mini-results&action=wlhm_run_check'); ?>"
		class="button button-primary">
			今すぐチェック
		</a>
	</p>

    <?php
        $table = new WP_Link_Health_Mini_Results_Table();
        $table->prepare_items();
    ?>

    <form method="get">
        <input type="hidden" name="page" value="wp-link-health-mini-results">
        <?php $table->display(); ?>
    </form>
</div>

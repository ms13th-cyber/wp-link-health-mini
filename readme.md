# WP Link Health Mini

A lightweight, on-demand internal link checker for WordPress. Designed to minimize server load by performing checks only when requested, avoiding the heavy background processes typical of other link-checking plugins.

[日本語の解説は英語の後にあります]

---

## Key Features

- **On-Demand Checking**: Unlike other plugins that run heavy background tasks via WP-Cron, this plugin only works when you click the "Check" button.
- **Ajax-Powered Stepping**: Processes links asynchronously using Ajax. This prevents server timeouts and ensures stability even on shared hosting environments with strict resource limits.
- **Minimal Footprint**: No large custom database tables. It scans existing post data temporarily, keeping your database clean and fast.
- **Real-time Feedback**: Includes a visual progress bar and direct edit links for any discovered 404 errors.

## Installation

1. Upload the `wp-link-health-mini` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to **Tools > Link Health Mini** to start your first check.

## Why "Mini"?

This plugin was developed for users who need a reliable link checker but want to avoid the performance degradation often caused by continuous background monitoring. It’s perfect for maintaining site health without sacrificing speed.

---

## 主な機能（日本語）

サーバー負荷を極限まで抑えた、オンデマンド型の内部リンク切れチェッカーです。

- **オンデマンド実行**: 他のプラグインのようにバックグラウンドで常時動作（WP-Cron）するのではなく、実行ボタンを押した時のみ動作します。
- **Ajaxによる分散処理**: 非同期通信（Ajax）を利用して少しずつ処理を進めるため、リソース制限の厳しい共用サーバーでもタイムアウトせずに安定して動作します。
- **軽量設計**: 独自の巨大なデータベーステーブルを作成しません。一時的に投稿データをスキャンするだけのクリーンな設計です。
- **リアルタイム反映**: 404エラーが見つかった際、その場で修正用リンクが表示され、進捗もプログレスバーで一目で確認できます。

## インストール

1. `wp-link-health-mini` フォルダを `/wp-content/plugins/` にアップロードします。
2. 管理画面の「プラグイン」から有効化してください。
3. 「ツール」 > 「Link Health Mini」から使用できます。
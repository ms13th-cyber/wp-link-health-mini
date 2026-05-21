=== WP Link Health Mini ===
Contributors: masato shibuya(Image-box Co., Ltd.)
Tags: link check, broken link, broken links, seo, maintenance, japanese
Requires at least: 5.0
Tested up to: 7.0.0
Requires PHP: 8.0
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight broken link checker for WordPress.
Automatically detects 404 / 403 / timeout errors in external links with minimal server load.

外部リンクの 404・403・タイムアウトを軽量にチェックする、ミニマルなリンクヘルス監視プラグインです。
WordPress サイトのリンク切れを自動検出し、管理バーやダッシュボードで通知します。

== Description ==

WP Link Health Mini は、サイト内の外部リンクを定期的にチェックし、
リンク切れ（404 / 403 / timeout）を自動検出する軽量プラグインです。

特徴は「とにかく軽い」こと。
余計な機能を排除し、必要最小限の処理だけを行うことで、
大規模サイトでも負荷をかけずに運用できます。

また、YouTube / Vimeo / Google Maps / SNS などの iframe・embed URL は
誤検知を防ぐため自動的に除外されます。

= 主な機能 =
* 外部リンクの死活監視 – 投稿本文から URL を抽出し、HTTP ステータスをチェック
* エラーのみ保存 – 404 / 403 / timeout のみ結果に記録（高速・軽量）
* 管理バー通知 – エラー件数を赤いバッジで表示
* ダッシュボード通知 – エラー発生時にアラートを表示
* 手動チェックボタン – 「今すぐチェック」で即時スキャン
* Cron による自動チェック – 毎日 / 週1 / 手動から選択可能
* iframe / embed / QueryMonitorData の URL を自動除外（誤検知ゼロ）
* Mini 設計 – 不要な機能を排除し、コードは極めて軽量

== Installation ==

1. `wp-link-health-mini` フォルダを `/wp-content/plugins/` にアップロードします。
2. WordPress の「プラグイン」メニューから有効化します。
3. 管理画面メニュー「Link Health」から設定を行ってください。

== Folder Structure ==

wp-link-health-mini/
- wp-link-health-mini.php
- includes/
  - class-link-checker.php
  - class-admin-page.php
  - class-cron-handler.php
  - class-results-table.php
  - helpers.php
- admin/
  - views/
    - settings-page.php
    - results-page.php
  - css/
    - admin-style.css
- assets/
  - icon.svg
- uninstall.php

== Frequently Asked Questions ==

= 内部リンクもチェックできますか？ =
設定画面で「内部リンクを含める」を選択すると可能です。ただし負荷が増える場合があります。

= チェック結果が保存されないのですが？ =
WP Link Health Mini は「エラーのみ保存」仕様です。正常なリンクは保存されません。

= 大規模サイトでも使えますか？ =
はい。1回のチェックで最大200件までに制限されており、負荷がかからない設計です。

== Screenshots ==

1. チェック結果一覧（WP_List_Table によるエラー表示）
2. 管理バーのエラーバッジ通知（赤いバッジで件数を表示）
3. 設定画面（チェック頻度・対象リンク・通知設定など）

== Changelog ==

= 1.0.1 =
* Wordpress7.0.0での動作確認

= 1.0.1 =
* テキスト修正

= 1.0 =
* 初回リリース
* 外部リンクの死活監視機能
* 管理バー通知・ダッシュボード通知
* 手動チェックボタン
* Cron 自動チェック
* iframe / embed / QueryMonitorData の URL 自動除外

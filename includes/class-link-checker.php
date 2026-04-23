<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WP_Link_Health_Mini_Link_Checker {

    const OPTION_RESULTS = 'wlhm_results';

    /**
     * メイン実行（最終軽量版）
     */
    public static function run_check() {

        $settings = get_option( 'wlhm_settings', [] );
        $target   = $settings['target'] ?? 'external';
        $timeout  = intval( $settings['timeout'] ?? 3 );

        // 投稿からリンク抽出（高速版）
        $links = self::extract_links_from_posts( $target );
        if ( empty( $links ) ) {
            update_option( self::OPTION_RESULTS, [] );
            return;
        }

        $results = [];
        $checked_cache = [];   // 同じURLは1回だけチェック
        $max_checks = 200;     // Cron負荷対策
        $checked_count = 0;

        foreach ( $links as $post_id => $urls ) {
            foreach ( $urls as $url ) {

                if ( $checked_count >= $max_checks ) {
                    break 2; // 2重ループ脱出
                }

                // キャッシュ利用
                if ( isset( $checked_cache[$url] ) ) {
                    $status = $checked_cache[$url];
                } else {
                    $status = self::check_url_status( $url, $timeout );
                    $checked_cache[$url] = $status;
                }

                $checked_count++;

                // エラーのみ保存（Mini仕様）
                if ( $status === 'timeout' || intval($status) >= 400 ) {
                    $results[] = [
                        'post_id' => $post_id,
                        'url'     => $url,
                        'status'  => $status,
                        'checked' => current_time( 'mysql' ),
                    ];
                }
            }
        }

        update_option( self::OPTION_RESULTS, $results );
    }

    /**
     * 投稿本文からリンクを抽出（QueryMonitorData + iframe 完全除外版）
     */
    private static function extract_links_from_posts( $target ) {

        $args = [
            'post_type'        => 'any',
            'post_status'      => 'publish',
            'posts_per_page'   => -1,
            'fields'           => 'ids',
            'suppress_filters' => true,
        ];

        $post_ids = get_posts( $args );
        $results  = [];

        foreach ( $post_ids as $post_id ) {

            $content = get_post_field( 'post_content', $post_id );
            if ( empty( $content ) ) continue;

            /**
             * ① Query Monitor のデバッグデータを完全削除
             *    <script id="qm-js"> ... </script>
             */
            $content_clean = preg_replace('/<script[^>]*id="qm-js"[^>]*>.*?<\/script>/is', '', $content);

            /**
             * ② JSON の "QueryMonitorData": {...} を削除
             */
            $content_clean = preg_replace('/"QueryMonitorData"\s*:\s*\{.*?\}/is', '', $content_clean);

            /**
             * ③ iframe の src を抽出（改行・シングル/ダブルクォート対応）
             */
            $iframe_urls = [];
            if ( preg_match_all('/<iframe[^>]+src=[\'"]([^\'"]+)[\'"]/i', $content_clean, $matches ) ) {
                $iframe_urls = $matches[1];
            }

            /**
             * ④ URL 抽出（QueryMonitorData 除去後の本文から）
             */
            $urls = wp_extract_urls( $content_clean );
            if ( empty( $urls ) ) continue;

            $urls = array_unique( $urls );

            /**
             * ⑤ iframe の URL を完全除外
             */
            if ( ! empty( $iframe_urls ) ) {
                $urls = array_diff( $urls, $iframe_urls );
            }

            /**
             * ⑥ iframe のドメインと同じ URL をすべて除外（最強）
             */
            $iframe_hosts = [];
            foreach ( $iframe_urls as $iframe_url ) {
                $host = wp_parse_url( $iframe_url, PHP_URL_HOST );
                if ( $host ) {
                    $iframe_hosts[] = $host;
                }
            }

            if ( ! empty( $iframe_hosts ) ) {
                $urls = array_filter( $urls, function( $url ) use ( $iframe_hosts ) {
                    $host = wp_parse_url( $url, PHP_URL_HOST );
                    if ( in_array( $host, $iframe_hosts, true ) ) {
                        return false;
                    }
                    return true;
                });
            }

            /**
             * ⑦ embed サービスの URL を除外
             */
            $exclude_patterns = [
                'youtube.com',
                'youtu.be',
                'vimeo.com',
                'maps.google.com',
                'google.com/maps',
                'open.spotify.com',
                'twitter.com',
                'x.com',
                'instagram.com',
                'tiktok.com',
                'facebook.com',
                'soundcloud.com',
                'embed.',
            ];

            $urls = array_filter( $urls, function( $url ) use ( $exclude_patterns ) {
                foreach ( $exclude_patterns as $pattern ) {
                    if ( stripos( $url, $pattern ) !== false ) {
                        return false;
                    }
                }
                return true;
            });

            /**
             * ⑧ 外部リンクのみ
             */
            if ( $target === 'external' ) {
                $urls = array_filter( $urls, function( $url ) {
                    return ! WP_Link_Health_Mini_Link_Checker::is_internal_url( $url );
                });
            }

            if ( ! empty( $urls ) ) {
                $results[$post_id] = $urls;
            }
        }

        return $results;
    }

    /**
     * URLが内部リンクか判定（精度高め）
     */
    private static function is_internal_url( $url ) {
        return wp_parse_url($url, PHP_URL_HOST) === wp_parse_url(home_url(), PHP_URL_HOST);
    }

    /**
     * URLのステータスチェック（HEAD → GET フォールバック）
     */
    private static function check_url_status( $url, $timeout ) {

        $args = [
            'timeout'     => $timeout,
            'redirection' => 3,
        ];

        $response = wp_remote_head( $url, $args );

        if ( is_wp_error( $response ) ) {
            $response = wp_remote_get( $url, $args );
        }

        if ( is_wp_error( $response ) ) {
            return 'timeout';
        }

        $code = wp_remote_retrieve_response_code( $response );
        return $code ? $code : 'unknown';
    }

    /**
     * 結果取得（一覧ページ用）
     */
    public static function get_results() {
        return get_option( self::OPTION_RESULTS, [] );
    }
}

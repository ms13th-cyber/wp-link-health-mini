<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class WP_Link_Health_Mini_Results_Table extends WP_List_Table {

    private $items_data = [];

    public function __construct() {
        parent::__construct([
            'singular' => 'link_result',
            'plural'   => 'link_results',
            'ajax'     => false,
        ]);

        $this->items_data = WP_Link_Health_Mini_Link_Checker::get_results();
    }

    /**
     * カラム定義
     */
    public function get_columns() {
        return [
            'post_id' => '投稿',
            'url'     => 'URL',
            'status'  => 'ステータス',
            'checked' => 'チェック日時',
        ];
    }

    /**
     * ソート可能カラム
     */
    protected function get_sortable_columns() {
        return [
            'status'  => ['status', false],
            'checked' => ['checked', false],
        ];
    }

    /**
     * デフォルトカラム表示
     */
    protected function column_default( $item, $column_name ) {

        switch ( $column_name ) {

            case 'post_id':
                $title = get_the_title( $item['post_id'] );
                $link  = get_edit_post_link( $item['post_id'] );
                return sprintf(
                    '<a href="%s">%s</a> (ID: %d)',
                    esc_url( $link ),
                    esc_html( $title ),
                    $item['post_id']
                );

            case 'url':
                return sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    esc_url( $item['url'] ),
                    esc_html( $item['url'] )
                );

            case 'status':
                return $this->format_status( $item['status'] );

            case 'checked':
                return esc_html( $item['checked'] );
        }

        return '';
    }

    /**
     * ステータスの色分け
     */
    private function format_status( $status ) {

        if ( $status === 'timeout' ) {
            return '<span style="color:#d63638;font-weight:bold;">Timeout</span>';
        }

        if ( intval( $status ) >= 400 ) {
            return sprintf(
                '<span style="color:#d63638;font-weight:bold;">%s</span>',
                esc_html( $status )
            );
        }

        if ( intval( $status ) >= 300 ) {
            return sprintf(
                '<span style="color:#dba617;font-weight:bold;">%s</span>',
                esc_html( $status )
            );
        }

        return sprintf(
            '<span style="color:#46b450;font-weight:bold;">%s</span>',
            esc_html( $status )
        );
    }

    /**
     * テーブルデータ準備
     */
    public function prepare_items() {

        $columns  = $this->get_columns();
        $hidden   = [];
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = [ $columns, $hidden, $sortable ];

        $data = $this->items_data;

        // ソート処理
        $orderby = $_GET['orderby'] ?? 'checked';
        $order   = $_GET['order']   ?? 'desc';

        usort( $data, function( $a, $b ) use ( $orderby, $order ) {

            $valueA = $a[$orderby] ?? '';
            $valueB = $b[$orderby] ?? '';

            if ( $orderby === 'checked' ) {
                $valueA = strtotime( $valueA );
                $valueB = strtotime( $valueB );
            }

            if ( $valueA == $valueB ) return 0;

            if ( $order === 'asc' ) {
                return ( $valueA < $valueB ) ? -1 : 1;
            } else {
                return ( $valueA > $valueB ) ? -1 : 1;
            }
        });

        $this->items = $data;
    }
}

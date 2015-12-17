<?php

class TradeTest extends WP_UnitTestCase {
    const USER_NAME = 'mytest';
    const USER_PASS = 'pass';

    /**
	 * テスト開始前の下準備を行います。
	 */
    function setUp(){
        wp_create_user(self::USER_NAME, self::USER_PASS);
        wp_set_current_user(get_user_by('login', self::USER_NAME)->ID);
    }

     /**
     * 取引を完了させるテスト
     */
     function testFinishTrade(){
        $case1_ISBN = 'TRADETESTISBN';

        $post1 = array(
            'exhibitor_id' => get_user_by('login', self::USER_NAME)->ID,
            'item_name' => 'FinishTradetest',
            'item_description' => '取引完了テスト',
            'ISBN' => $case1_ISBN,
            'author' => '取引完了テスト著者'
        );
        // 2冊出品する
        exhibit($post1);
        exhibit($post1);

        $id1 = get_post_by_ISBN($case1_ISBN)->ID;
        $this->assertEquals(2, count_books($id1)); //冊数は2冊

        $this->assertEquals(true, finish_trade($id1)); //取引一件完了
        $this->assertEquals(1, count_books($id1)); //冊数は1冊

        $this->assertEquals(true, finish_trade($id1)); //もう一件完了
        $this->assertEquals(0, count_books($id1)); //冊数は0冊

        $this->assertEquals(false, finish_trade($id1)); //0冊なので完了できない
        $this->assertEquals(0, count_books($id1)); //冊数は0のまま
     }

    /**
     * テスト後の処理を行います。
     */
    function tearDown(){
        global $wpdb, $table_prefix;
        // fmt_points テーブルを掃除
        $wpdb->delete($table_prefix . "fmt_points", array("user_id"=>get_user_by('login', self::USER_NAME)->ID));
    }
}

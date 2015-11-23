<?php

class ISBNTest extends WP_UnitTestCase {
    const USER_NAME = 'isbntest';
    const USER_PASS = 'pass';

    /**
	 * テスト開始前の下準備を行います。
     * この処理に記入された処理は、テストケース群実行前に一度だけ呼ばれます。
     * 各テストケースの実行前に一度ずつ実行したい処理には setUp 関数を使ってください。
	 */
    public static function setUpBeforeClass(){
        wp_create_user(self::USER_NAME, self::USER_PASS);
        wp_set_current_user(get_user_by('login', self::USER_NAME)->ID);
    }

     /**
     *  ISBNで本を取得するテスト
     */
     function testGetPostByISBN(){
        $case1_ISBN = 'ISBN123456789'; //存在する値
        $case2_ISBN = '0987654321092'; //存在しない値

        $post1 = array(
            'exhibitor_id' => get_user_by('login', self::USER_NAME)->ID,
            'item_name' => 'ISBNtest',
            'item_description' => 'ISBNテスト正常系',
            'ISBN' => $case1_ISBN,
            'author' => 'ISBN著者'
        );
        $case1_id = exhibit($post1);

        $id1 = get_post_by_ISBN($case1_ISBN)->ID;
        $this->assertEquals($case1_id, $id1); //存在する

        $post2 = get_post_by_ISBN($case2_ISBN);
        $this->assertEquals(true, empty($post2)); //存在しない
     }

    /**
     * ISBNによる冊数インクリメント処理テスト
     */
     function testGetPostsByISBN(){
        $ISBN = 'MULTIISBN0189'; //重複させる用の値
        $post = array(
            'exhibitor_id' => get_user_by('login', self::USER_NAME)->ID,
            'item_name' => 'ISBNtest',
            'item_description' => 'ISBNテスト冊数複数',
            'ISBN' => $ISBN,
            'author' => 'ISBN著者'
        );
        $id = exhibit($post);
        $second_id = exhibit($post); //2冊目を出品
        $this->assertEquals($id, $second_id); //2冊めのidは1冊めと同じものが返る
        $this->assertEquals(2, count_books($id)); //冊数は2冊
     }

    /**
     * テスト後の処理を行います。
     * この処理に記入された処理は、テストケース群実行後に一度だけ呼ばれます。
     * 各テストケースの実行後に一度ずつ実行したい処理には tearDown 関数を使ってください。
     */
    public static function tearDownAfterClass(){
        global $wpdb, $table_prefix;
        // fmt_points テーブルを掃除
        $wpdb->delete($table_prefix . "fmt_points", array("user_id"=>get_user_by("login", self::USER_NAME)->ID));
    }
}

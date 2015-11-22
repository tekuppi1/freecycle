<?php

class ISBNTest extends WP_UnitTestCase {
    private $user_name = 'isbntest';
    private $user_pass = 'pass';

    private $case1_id, $case3_1_id, $case3_2_id;
    private $case1_ISBN = '1234567890123'; //存在する値
    private $case2_ISBN = '0987654321092'; //存在しない値

    /**
	 * テスト開始前の下準備を行います
	 */
     function setUp(){
        wp_create_user($this->user_name, $this->user_pass);
        wp_set_current_user(get_user_by('login', $this->user_name)->ID);
        $post1 = array(
                    'exhibitor_id' => get_user_by('login', $this->user_name)->ID,
                    'item_name' => 'ISBNtest',
                    'item_description' => 'ISBNテスト正常系',
                    'ISBN' => $this->case1_ISBN
                );
        $this->case1_id = exhibit($post1);
     }

     /**
     *  ISBNで本を取得するテスト
     */
     function testGetPostsByISBN(){
        $id1 = get_post_by_ISBN($this->case1_ISBN)->ID;
        $this->assertEquals($this->case1_id, $id1); //存在する

        $post2 = get_post_by_ISBN($this->case2_ISBN);
        $this->assertEquals(true, empty($post2)); //存在しない
     }

    /**
     * テスト開始後の後始末を行います
     */
    function tearDown(){
        global $wpdb, $table_prefix;
        // fmt_points テーブルを掃除
        $wpdb->delete($table_prefix . "fmt_points", array("user_id"=>get_user_by("login", $this->user_name)->ID));
    }
}

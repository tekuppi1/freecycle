<?php

class BookCountTest extends WP_UnitTestCase {
    private $user_name = 'bookcounttest';
    private $pass = 'pass';
    private $postID = '';
    private $INITIAL_NUM = 1;
    private $INCREMENT_NUM = 1;
    private $DECREMENT_NUM = 1;

    /**
	 * テスト開始前の下準備を行います
	 */
     function setUp(){
         wp_create_user($this->user_name, $this->pass);
         wp_set_current_user(get_user_by('login', $this->user_name)->ID);
         $post = array(
                    'post_title' => 'bookcounttest',
                    'post_content' => '冊数系テスト
                ');
         $this->postID = wp_insert_post($post, true);
        add_post_meta($this->postID, 'book_count', $this->INITIAL_NUM);
     }

     /**
     *  本の冊数を増加させた場合のテスト
     */
     function testIncreceBookCount(){
         $count = count_books($this->postID);
         increace_book_count($this->postID, $this->INCREMENT_NUM);

         $this->assertEquals($count + $this->INCREMENT_NUM, count_books($this->postID));
     }

     /**
     *  本の冊数を減少させた場合のテスト
     */
     function testDecreceBookCount(){
         $count = count_books($this->postID);
         decreace_book_count($this->postID, $this->DECREMENT_NUM);

         $this->assertEquals($count - $this->DECREMENT_NUM, count_books($this->postID));
     }

     /**
     * テスト開始後の後始末を行います
     */
     function tearDown(){
         global $wpdb, $table_prefix;
         //postmetaテーブルを掃除
         $wpdb->delete($table_prefix . 'postmeta', array('post_id' =>$this->postID, 'meta_key'=> 'book_count'));
     }
}

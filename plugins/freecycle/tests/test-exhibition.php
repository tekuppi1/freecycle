<?php

class ExhibitionTest extends WP_UnitTestCase {
	private $exhibitor_name = "exhibit";

	/**
	 * テスト開始前の下準備を行います
	 */
	function setUp(){
		wp_create_user($this->exhibitor_name, ""); // 出品者ユーザを作成
	}

	/**
	 * ちゃんと出品できるかテストします。
	 */
	function testExhibition() {
		$exhibitor_id = get_user_by("login", $this->exhibitor_name)->ID;
		// すべての値をセット済
		$case1 = exhibit(array(
			'exhibitor_id' => $exhibitor_id,
			'item_name' => "情報すべてあり",
			'image_url' => "http://hoge.com/hoge.jpg",
			'department' => "なんとか学部",
			'course' => "なんとか学科",
			'item_status' => 1,
			'ISBN' => "EXHIBITION123",
			'author' => "村上春樹",
			'price' => "1000"
		));
		$this->assertEquals($exhibitor_id, get_post_field("post_author", $case1));
		$this->assertEquals("情報すべてあり", get_post_field("post_title", $case1));
		$this->assertEquals("なんとか学部", get_post_meta($case1, "department", true));
		$this->assertEquals("なんとか学科", get_post_meta($case1, "course", true));
		$this->assertEquals(1, get_post_meta($case1, "item_status", true));
		$this->assertEquals("EXHIBITION123", get_post_meta($case1, "ISBN", true));
		$this->assertEquals("村上春樹", get_post_meta($case1, "author", true));
		$this->assertEquals("1000", get_post_meta($case1, "price", true));

		// Amazon からの取得ができない場合
		$case2 = exhibit(array(
			'exhibitor_id' => $exhibitor_id,
			'item_name' => "追加情報すべてなし",
			'image_url' => "http://hoge.com/hoge.jpg",
			'department' => "なんとか学部",
			'course' => "なんとか学科",
			'item_category' => 0
		));
		$this->assertEquals("", get_post_meta($case2, "ISBN", true));
		$this->assertEquals("", get_post_meta($case2, "author", true));
		$this->assertEquals("", get_post_meta($case2, "price", true));
	}

	/**
	 * テスト開始後の後始末を行います
	 */
	function tearDown(){
		global $wpdb, $table_prefix;
		// fmt_points テーブルを掃除
		$wpdb->delete($table_prefix . "fmt_points", array("user_id"=>get_user_by("login", $this->exhibitor_name)->ID));
	}
}


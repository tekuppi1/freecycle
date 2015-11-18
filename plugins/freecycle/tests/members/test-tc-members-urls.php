<?php

class MembersUrlTest extends WP_UnitTestCase {
	private $user_name = "test";
	private $user_pass = "pass";

	/**
	 * テスト開始前の下準備を行います
	 */
	function setUp(){
		wp_create_user($this->user_name, $this->user_pass); // ユーザを作成
		wp_set_current_user(get_user_by("login", $this->user_name)->ID); //現在のユーザを設定
	}

	/**
	 * get_entry_list_url の単体テスト
	 */
	function test_get_entry_list_url() {
		// ログインユーザに関するちゃんとしたテストが書けてません……。
		// current_user をセットしただけでは、$bp->loggedin_user が更新されないようです。
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


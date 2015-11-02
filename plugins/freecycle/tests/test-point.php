<?php

class PointTest extends WP_UnitTestCase {
	private $initial_point = 5;
	private $exhibitor_name = "exhibitor";
	private $bidder_name = "bidder";

	/**
	 * テスト開始前の下準備を行います
	 */
	function setUp(){
		update_option("register-point", $this->initial_point); //新規登録時のポイントを設定
		wp_create_user($this->exhibitor_name, ""); // 出品者ユーザを作成
		wp_create_user($this->bidder_name, ""); // 落札者ユーザを作成
		wp_set_current_user(get_user_by("login", $this->exhibitor_name)->ID); //現在のユーザを出品者に設定
	}

	/**
	 * 使用可能なポイントが正しいかテストします
	 */
	function testUsablePoint() {
		// 初期ポイントのテスト
		// 初期ポイント = 使用可能ポイント
		$this->assertEquals($this->initial_point, get_usable_point(get_user_by("login", $this->exhibitor_name)->ID));
	}

	/**
	 * テスト開始後の後始末を行います
	 */
	function tearDown(){
		global $wpdb, $table_prefix;
		// fmt_points テーブルを掃除
		$wpdb->delete($table_prefix . "fmt_points", array("user_id"=>get_user_by("login", $this->exhibitor_name)->ID));
		$wpdb->delete($table_prefix . "fmt_points", array("user_id"=>get_user_by("login", $this->bidder_name)->ID));
	}
}


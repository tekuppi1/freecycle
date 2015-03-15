<?php

class PointTest extends WP_UnitTestCase {
	private $initial_point = 5;
	private $exhibitor_name = "exhibitor";
	private $bidder_name = "bidder";

	function setUp(){
		update_option("register-point", $this->initial_point);
		wp_create_user($this->exhibitor_name, "");
		wp_create_user($this->bidder_name, "");
		wp_set_current_user(get_user_by("login", $this->exhibitor_name)->ID);
	}

	function testUsablePoint() {
		$this->assertEquals($this->initial_point, get_usable_point(get_user_by("login", $this->exhibitor_name)->ID));
	}

	function tearDown(){
		wp_delete_user(get_user_by("login", $this->exhibitor_name)->ID);
		wp_delete_user(get_user_by("login", $this->bidder_name)->ID);		
	}
}


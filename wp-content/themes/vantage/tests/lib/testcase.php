<?php

require_once __DIR__ . '/constraints.php';

abstract class APP_UnitTestCase extends WP_UnitTestCase {

	function setUp() {
		parent::setUp();

		do_action( 'appthemes_first_run' );

		$this->catcher = new APP_Mail_Catcher;
	}

	function assertMailSentTo( $expected ) {
		$results = wp_list_pluck( $this->catcher->get_bounty(), 'to' );

		sort( $results );
		sort( $expected );

		$constraint = new PHPUnit_Framework_Constraint_IsEqual( $expected );

		self::assertThat( $results, $constraint );
	}

	function assertPostCount( $expected ) {
		self::assertThat( $GLOBALS['wp_query'], $this->postCount( $expected ) );
	}

	protected function postCount( $expected ) {
		return new APP_Constraint_Post_Count( $expected );
	}
}


class APP_Mail_Catcher {

	protected $mail = array();

	function __construct() {
		add_action( '_wp_mail_sent', array( $this, '_catch_mail' ) );
	}

	function _catch_mail( $args ) {
		$this->mail[] = $args;
	}

	function get_bounty() {
		return $this->mail;
	}
}


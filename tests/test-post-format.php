<?php
/**
 * ROP Test Post format actions for PHPUnit.
 *
 * @package     ROP
 * @subpackage  Tests
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

require_once dirname( __FILE__ ) . '/helpers/class-setup-accounts.php';
/**
 * Test post format related actions. class.
 */
class Test_RopPostFormat extends WP_UnitTestCase {
	/**
	 * Init test accounts.
	 */
	public static function setUpBeforeClass() {
		Rop_InitAccounts::init();
	}

	/**
	 * Testing URL shortners
	 *
	 * @since   8.0.0
	 * @access  public
	 *
	 * @covers Rop_Url_Shortner_Abstract
	 * @covers Rop_Rvivly_Shortner
	 * @covers Rop_Bitly_Shortner
	 * @covers Rop_Shortest_Shortner
	 * @covers Rop_Googl_Shortner
	 * @covers Rop_Isgd_Shortner
	 */
	public function test_url_shortners() {
		$url = 'http://google.com/';

		// rviv.ly Test
		$rvivly = new Rop_Rvivly_Shortner();
		$rvivly->set_website( $url );
		$short_url = $rvivly->shorten_url( $url );
		$this->assertNotEquals( $url, $short_url );

		$this->assertNotFalse( filter_var( $short_url, FILTER_VALIDATE_URL ) );
		$this->assertNotEquals( $short_url, '' );

		// bit.ly Test
		$bitly = new Rop_Bitly_Shortner();
		$user  = 'o_57qgimegp1';
		$key   = 'R_9a63d988de77438aaa6b3cd8e0830b6b';
		$bitly->set_credentials( array( 'user' => $user, 'key' => $key ) );
		$short_url = $bitly->shorten_url( $url );
		$this->assertNotEquals( $url, $short_url );

		$this->assertNotFalse( filter_var( $short_url, FILTER_VALIDATE_URL ) );
		$this->assertNotEquals( $short_url, '' );

		// shorte.st Test
		$shortest = new Rop_Shortest_Shortner();
		$key      = 'e3b65f77eddddc7c0bf1f3a2f5a13f59';
		$shortest->set_credentials( array( 'key' => $key ) );
		$short_url = $shortest->shorten_url( $url );

		$this->assertNotEquals( $url, $short_url );
		$this->assertNotFalse( filter_var( $short_url, FILTER_VALIDATE_URL ) );
		$this->assertNotEquals( $short_url, '' );

		// goo.gl Test
		$googl = new Rop_Googl_Shortner();
		$key   = 'AIzaSyAqNtuEu-xXurkpV-p57r5oAqQgcAyMSN4';
		$googl->set_credentials( array( 'key' => $key ) );
		$short_url = $googl->shorten_url( $url );

		$this->assertNotEquals( $url, $short_url );
		$this->assertNotFalse( filter_var( $short_url, FILTER_VALIDATE_URL ) );
		$this->assertNotEquals( $short_url, '' );

		// is.gd Test
		$isgd      = new Rop_Isgd_Shortner();
		$short_url = $isgd->shorten_url( $url );

		$this->assertNotFalse( filter_var( $short_url, FILTER_VALIDATE_URL ) );
		$this->assertNotEquals( $short_url, '' );

	}

	/**
	 * Testing post format
	 *
	 * @since   8.0.0
	 * @access  public
	 *
	 * @covers Rop_Model_Abstract
	 * @covers Rop_Post_Format_Model::<public>
	 */
	public function test_post_format() {
		$service    = Rop_InitAccounts::ROP_TEST_SERVICE_NAME;
		$account_id = Rop_InitAccounts::get_account_id();
//		$post_format_data = array(
//			'post_content'      => 'post_title',
//			'custom_meta_field' => '',
//			'maximum_length'    => '190',
//			'custom_text'       => 'I am the King of Random!',
//			'custom_text_pos'   => 'beginning',
//			'include_link'      => true,
//			'url_from_meta'     => false,
//			'url_meta_key'      => '',
//			'short_url'         => true,
//			'short_url_service' => 'rviv.ly',
//			'hashtags'          => 'common-hashtags',
//			'hashtags_length'   => '15',
//			'hashtags_common'   => '#testLikeABoss, #themeIsle',
//			'hashtags_custom'   => '',
//			'image'             => true,
//		);

		$global_settings = new Rop_Global_Settings();
		$defaults        = $global_settings->get_default_post_format( $service );

		$post_format = new Rop_Post_Format_Model( $service );

		$this->assertEquals( $post_format->get_post_format( $account_id ), $defaults );
		$this->assertArrayHasKey( 'post_content', $defaults );
		$this->assertArrayHasKey( 'maximum_length', $defaults );
		$this->assertArrayHasKey( 'short_url_service', $defaults );
		$this->assertArrayHasKey( 'short_url', $defaults );
		$this->assertArrayHasKey( 'include_link', $defaults );

		$this->assertEquals( 'post_title', $defaults['post_content'] );
		$this->assertEquals( '140', $defaults['maximum_length'] );
		$this->assertEquals( true, $defaults['short_url'] );
		$this->assertEquals( 'rviv.ly', $defaults['short_url_service'] );
		$this->assertEquals( true, $defaults['include_link'] );
		$new_data                      = $defaults;
		$new_data['include_link']      = false;
		$new_data['maximum_length']    = 1900;
		$new_data['short_url_service'] = 'wp_short_url';

		$post_format->add_update_post_format( $account_id, $new_data );

		$this->assertEquals( $post_format->get_post_format( $account_id ), $new_data );

		$post_format->remove_post_format( $account_id );

		$this->assertEquals( $post_format->get_post_format( $account_id ), $defaults );

	}

}

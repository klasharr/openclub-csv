<?php

namespace OpenClubCSV\Test;

use OpenClub\Data_Set_Input;
use OpenClub\Null_Filter;

require_once( 'class-base.php' );
require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/class-data-set-input.php' );
require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/class-csv-display.php' );

/**
 * Class CSVDataSetInput
 * @package OpenClubCSV\Test
 * @group input
 */
class CSVDataSetInput extends Base {

	/**
	 * @var \OpenClub\Data_Set_Input
	 */
	private $csv_data_input;

	/**
	 * @var \WP_Post
	 */
	private $valid_post;

	public static function wpSetUpBeforeClass( \WP_UnitTest_Factory $factory ) {
	}

	private function get_data_set_input_object() {
		return new \OpenClub\Data_Set_Input();
	}

	public function setUp(){
		$this->csv_data_input = new \OpenClub\Data_Set_Input();
	}

	public function test_init_called_with_no_array_will_not_set_config(){

		$this->assertEquals( $this->csv_data_input->get_init_has_been_called(), false);

	}

	public function test_init_called_with_array_will_set_config(){

		$this->csv_data_input->init( $this->get_valid_test_config() );
		$this->assertEquals( $this->csv_data_input->get_init_has_been_called(), true );

	}
	

	/**
	 * This will let us know if we change the real config defaults.
	 * If so, we can update the dummy data in the base class.
	 */
	public function test_default_config_is_same_as_test_data() {

		$this->assertEquals( $this->get_default_config(), \OpenClub\CSV_Display::get_config() );

	}

	// ########################### Post ID ###########################

	public function test_init_passing_empty_post_id_will_throw_exception() {

		$this->setExpectedException( 'Exception', '$post_id was not passed' );
		$this->csv_data_input->init(  $this->get_default_config() );
	}

	public function test_init_non_numeric_empty_post_id_will_throw_exception() {

		$this->setExpectedException( 'Exception', '$post_id is not numeric' );
		$config            = $this->get_default_config();
		$config['post_id'] = 'hamster';
		$this->csv_data_input->init(  $config );
	}

	// ########################### Group by field ###################################

	public function test_init_setting_empty_space_group_by_field_will_throw_exception() {

		$this->setExpectedException( 'Exception', 'set_group_by_field called with invalid value, likely an empty space' );
		$config = $this->get_valid_test_config();
		$config['group_by_field'] = ' ';
		$this->csv_data_input->init(  $config );
	}

	// ################################### Fields ###################################

	public function test_init_correct_fields_override() {

		$config = $this->get_valid_test_config();
		$config['fields']  = 'Event,Fare,Date';
		$this->csv_data_input->init(  $config );

		$this->assertEquals(
			$this->csv_data_input->get_fields_override(),
			explode( ',', $config['fields'] )
		);
	}

	public function test_init_setting_group_field_with_non_existing_field_will_throw_exception() {

		$this->setExpectedException( 'Exception', 'set_group_by_field called with invalid field.' );
		$config = $this->get_valid_test_config();
		$config['group_by_field'] = 'hamster';
		$this->csv_data_input->init( $config );
	}

	// ##################################### Error Messages #####################################

	public function test_init_setting_error_messages_with_bad_value_will_throw_exception() {

		$this->setExpectedException( 'Exception', 'error_messages must be yes, no or not set. The default is yes.' );
		$config = $this->get_valid_test_config();
		$config['error_messages'] = 'hamster';
		$this->csv_data_input->init( $config );
	}

	public function test_init_setting_error_messages_with_int_one_will_throw_exception() {

		$this->setExpectedException( 'Exception', 'error_messages must be yes, no or not set. The default is yes.' );
		$config = $this->get_valid_test_config();
		$config['error_messages'] = 1;
		$this->csv_data_input->init( $config );
	}

	public function test_init_setting_error_messages_with_default_will_return_true() {

		$config = $this->get_valid_test_config();
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_error_messages(), true );
	}

	public function test_init_setting_error_messages_with_yes_will_return_true() {

		$config = $this->get_valid_test_config();
		$config['error_messages'] = 'yes';
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_error_messages(), true );
	}

	public function test_init_setting_error_messages_with_no_will_return_true() {

		$config = $this->get_valid_test_config();
		$config['error_messages'] = 'no';
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_error_messages(), false );
	}

	public function test_init_setting_error_messages_with_zero_will_return_false() {

		$config = $this->get_valid_test_config();
		$config['error_messages'] = 0;
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_error_messages(), false );
	}

	public function test_init_setting_error_messages_with_string_zero_will_return_false() {

		$config = $this->get_valid_test_config();
		$config['error_messages'] = '0';
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_error_messages(), false );
	}

	// ##################################### Error Lines #####################################

	public function test_init_setting_error_lines_with_bad_value_will_throw_exception() {

		$config = $this->get_valid_test_config();

		$this->setExpectedException( 'Exception', 'error_lines must be yes, no or not set. The default is yes.' );
		$config = $this->get_valid_test_config();
		$config['error_lines'] = 'hamster';
		$this->csv_data_input->init( $config );
	}


	public function test_init_setting_error_lines_with_default_will_return_true() {

		$config = $this->get_valid_test_config();
		$this->csv_data_input->init( $config );
		$this->assertEquals( $this->csv_data_input->get_error_lines(), true );
	}


	public function test_init_setting_error_lines_with_yes_will_return_true() {

		$config = $this->get_valid_test_config();
		$config['error_lines'] = 'yes';
		$this->csv_data_input->init( $config );
		$this->assertEquals( true, $this->csv_data_input->get_error_lines() );
	}

	public function test_init_setting_error_lines_with_no_will_return_true() {

		$config = $this->get_valid_test_config();
		$config['error_lines'] = 'no';
		$this->csv_data_input->init( $config );
		$this->assertEquals( false, $this->csv_data_input->get_error_lines() );
	}

	public function test_init_setting_error_lines_with_not_yes_will_return_false() {

		$config = $this->get_valid_test_config();
		$config['error_lines'] = 0;
		$this->csv_data_input->init( $config );
		$this->assertEquals( false, $this->csv_data_input->get_error_lines() );

		$config['error_lines'] = '0';
		$o = new \OpenClub\Data_Set_Input( $config );
		$this->assertEquals( false, $o->get_error_lines() );

	}

	// #####################################  Future Items Only #####################################

	public function test_init_setting_future_items_only_with_bad_value_will_throw_exception() {

		$config = $this->get_valid_test_config();
		$this->setExpectedException( 'Exception', '$config[\'future_items_only\'] can be "yes", "no", 1, 2 or must not be set.' );
		$config['future_items_only'] = 'hamster';
		$this->csv_data_input->init( $config );
	}

	public function test_init_setting_future_items_will_return_correct_values() {

		$set_and_return_values = array(
			'yes' => true,
			2 => true,
			'no' => false,
			1 => false,
			0 => false,
			'2dsd' => false,
			/*'0sdsd' => false,
			'yeyey' => false,*/
		);

		$config = $this->get_valid_test_config();
		foreach( $set_and_return_values as $set => $return ) {


			$config['future_items_only'] = $set;

			$o = $this->get_data_set_input_object();
			$o->init( $config );
			echo $set. ' ' . $return;
			$this->assertEquals( $return, $o->get_future_items_value() );
		}

	}






/**

	public function test_constructor_setting_bad_field_overrides_will_throw_exception() {
		$this->setExpectedException( 'Exception', 'Field override error: field Hamster does not exist. Check the config.' );

		$post = $this->get_valid_post();

		$config            = $this->get_default_config();
		$config['fields']  = 'Event,Fare,Hamster';
		$config['post_id'] = $post->ID;

		$o = new \OpenClub\Data_Set_Input( $config );

		$this->assertEquals( $o->get_fields_override(), explode( ',', $config['fields'] ) );

	}

	public function test_constructor_setting_limit_with_non_integer_will_throw_exception() {

		$post = $this->get_valid_post();

		$config            = $this->get_default_config();
		$config['post_id'] = $post->ID;

		$config['limit'] = 'dd';
		$this->setExpectedException( 'Exception', '$limit must be an integer and greater than zero.' );
		$o = new \OpenClub\Data_Set_Input( $config );

	}

	public function test_constructor_setting_limit_with_negative_integer_will_throw_exception() {

		$post = $this->get_valid_post();

		$config            = $this->get_default_config();
		$config['post_id'] = $post->ID;

		$config['limit'] = - 1;
		$this->setExpectedException( 'Exception', '$limit must be an integer and greater than zero.' );
		$o = new \OpenClub\Data_Set_Input( $config );

	}

	public function test_constructor_setting_future_items_only_with_invalid_value_will_throw_exception() {

		$post = $this->get_valid_post();

		$config            = $this->get_default_config();
		$config['post_id'] = $post->ID;

		$config['future_items_only'] = 's';
		$this->setExpectedException( 'Exception', '$config[\'future_items_only\'] can be "yes", "no", 1, 2 or must not be set.' );
		$o = new \OpenClub\Data_Set_Input( $config );

	}

	public function test_constructor_setting_non_existing_filter_will_throw_exception() {

		$post = $this->get_valid_post();

		$config            = $this->get_default_config();
		$config['post_id'] = $post->ID;

		$config['filter'] = 'foo';
		$this->setExpectedException( 'Exception', 'Filter class \OpenClub\foo does not exist, check the value passed in $config[ \'filter\' ]' );
		$o = new \OpenClub\Data_Set_Input( $config );

	}

	public function test_get_set_config_with_no_extra_settings_is_valid() {

		$post = $this->get_valid_post();

		$config            = $this->get_default_config();
		$config['post_id'] = $post->ID;
		$o                 = new \OpenClub\Data_Set_Input( $config );

		$set_config = $o->get_set_config();

		$test_config_data = array(
			'post'               => $post,
			'post_id'            => $post->ID,
			'filter'             => new Null_Filter(),
			'group_by_field'     => null,
			'overridden_fields'  => array(),
			'context'            => null,
			'limit'              => null,
			'future_items_only' => false,
			'error_messages'    => true,
			'error_lines'       => true,
		);

		$this->assertEquals( $set_config, $test_config_data );
	}

	public function test_get_set_config_with_settings_is_valid() {

		$post = $this->get_valid_post();

		$config                       = $this->get_default_config();
		$config['post_id']            = $post->ID;
		$config['filter']             = 'Empty_Description';
		$config['fields']             = 'Fare,Event,Date';
		$config['context']            = 'my_context';
		$config['limit']              = 10;
		$config['future_items_only']  = 'yes';
		$config['error_messages']     = 'no';
		$config['error_lines']        = 'no';
		$o                            = new \OpenClub\Data_Set_Input( $config );

		$set_config = $o->get_set_config();

		$test_config_data = array(
			'post'               => $post,
			'post_id'            => $post->ID,
			'filter'             => new \OpenClub\Empty_Description(),
			'group_by_field'     => null,
			'overridden_fields'  => explode( ',', 'Fare,Event,Date' ),
			'context'            => 'my_context',
			'limit'              => 10,
			'error_messages'     => false,
			'error_lines'        => false,
			'future_items_only' => true,
		);

		$this->assertEquals( $set_config, $test_config_data );
	}

	public function test_get_post_id_returns_correct_post_id(){

		$post = $this->get_valid_post();

		$o = new \OpenClub\Data_Set_Input(
			$this->get_default_config( array( 'post_id' => $post->ID ) )
		);

		$this->assertEquals( $o->get_post_id(), $post->ID );

	}

	public function test_get_group_by_field_returns_correct_field() {

		$post = $this->get_valid_post();

		$o = new \OpenClub\Data_Set_Input(
			$this->get_default_config(
				array(
					'post_id' => $post->ID,
					'group_by_field' => 'Date')
			)
		);

		$this->assertEquals( $o->get_group_by_field(), 'Date' );
	}

	public function test_get_filter_with_no_filter_set_returns_null_filter() {

		$post = $this->get_valid_post();

		$o = new \OpenClub\Data_Set_Input(
			$this->get_default_config(
				array(
					'post_id' => $post->ID)
			)
		);

		$this->assertEquals( $o->get_filter(), new \OpenClub\Null_Filter() );
	}

	public function test_get_filter_with_filter_set_returns_correct_filter() {

		$post = $this->get_valid_post();

		$o = new \OpenClub\Data_Set_Input(
			$this->get_default_config(
				array(
					'post_id' => $post->ID,
					'filter' => 'Empty_Description')
			)
		);

		$this->assertEquals( $o->get_filter(), new \OpenClub\Empty_Description() );
	}

	public function test_has_group_by_field_returns_true_if_group_by_field_is_set(){

		$post = $this->get_valid_post();

		$o = new \OpenClub\Data_Set_Input(
			$this->get_default_config(
				array(
					'post_id' => $post->ID,
					'group_by_field' => 'Date')
			)
		);

		$this->assertTrue( $o->has_group_by_field() );
	}

	public function test_has_group_by_field_returns_false_if_no_group_by_field_is_set(){

		$post = $this->get_valid_post();

		$o = new \OpenClub\Data_Set_Input(
			$this->get_default_config(
				array(
					'post_id' => $post->ID
				)
			)
		);

		$this->assertFalse( $o->has_group_by_field() );
	}

	public function test_is_show_future_items_only_returns_true_if_set(){

		$post = $this->get_valid_post();

		$o = new \OpenClub\Data_Set_Input(
			$this->get_default_config(
				array(
					'post_id' => $post->ID,
					'future_items_only' => 'yes'
				)
			)
		);

		$this->assertTrue( $o->is_show_future_items_only() );
	}

	public function test_is_show_future_items_only_returns_false_if_not_set(){

		$post = $this->get_valid_post();

		$o = new \OpenClub\Data_Set_Input(
			$this->get_default_config(
				array(
					'post_id' => $post->ID,
				)
			)
		);
		$this->assertFalse( $o->is_show_future_items_only() );
	}

	public function test_error_lines_variations_are_set_properly_to_the_config(){

		$variations = array(
			'yes' => true,
			'no' => false,
		);

		$post = $this->get_valid_post();

		foreach( $variations as $key => $value ) {

			$o = new \OpenClub\Data_Set_Input(
				$this->get_default_config(
					array(
						'post_id' => $post->ID,
						'error_lines' => $key,
					)
				)
			);

			$this->assertEquals( $value, $o->get_config( 'error_lines' ) );

		}

	}


	public function test_get_boolean_from_config_value_returns_correct_values() {

		$post = $this->get_valid_post();

		$o = new \OpenClub\Data_Set_Input(
			$this->get_default_config(
				array(
					'post_id' => $post->ID,
				)
			)
		);

	}
*/

}
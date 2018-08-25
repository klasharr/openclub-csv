<?php

namespace OpenClubCSV\Test;

require_once( 'class-base.php' );


class CSVParserTest extends Base {

	function test_constructor_with_post_with_no_content_throws_exception(){

		$post   = $this->get_valid_post( array( 'post_content' => false ) ) ;

		$config = $this->get_default_config( array( 'post_id' => $post->ID ) );
		$data_set_input = new \OpenClub\Data_Set_Input( $config );


		$this->setExpectedException( 'Exception', sprintf( 'Post ID %d has no content', $post->ID ) );
		$parser = new \OpenClub\Parser( $data_set_input );

	}

}
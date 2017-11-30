<?php

/**
 * This little class records how long it takes each WordPress action or filter
 * to execute which gives a good indicator of what hooks are being slow.
 * You can then debug those hooks to see what hooked functions are causing problems.
 * 
 * This class does NOT time the core WordPress code that is being run between hooks.
 * You could use similar code to this that doesn't have an end processor to do that.
 * 
 * @version 0.4
 * @author Alex Mills (Viper007Bond)
 *
 * This code is released under the same license as WordPress:
 * http://wordpress.org/about/license/
 */

// Here's a test to make sure it's working
add_action( 'wp_footer', function() { sleep( 2 ); } );


class WhatIsSoSlow {

	public $data = array();

	function __construct() {
		add_action( 'all', array( $this, 'filter_start' ) );
		add_action( 'shutdown', array( $this, 'results' ) );
	}

	// This runs first for all actions and filters.
	// It starts a timer for this hook.
	public function filter_start() {
		$current_filter = current_filter();
		
		$this->data[ $current_filter ][]['start'] = microtime( true );

		add_filter( $current_filter, array( $this, 'filter_end' ), 99999 );
	}

	// This runs last (hopefully) for each hook and records the end time.
	// This has problems if a hook fires inside of itself since it assumes
	// the last entry in the data key for this hook is the matching pair.
	public function filter_end( $filter_data ) {
		$current_filter = current_filter();
		
		remove_filter( $current_filter, array( $this, 'filter_end' ), 99999 );

		end( $this->data[ $current_filter ] );

		$last_key = key( $this->data[ $current_filter ] );

		$this->data[ $current_filter ][ $last_key ]['stop'] = microtime( true );

		return $filter_data;
	}

	// Processes the results and var_dump()'s them. TODO: Debug bar panel?
	public function results() {
		$results = array();

		foreach ( $this->data as $filter => $calls ) {
			foreach ( $calls as $call ) {
				// Skip filters with no end point (i.e. the hook this function is hooked into)
				if ( ! isset( $call['stop'] ) )
					continue;

				if ( ! isset( $results[ $filter ] ) )
					$results[ $filter ] = 0;

				$results[ $filter ] = $results[ $filter ] + ( $call['stop'] - $call['start'] );
			}
		}

		asort( $results, SORT_NUMERIC );

		$results = array_reverse( $results );

		var_dump( $results );
	}
}

new WhatIsSoSlow();
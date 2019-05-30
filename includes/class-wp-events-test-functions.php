<?php
/**
 * Functions.
 *
 * @package WP Events Test.
 */

/**
 * Asset management.
 */
if ( ! class_exists('WPEvents_Test_Functions') ) : 

	class WPEvents_Test_Functions {	

		/**
		 * Get events.
		 */		
		public static function get_events( $get_year, $get_monthnum ) {
			
			global $wpdb;
			
			$sql = "SELECT post.ID, post.post_title, post.post_content, $wpdb->postmeta.meta_value FROM $wpdb->posts AS post INNER JOIN $wpdb->postmeta ON ( post.ID = $wpdb->postmeta.post_id ) WHERE 1=1  AND 
					( $wpdb->postmeta.meta_key = '%s' ) AND 
					post.post_type = 'event' AND (post.post_status = 'publish' OR post.post_status = 'private') GROUP BY post.ID ORDER BY post.post_date ASC";
		
			$_results = $wpdb->get_results( $wpdb->prepare( $sql, 'wp_events__event_date' ) );
			
			if ( empty( $_results ) ) {
				return array();
			}
			
			$results = array();
			foreach( $_results as $key=>$event ) {
				
				$_date = explode( '-', $event->meta_value );

				if ( $_date[0] == $get_year && $_date[1] == $get_monthnum ) {
					$results[ $_date[2] ] = $event;
				}
				
			}
			
			return $results;
		}
		
		/**
		 * Check if day is today.
		 */
		public static function is_current_day( $day, $thismonth, $thisyear ) {
			if ( $day == current_time( 'j' ) &&	$thismonth == current_time( 'm' ) && $thisyear == current_time( 'Y' ) ) {
				return true;
			}
			return false;
		}
		
		/**
		 * Get calendar.
		 */
		public static function get_calendar( $initial = true, $echo = true, $get_month = false ) {
			
			global $wpdb, $m, $monthnum, $year, $wp_locale, $posts;

			$get_monthnum = $monthnum;
			$get_year = $year;
			
			if ( $get_month ) {
				$_m = explode( '/', $get_month );
				$get_monthnum = $_m[1];
				$get_year = $_m[0];
			}

			//$key   = md5( $m . $monthnum . $year );
			$key   = md5( $m . $get_monthnum . $get_year );
			$cache = wp_cache_get( 'get_calendar', 'calendar' );

			if ( $cache && is_array( $cache ) && isset( $cache[ $key ] ) ) {
				/** This filter is documented in wp-includes/general-template.php */
				$output = apply_filters( 'wp_events_test_get_calendar', $cache[ $key ] );

				if ( $echo ) {
					echo $output;
					return;
				}

				return $output;
			}

			if ( ! is_array( $cache ) ) {
				$cache = array();
			}

			// Quick check. If we have no events at all, abort!
			/*
			$gotsome = $wpdb->get_var( "SELECT 1 as test FROM $wpdb->posts WHERE post_type = 'event' AND post_status = 'publish' LIMIT 1" );
			if ( ! $gotsome ) {
				$cache[ $key ] = '';
				wp_cache_set( 'get_calendar', $cache, 'calendar' );
				return '';
			} // */
			

			if ( isset( $_GET['w'] ) ) {
				$w = (int) $_GET['w'];
			}
			// week_begins = 0 stands for Sunday
			$week_begins = (int) get_option( 'start_of_week' );

			// Let's figure out when we are.
			//if ( ! empty( $monthnum ) && ! empty( $year ) ) {
			if ( ! empty( $get_monthnum ) && ! empty( $get_year ) ) {
				//$thismonth = zeroise( intval( $monthnum ), 2 );
				$thismonth = zeroise( intval( $get_monthnum ), 2 );
				//$thisyear  = (int) $year;
				$thisyear  = (int) $get_year;
			} elseif ( ! empty( $w ) ) {
				// We need to get the month from MySQL
				$thisyear = (int) substr( $m, 0, 4 );
				//it seems MySQL's weeks disagree with PHP's
				$d         = ( ( $w - 1 ) * 7 ) + 6;
				$thismonth = $wpdb->get_var( "SELECT DATE_FORMAT((DATE_ADD('{$thisyear}0101', INTERVAL $d DAY) ), '%m')" );
			} elseif ( ! empty( $m ) ) {
				$thisyear = (int) substr( $m, 0, 4 );
				if ( strlen( $m ) < 6 ) {
					$thismonth = '01';
				} else {
					$thismonth = zeroise( (int) substr( $m, 4, 2 ), 2 );
				}
			} else {
				$thisyear  = current_time( 'Y' );
				$thismonth = current_time( 'm' );
			}

			$unixmonth = mktime( 0, 0, 0, $thismonth, 1, $thisyear );
			$last_day  = date( 't', $unixmonth );

			// get events for whole month.
			$day_with_events = WPEvents_Test_Functions::get_events( $thisyear, $thismonth );
			
			// Get previous month and year.
			$previous = (object) array( 'id' => 'previous_month' );
			$previous_month = (int) $thismonth - 1;
			$_year = $thisyear;
			if ( $previous_month == 0 ) {
				$previous_month = 12;
				$_year = (int) $thisyear - 1;
			}
			$previous->month = zeroise( $previous_month, 2 );
			$previous->year  = $_year;

			// Get next month and year.
			$next = (object) array( 'id' => 'next_month' );
			$next_month = (int) $thismonth + 1;
			$_year = $thisyear;
			if ( $next_month > 12 ) {
				$next_month = 1;
				$_year = (int) $thisyear + 1;
			}
			$next->month = zeroise( $next_month, 2 );
			$next->year  = $_year;			
			
			/* translators: Calendar caption: 1: month name, 2: 4-digit year */
			$calendar_caption = _x( '%1$s %2$s', 'calendar caption' );
			$calendar_output  = '<table class="wp-events-test-calendar">
			<caption>' . sprintf(
				$calendar_caption,
				$wp_locale->get_month( $thismonth ),
				date( 'Y', $unixmonth )
			) . '</caption>
			<thead>
			<tr>';

			$myweek = array();

			for ( $wdcount = 0; $wdcount <= 6; $wdcount++ ) {
				$myweek[] = $wp_locale->get_weekday( ( $wdcount + $week_begins ) % 7 );
			}

			foreach ( $myweek as $wd ) {
				$day_name         = $initial ? $wp_locale->get_weekday_initial( $wd ) : $wp_locale->get_weekday_abbrev( $wd );
				$wd               = esc_attr( $wd );
				$calendar_output .= "\n\t\t<th scope=\"col\" title=\"$wd\">$day_name</th>";
			}

			$calendar_output .= '
			</tr>
			</thead>

			<tfoot>
			<tr>';

			if ( $previous ) {
				$calendar_output .= "\n\t\t" . '<td colspan="3" id="wp-events-test-prev" class="wp-events-test-prev"><a href="#" onclick="return false;" class="wp-events-test-nav previous" data-get-month="'.$previous->year.'/'.$previous->month.'">&laquo; ' .
					$wp_locale->get_month_abbrev( $wp_locale->get_month( $previous->month ) ) . '&nbsp;' . $previous->year .
				'</a></td>';
			} else {
				$calendar_output .= "\n\t\t" . '<td colspan="3" id="wp-events-test-prev" class="pad wp-events-test-prev">&nbsp;</td>';
			}

			$calendar_output .= "\n\t\t" . '<td class="pad"><span class="wp-events-test-spinner"></span></td>';

			if ( $next ) {
				$calendar_output .= "\n\t\t" . '<td colspan="3" id="wp-events-test-next" class="wp-events-test-next"><a href="#" onclick="return false;" class="wp-events-test-nav next"  data-get-month="'.$next->year.'/'.$next->month.'">' .
					$wp_locale->get_month_abbrev( $wp_locale->get_month( $next->month ) ) . '&nbsp;' . $next->year .
				' &raquo;</a></td>';
			} else {
				$calendar_output .= "\n\t\t" . '<td colspan="3" id="wp-events-test-next" class="pad wp-events-test-next">&nbsp;</td>';
			}

			$calendar_output .= '
			</tr>
			</tfoot>

			<tbody>
			<tr class="calendar-row calendar-row-first">';
			
			// See how much we should pad in the beginning
			$pad = calendar_week_mod( date( 'w', $unixmonth ) - $week_begins );
			if ( 0 != $pad ) {
				$calendar_output .= "\n\t\t" . '<td colspan="' . esc_attr( $pad ) . '" class="pad">&nbsp;</td>';
			}

			$newrow      = false;
			$daysinmonth = (int) date( 't', $unixmonth );

			for ( $day = 1; $day <= $daysinmonth; ++$day ) {
				if ( isset( $newrow ) && $newrow ) {
					$calendar_output .= "\n\t</tr>\n\t<tr class=\"calendar-row\">\n\t\t";
				}
				$newrow = false;

				if ( array_key_exists( $day, $day_with_events ) ) {

					// any event today?

					$_title = $day_with_events[$day]->post_title;
					$_content = $day_with_events[$day]->post_content;
					
					if ( self::is_current_day( $day, $thismonth, $thisyear ) ) {
						$calendar_output .= '<td class="today wp-events-test-today cell with-events" data-tooltip data-tooltip-title="'.$_title.'" data-tooltip-content="'.$_content.'">';
					} else {
						$calendar_output .= '<td class="cell with-events" data-tooltip data-tooltip-title="'.$_title.'" data-tooltip-content="'.$_content.'">';
						
					}
					
					$calendar_output .= sprintf( '<a href="%s">%s</a>', '#', $day );
					
				} else {
					
					if ( self::is_current_day( $day, $thismonth, $thisyear ) ) {
						$calendar_output .= '<td class="today wp-events-test-today cell">';
					} else {
						$calendar_output .= '<td class="cell">';
					}
					$calendar_output .= $day;
					
				}
				$calendar_output .= '</td>';

				if ( 6 == calendar_week_mod( date( 'w', mktime( 0, 0, 0, $thismonth, $day, $thisyear ) ) - $week_begins ) ) {
					$newrow = true;
				}
			}

			$pad = 7 - calendar_week_mod( date( 'w', mktime( 0, 0, 0, $thismonth, $day, $thisyear ) ) - $week_begins );
			if ( $pad != 0 && $pad != 7 ) {
				$calendar_output .= "\n\t\t" . '<td class="pad" colspan="' . esc_attr( $pad ) . '">&nbsp;</td>';
			}
			$calendar_output .= "\n\t</tr>\n\t</tbody>\n\t</table>";

			$cache[ $key ] = $calendar_output;
			wp_cache_set( 'get_calendar', $cache, 'calendar' );

			if ( $echo ) {
				/**
				 * Filters the HTML calendar output.
				 *
				 * @since 1.0.0
				 *
				 * @param string $calendar_output HTML output of the calendar.
				 */
				echo apply_filters( 'wp_events_test_get_calendar', $calendar_output );
				return;
			}
			/** This filter is documented in wp-includes/general-template.php */
			return apply_filters( 'wp_events_test_get_calendar', $calendar_output );
			
		}
		
	}
	
endif;

# --- EOF
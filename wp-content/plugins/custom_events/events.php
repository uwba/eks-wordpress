<?
/*
  Plugin Name: Custom Events
  Plugin URI: http://hondosite.com
  Description: Events
  Author: Andrey
  Version: 1.0
  Author URI:
 */

/* draws a calendar */
function draw_calendar($month, $year, $events = array()) {

	/* draw table */
	$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

	/* table headings */
	$headings = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
	$calendar.= '<tr class="calendar-row"><th class="calendar-day-head">' . implode('</th><th class="calendar-day-head">', $headings) . '</th></tr>';

	/* days and weeks vars now ... */
	$running_day = date('w', mktime(0, 0, 0, $month, 1, $year));
	$days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<tr class="calendar-row">';

	/* print "blank" days until the first of the current week */
	for ($x = 0; $x < $running_day; $x++):
		$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
		$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for ($list_day = 1; $list_day <= $days_in_month; $list_day++):
		$calendar.= '<td class="calendar-day"><div style="position:relative;height:100px;">';
		/* add in the day number */
		$calendar.= '<div class="day-number">' . $list_day . '</div>';
		$calendar.= draw_ceil_content($events[$list_day]);
		$event_day = $year . '-' . $month . '-' . $list_day;
		
		$calendar.= '</div></td>';
		if ($running_day == 6):
			$calendar.= '</tr>';
			if (($day_counter + 1) != $days_in_month):
				$calendar.= '<tr class="calendar-row">';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++;
		$running_day++;
		$day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if ($days_in_this_week < 8):
		for ($x = 1; $x <= (8 - $days_in_this_week); $x++):
			$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
		endfor;
	endif;

	/* final row */
	$calendar.= '</tr>';


	/* end the table */
	$calendar.= '</table>';

	/** DEBUG * */
	$calendar = str_replace('</td>', '</td>' . "\n", $calendar);
	$calendar = str_replace('</tr>', '</tr>' . "\n", $calendar);

	/* all done, return result */
	return $calendar;
}

function draw_ceil_content($events) {
//	var_dump($events);
	$calendar = '';
	if (isset($events)) {
		foreach ($events as $event) {
			$calendar.= '<div class="event"><a href="' . get_permalink( $event->ID ). '" title="'.$event->shift." ({$event->status})".'">'.$event->post_title . '</a></div>';
		}
	} else {
		$calendar.= '&nbsp;';
	}
	return $calendar;
}

function draw_controls($month, $year, $view = FALSE) {

	/* select month control */
	$select_month_control = '<select name="month" id="month">';
	for ($x = 1; $x <= 12; $x++) {
		$select_month_control.= '<option value="' . $x . '"' . ($x != $month ? '' : ' selected="selected"') . '>' . date('F', mktime(0, 0, 0, $x, 1, $year)) . '</option>';
	}
	$select_month_control.= '</select>';

	/* select year control */
	$year_range = 7;
	$select_year_control = '<select name="year" id="year">';
	for ($x = ($year - floor($year_range / 2)); $x <= ($year + floor($year_range / 2)); $x++) {
		$select_year_control.= '<option value="' . $x . '"' . ($x != $year ? '' : ' selected="selected"') . '>' . $x . '</option>';
	}
	$select_year_control.= '</select>';
	if ($view) {
		$select_view_control = "<select name='view' id='view'>
		<option value='all'" . ($view != 'all' ? '' : ' selected="selected"') .">All</option>
		<option value='my'". ($view != 'my' ? '' : ' selected="selected"') .">My</option>
		</select>";
	} else {
		$select_view_control = '';
	}
	

	/* "next month" control */
	$next_month_link = '<a href="?view='.$view.'&month=' . ($month != 12 ? $month + 1 : 1) . '&year=' . ($month != 12 ? $year : $year + 1) . '" class="control">Next Month &gt;&gt;</a>';

	/* "previous month" control */
	$previous_month_link = '<a href="?view='.$view.'&month=' . ($month != 1 ? $month - 1 : 12) . '&year=' . ($month != 1 ? $year : $year - 1) . '" class="control">&lt;&lt; 	Previous Month</a>';


	/* bringing the controls together */
	$controls = '<form method="get">' . $select_month_control . $select_year_control . $select_view_control.'&nbsp;<input type="submit" name="submit" value="Go" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $previous_month_link . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $next_month_link . ' </form>';
	return $controls;
}


function coordinator_calendar() {
	/* date settings */
	$month = (int) ($_GET['month'] ? $_GET['month'] : date('m'));
	$year = (int) ($_GET['year'] ? $_GET['year'] : date('Y'));
	
	$controls = draw_controls($month, $year);

	
	/* get all events for the given month */
	$events = array();
	global $wpdb;
	global $current_user, $user_ID;
	get_currentuserinfo();
	$query = "SELECT p.ID, p.post_title, m1.meta_value tax_site, m2.meta_value volunteer, m3.meta_value date, m4.meta_value shift, m5.meta_value status
 FROM wp_posts p
 INNER JOIN wp_postmeta m1 on m1.post_id = p.ID and m1.meta_key='tax_site'
 INNER JOIN wp_postmeta m2 on m2.post_id = p.ID and m2.meta_key='volunteer'
 INNER JOIN wp_postmeta m3 on m3.post_id = p.ID and m3.meta_key='date'
 INNER JOIN wp_postmeta m4 on m4.post_id = p.ID and m4.meta_key='shift'
 INNER JOIN wp_postmeta m5 on m5.post_id = p.ID and m5.meta_key='status'
 WHERE p.post_author = $user_ID 
 AND m3.meta_value LIKE '$year$month%'";
	$results = $wpdb->get_results($query, 'OBJECT');
//		echo $query;

	foreach ($results as $item) {
//		if (!$item->status) ===
		$events[substr($item->date, 6, 2)][] = $item;
		
	}
//	var_dump($events);

	echo '<h2 style="float:left; padding-right:30px;">' . date('F', mktime(0, 0, 0, $month, 1, $year)) . ' ' . $year . '</h2>';
	echo '<div style="float:left;">' . $controls . '</div>';
	echo '<div style="clear:both;"></div>';
	echo draw_calendar($month, $year, $events);
	echo '<br /><br />';
}

function volunteer_calendar() {
	/* date settings */
	$month = (int) ($_GET['month'] ? $_GET['month'] : date('m'));
	$year = (int) ($_GET['year'] ? $_GET['year'] : date('Y'));
	$view = isset($_GET['view']) ? $_GET['view'] : 'all';
	if (!in_array($view, array('all', 'my'))) {
		$view = 'all';
	}
	
	$controls = draw_controls($month, $year, $view);

	
	/* get all events for the given month */
	$events = array();
	global $wpdb;
	global $current_user, $user_ID;
	get_currentuserinfo();
	$query = "SELECT p.ID, p.post_title, m1.meta_value tax_site, m2.meta_value volunteer, m3.meta_value date, m4.meta_value shift, m5.meta_value status
 FROM wp_posts p
 INNER JOIN wp_postmeta m1 on m1.post_id = p.ID and m1.meta_key='tax_site'
 INNER JOIN wp_postmeta m2 on m2.post_id = p.ID and m2.meta_key='volunteer'
 INNER JOIN wp_postmeta m3 on m3.post_id = p.ID and m3.meta_key='date'
 INNER JOIN wp_postmeta m4 on m4.post_id = p.ID and m4.meta_key='shift'
 INNER JOIN wp_postmeta m5 on m5.post_id = p.ID and m5.meta_key='status'
 LEFT JOIN wp_term_relationships tr ON tr.object_id = p.ID
 LEFT JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tr.term_taxonomy_id AND tt.taxonomy = 'listing_category'	
 WHERE m3.meta_value LIKE '$year$month%'";
	if ($view == 'my') {
		$query .= " AND m2.meta_value = $user_ID ";
	} elseif ($view == 'all') {
		$tax_sites = get_volunteer_tax_sites();
		$query .= " AND m1.meta_value IN (".implode(',', array_keys($tax_sites)).")";
	}
 
	$results = $wpdb->get_results($query, 'OBJECT');
//	var_dump($results);
//		echo $query;

	foreach ($results as $item) {
		$events[substr($item->date, 6, 2)][] = $item;
	}
//	var_dump($events);

	echo '<h2 style="float:left; padding-right:30px;">' . date('F', mktime(0, 0, 0, $month, 1, $year)) . ' ' . $year . '</h2>';
	echo '<div style="float:left;">' . $controls . '</div>';
	echo '<div style="clear:both;"></div>';
	echo draw_calendar($month, $year, $events);
	echo '<br /><br />';
}


add_action( 'wp_ajax_nopriv_take_part', 'take_part' );
add_action( 'wp_ajax_take_part', 'take_part' );
 
function take_part() {
	$result = array();
	header( "Content-Type: application/json" );
	
	$post_ID = (int) $_POST['post_ID'];
	global $current_user, $user_ID;	get_currentuserinfo();
	
	$event = get_post($post_ID);
	if (can_take_part($event)) {
		update_post_meta($event->ID, 'volunteer', $user_ID);
	} else {
		$result['errors'][] = 'You cant take part at this event!';
	}
	
	echo json_encode($result);
	exit;
}

function can_take_part($event) {
	// is event free ?
	$volunteer = get_post_meta($event->ID, 'volunteer', TRUE);
	if ($volunteer) {
		return FALSE;
	}
	
	// is volunteer?
	global $current_user; get_currentuserinfo(); 
	if ($current_user->roles[0] != 'volunteer') {
		return FALSE;
	}
	
	return TRUE;
}

add_action( 'wp_ajax_nopriv_approve', 'approve' );
add_action( 'wp_ajax_approve', 'approve' );
 
function approve() {
	$result = array();
	header( "Content-Type: application/json" );
	
	$post_ID = (int) $_POST['post_ID'];
	global $current_user, $user_ID;	get_currentuserinfo();
	
	
	
	$event = get_post($post_ID);
	if ($event->post_author == $user_ID) {
		update_post_meta($event->ID, 'status', 'approved');
	} else {
		$result['errors'][] = 'It is not your event!';
	}

	echo json_encode($result);
	exit;
}

add_action( 'wp_ajax_nopriv_decline', 'decline' );
add_action( 'wp_ajax_decline', 'decline' );
 
function decline() {
	$result = array();
	header( "Content-Type: application/json" );
	
	$post_ID = (int) $_POST['post_ID'];
	global $current_user, $user_ID;	get_currentuserinfo();
	
	
	
	$event = get_post($post_ID);
	if ($event->post_author == $user_ID) {
		update_post_meta($event->ID, 'volunteer', '');
		update_post_meta($event->ID, 'status', 'not assigned');
	} else {
		$result['errors'][] = 'It is not your event!';
	}
	
	echo json_encode($result);
	exit;
}
eks-wordpress
=============

EarnItKeepItSaveIt site, based on Wordpress 3.5.

### Migrating to a different server

1. Copy the contents of /wp-contents/uploads
1. Execute this SQL on your database server:

```sql
UPDATE db_options SET option_value = 'http://yourservername' WHERE option_name IN ('siteurl', 'home');
```
1. Activate the Configure SMTP plugin if needed to send mail.

### Debugging

To aid in debugging, you can do optionally do the following:

* Modify wp_debug_backtrace_summary() in wp-includes/functions.php as follows:

```php
function wp_debug_backtrace_summary( $ignore_class = null, $skip_frames = 0, $pretty = true ) {
  if ( version_compare( PHP_VERSION, '5.2.5', '>=' ) )
		$trace = debug_backtrace( false );
	else
		$trace = debug_backtrace();

	$caller = array();
	$check_class = ! is_null( $ignore_class );
	$skip_frames++; // skip this function

	foreach ( $trace as $call ) {
            
            // Start EKS hack 
            $file = empty($call['file']) ? '' : $call['file'] . ':' . $call['line'] . " ";       
            
		if ( $skip_frames > 0 ) {
			$skip_frames--;
		} elseif ( isset( $call['class'] ) ) {
			if ( $check_class && $ignore_class == $call['class'] )
				continue; // Filter out calls

			$caller[] = $file . "{$call['class']}{$call['type']}{$call['function']}";
		} else {
			if ( in_array( $call['function'], array( 'do_action', 'apply_filters' ) ) ) {
				$caller[] = $file . "{$call['function']}('{$call['args'][0]}')";
			} elseif ( in_array( $call['function'], array( 'include', 'include_once', 'require', 'require_once' ) ) ) {
				$caller[] = $file . $call['function'] . "('" . str_replace( array( WP_CONTENT_DIR, ABSPATH ) , '', $call['args'][0] ) . "')";
			} else {
				$caller[] = $file . $call['function'] . '()';
			}
		}
	}
	if ( $pretty )
		return join( '<br/>', array_reverse( $caller ) );
                // END EKS hack
	else
		return $caller;
}
```

* For SQL logging, modify query() in wp-includes/wp-db.php as follows:

```php
[...]
		$this->num_queries++;

		if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES )
                {
                    // EKS hack
                    $is_statement = preg_match( '/^\s*(insert|delete|update|replace)\s/i', $query );
                    $row_count = $is_statement ? mysql_affected_rows() : mysql_num_rows ( $this->result );
                    $this->queries[] = array( $query, $this->timer_stop(), $this->get_caller(),  $row_count);
                    error_log("[" . date(DATE_ATOM) . "] $query ($row_count)\n", 3, dirname(ini_get('error_log')) . '/' . 'sql.log');
                    // End EKS hack
                }
[...]
```

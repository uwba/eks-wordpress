eks-wordpress
=============

EarnItKeepItSaveIt site, based on Wordpress 3.5.



To aid in debugging, you can optionally modify wp_debug_backtrace_summary() in wp-includes/functions.php as follows:

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

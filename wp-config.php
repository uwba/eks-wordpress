<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
//define( 'DB_NAME', 'eks_sandbox' );
define( 'DB_NAME', 'wp_dev_eks' );


/** MySQL database username */
define( 'DB_USER', 'eksdevuser' );

/** MySQL database password */
//define( 'DB_PASSWORD', 'NY5yF2nhnErc3nfs' );
define( 'DB_PASSWORD', 'ccflutes' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/** Allow Backup Buddy to run cron. 
define('ALTERNATE_WP_CRON', true);*/
/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Q|^6hW;G?*04Y9rVs%yFD&Hj@k>C&2inM;L84*{&=#m%l7MoB#ZC!l|bY=j:XMj1');
define('SECURE_AUTH_KEY',  '<RTa)Sfxq3-|VcD.r>%F)lnS0_;z><W}O<L?rwHkfA:>{b&}l}S>fY1k{;YsP:,)');
define('LOGGED_IN_KEY',    '*,>_Bw$z Gm4H>f+@8PY4kd+/5wq[GJg+fb@Z_,50xzsyeu;CHP+2mc;%KXzpYxB');
define('NONCE_KEY',        '+og>gIY5^was-1L?>;xH>|ty|SLU+]:}+=-$QdSgUZ%:|s.3}gBKOl-23N-?|T8M');
define('AUTH_SALT',        '2:C*8z(61]Xgg?ffecP+%S&[:LP|4Y_@A7q8NtX:$TbT1#%5 37&TPyaCd/gH[<G');
define('SECURE_AUTH_SALT', '+7iFo)DOsvS`%{{y|%Amzuqe.`<L tE`rN6-Qv8w1:|UV-0G+6|h+&EJxBwuIF{g');
define('LOGGED_IN_SALT',   'af:Fi:^;N|JCB#_07%`dQrn3H&&:&:$rO$z>|F(agT$uZ<wlv*<+R46f[~eG=WuP');
define('NONCE_SALT',       'w&;[SbLg:{|#K)0%D_ g7<xjfR#puC Xcw&x|75@J.-z#.=Y|dc:/mf28B,<we7j');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'db_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);
define('SAVEQUERIES', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');


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
define('DB_NAME', 'nightowl_holly_dev_1');

/** MySQL database username */
define('DB_USER', 'nightowl_milica');

/** MySQL database password */
define('DB_PASSWORD', 'h@lly_m!lica');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'bRF.i9*,Q _L0FlM_SojWb_YAP,sh8g0|--C!P+Qpf>-W_}~pB4QlRR&mxs*8*iu');
define('SECURE_AUTH_KEY',  '0+1KeySb4(pf|ug>;H8>_vtP2-<=6_h$S1ngS).1A&Fg62x.W[2Q`vh(LI-t+fe/');
define('LOGGED_IN_KEY',    '9GZ3~QL|rWw9V--qkI 5xIdJTeK|/Q ;q+=7+paKe%hR3+F{4qk&s1[`u>j^<Obc');
define('NONCE_KEY',        ')cPZv/Y_`^j6w<KMEy*1n(7-d#jW~-?Q5J,c-67!u*.3wQ/2@1]u0t-W}WCIFcM:');
define('AUTH_SALT',        '{aQ-N`4k|x>+-3_QEWe(EkRG)R5oM1WWJY^lu;..Smn6e.[25it|${%)]w5s 92p');
define('SECURE_AUTH_SALT', '!$&-=-00.9ltr;1#Srl+&gE7{lpb4qZC`s+;[kl50_Oh#,7mN(Ql@P+Ss@#;PO@4');
define('LOGGED_IN_SALT',   '/U,|+PX9ZOh_2elwE{=00X,vAm-X=Tt$F7{Bd]whSd#<NHtoE:&ovJW7)9OPk+j^');
define('NONCE_SALT',       '@*W711VxA3$j{vaXQ?mU,FsQeXJG>$c@^t$~|tzg%ydS@Gu-?;|f-]rI|`( yp{<');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

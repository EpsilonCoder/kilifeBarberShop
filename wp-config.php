<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'brief_woordpress' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'm3qe%J5GFh).4xlF8yDs8UtV`<U+e3wlrX8$tHc6]o-f.Cvu>I<+,P(lu7b{Ej(*' );
define( 'SECURE_AUTH_KEY',  '_;Vl9{UG&=8;Q]R4M*BcXw.VWs(f{xUsRRdI6J3k4jL$9A,?JLO4>DG?gjWK]wnQ' );
define( 'LOGGED_IN_KEY',    '8??GcUSy})mp`-fNu#[a8xH$,,#&|oS%y1c<{,nw0j?&j?.7ckU 5KC8^RA)M{WG' );
define( 'NONCE_KEY',        'F{5~^_.{SWihv2OF?Upx>zy`:y[bGkU1Af3fqghylq($C66/B@N=UHq_NZT}?6kV' );
define( 'AUTH_SALT',        'Bq-Yxm8J!X1Q<KV@J[YFkcyU%G39]13jPFcg1Gf5K!=<2Z:MN<#s&DMKG>,x]*aJ' );
define( 'SECURE_AUTH_SALT', '7/Zqqeo^1C,Spw~ a:Ht!DSzWmFmDt]v:ocVqkk@)_mpbqhS[USP|Qu$c@}|1$)}' );
define( 'LOGGED_IN_SALT',   ',3}YU*N+H>*T==@P,U9W(-@0XhT/L$nA)GfI%k 5)XAVSiMZ=.?k,Q1__l&PT_&V' );
define( 'NONCE_SALT',       'KGS&s~+DB6xMb8@$SRYJ[43}[0zA[my8{eg&X)1tcNeDjYN-1V6J3}I,u9@QU~*A' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

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
define( 'DB_NAME', 'organics' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

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
define( 'AUTH_KEY',         ';&r(zTxvfA^iMno@S3I!.%$.zB7R`YxNK5[b@t~/hzUq1*IKX1tn$1;6n%Jrmosh' );
define( 'SECURE_AUTH_KEY',  ']==&*b6*;s%0f,@xN!{0>lVBs)pfj3kH~_0cAuk#Qx}aX55<nBWLRDI,g Qy95>|' );
define( 'LOGGED_IN_KEY',    '*&5WQ9+I8tve#,$34!IlFEj0AyBS.kKO>]z64N~`=UPY?0O~>@KIYKK1:#ltt*P3' );
define( 'NONCE_KEY',        '0)dbs5aHxiJP$6rJ37>log LYyh~CgH|& P4l-B$vy,N.W d^>Rg[ZPro>vX3vy#' );
define( 'AUTH_SALT',        '[xP4ULT4E0@[xWYF1[}8FI1 827U^5@xO:loO;&yNO;%XsqO# Wl</ }9$vTAk*:' );
define( 'SECURE_AUTH_SALT', ')CjWzaf_0Z]ziI9~@VM[*s.t&X%Ce`w{nX],Mb*7` f.[T/u|OX8Ru%p$IyfRBu9' );
define( 'LOGGED_IN_SALT',   '7S*,5jBh8*59&#K,odn.?mgPGNl5zEQ(ngpeuwfP&IkU%tBA}XK~Tyg.37 XzGd{' );
define( 'NONCE_SALT',       '*rH{?Ig[5y#p:(rR`x_LL2RYf7I0*j?SsxK7**]YgZNnk v|I$+8`r7X}(aq`Dt&' );

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
define( 'WP_DEBUG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

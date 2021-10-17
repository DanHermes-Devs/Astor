<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'digout_wp3' );

/** MySQL database username */
define( 'DB_USER', 'digout_wp3' );

/** MySQL database password */
define( 'DB_PASSWORD', 'K.hkUtyF21tDbTkUnHP85' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'uZ3GDw5TmwOj0cJX3GO1e7byaKM6ZyebxLkeGGbGYQepjDFXLQ0547qnfNShCpE8');
define('SECURE_AUTH_KEY',  '5cnAkiTBCzjmY8ZwRaX3m7cjfO0pPT1UrujgDmSYWHE56Lhkt5TAmkltRLhdwjG9');
define('LOGGED_IN_KEY',    'AxmTZAQkOYgSxNWiVgB3scQrs9yORL7SPMPuuYGxbyCzJybGCzuFA4rtVE61ptxR');
define('NONCE_KEY',        'LB2CUJZQ08BGaaGmxykJID3b3uMDspSgJ7B2xgCrLyFJZxmnzwADEeQUEpjpGsI9');
define('AUTH_SALT',        'Ctg072kjAhjhsw3vhTwA4zlL9ZPVvBO70ulsdDPgjp2iIo3l6g9gOEk0qbvdf1Ki');
define('SECURE_AUTH_SALT', 'lQwTHvwFwaUUVpYQtZsO3M5wLbJIc4gnt9FKa0yHyVDHUq5uPC9fsgTa3vZjOjYM');
define('LOGGED_IN_SALT',   '5espSTnd7w6L7Bl7u7BU7VlLbIQ6QUvZBPqa2N4pMtcTcyl0bwl8JnQhbzhS1usG');
define('NONCE_SALT',       'UfEqtZ0rCYvnnNXteQyFpIOo88BIV3D4i1cHczhhjdqWhRs2t3bN34yFgUDZQ5UJ');

/**
 * Other customizations.
 */
define('FS_METHOD','direct');
define('FS_CHMOD_DIR',0755);
define('FS_CHMOD_FILE',0644);
define('WP_TEMP_DIR',dirname(__FILE__).'/wp-content/uploads');

/**
 * Turn off automatic updates since these are managed externally by Installatron.
 * If you remove this define() to re-enable WordPress's automatic background updating
 * then it's advised to disable auto-updating in Installatron.
 */
define('AUTOMATIC_UPDATER_DISABLED', true);


/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );

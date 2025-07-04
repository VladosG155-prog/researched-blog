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
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - Docker environment variables ** //
/** The name of the database for WordPress */
define( 'DB_NAME', getenv('WORDPRESS_DB_NAME') ?: 'researched_db' );

/** Database username */
define( 'DB_USER', getenv('WORDPRESS_DB_USER') ?: 'researched_user' );

/** Database password */
define( 'DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD') ?: 'researched_password' );

/** Database hostname */
define( 'DB_HOST', getenv('WORDPRESS_DB_HOST') ?: 'mysql' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',         getenv('WORDPRESS_AUTH_KEY') ?: 'K&q8]D[r*9Lm#3vE@Nh7!wX$bF2+gJ5sZ0YuP6cT&8fK9qR#mL3xV@2bN&7wE!' );
define( 'SECURE_AUTH_KEY',  getenv('WORDPRESS_SECURE_AUTH_KEY') ?: 'M9qR#mL3xV@2bN&7wE!K&q8]D[r*9Lm#3vE@Nh7!wX$bF2+gJ5sZ0YuP6cT&8f' );
define( 'LOGGED_IN_KEY',    getenv('WORDPRESS_LOGGED_IN_KEY') ?: 'X$bF2+gJ5sZ0YuP6cT&8fK9qR#mL3xV@2bN&7wE!K&q8]D[r*9Lm#3vE@Nh7!w' );
define( 'NONCE_KEY',        getenv('WORDPRESS_NONCE_KEY') ?: 'Z0YuP6cT&8fK9qR#mL3xV@2bN&7wE!K&q8]D[r*9Lm#3vE@Nh7!wX$bF2+gJ5s' );
define( 'AUTH_SALT',        getenv('WORDPRESS_AUTH_SALT') ?: 'F2+gJ5sZ0YuP6cT&8fK9qR#mL3xV@2bN&7wE!K&q8]D[r*9Lm#3vE@Nh7!wX$b' );
define( 'SECURE_AUTH_SALT', getenv('WORDPRESS_SECURE_AUTH_SALT') ?: 'R#mL3xV@2bN&7wE!K&q8]D[r*9Lm#3vE@Nh7!wX$bF2+gJ5sZ0YuP6cT&8fK9q' );
define( 'LOGGED_IN_SALT',   getenv('WORDPRESS_LOGGED_IN_SALT') ?: 'V@2bN&7wE!K&q8]D[r*9Lm#3vE@Nh7!wX$bF2+gJ5sZ0YuP6cT&8fK9qR#mL3x' );
define( 'NONCE_SALT',       getenv('WORDPRESS_NONCE_SALT') ?: '7wE!K&q8]D[r*9Lm#3vE@Nh7!wX$bF2+gJ5sZ0YuP6cT&8fK9qR#mL3xV@2bN&' );

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */

// === PRODUCTION SETTINGS FOR RESEARCHED.XYZ ===
define('WP_HOME', 'https://researched.xyz/blog');
define('WP_SITEURL', 'https://researched.xyz/blog');

// === SECURITY ===
define('DISALLOW_FILE_EDIT', true);
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY', false);

// === PERFORMANCE ===
define('WP_CACHE', true);
define('WP_MEMORY_LIMIT', '256M');
define('COMPRESS_CSS', true);
define('COMPRESS_SCRIPTS', true);
define('CONCATENATE_SCRIPTS', false);

// === SSL ===
define('FORCE_SSL_ADMIN', true);
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}
// Важно: компенсируем stripPrefix `/blog` для WordPress
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/blog') === 0) {
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 5); // убираем "/blog"
    $_SERVER['SCRIPT_NAME'] = substr($_SERVER['SCRIPT_NAME'], 5);
    $_SERVER['PHP_SELF'] = substr($_SERVER['PHP_SELF'], 5);
}
// === ELASTICSEARCH (optional) ===
// define('ELASTICSEARCH_URL', 'http://your-elasticsearch-server:9200');

/* Add any custom values between this line and the "stop editing" line. */

// Если ты используешь HTTPS за прокси (Traefik), то:
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// Восстановим /blog в REQUEST_URI, чтобы WordPress знал, что сайт находится в /blog
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/blog') !== 0) {
    $_SERVER['REQUEST_URI'] = '/blog' . $_SERVER['REQUEST_URI'];
}

// Убедимся, что хост правильный (для редиректов и ссылок)
$_SERVER['HTTP_HOST'] = 'researched.xyz';
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

<?php

/* 
 * Copyright (C) 2015 travishill
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

// =================================================================================
// Define the type of server and load database info and local development parameters
// =================================================================================

$remote =   $_SERVER['REMOTE_ADDR'];    // this server's address.
$local  =   '127.0.0.1';                // your local machine, in case you develop on it.
$dev    =   '192.168.1.20';             // your dev machine, in case it's not $local

if ( $remote == $local || $remote == $dev ) {
    define('WP_ENV', 'dev');
} else {
    define('WP_ENV', 'prod');
}
if (WP_ENV == 'dev') {

    define('DB_NAME', 'database');                                                      // DB_NAME
    define('DB_USER', 'username');                                                      // DB_USER
    define('DB_PASSWORD', 'password');                                                  // DB_PASSWORD
    define('DB_HOST', 'localhost');                                                     // DB_HOST

    define('WP_DEBUG', true );                                                          // Error Reporting
    define('WP_LOCAL_DEV', true );

} else {

    define('DB_NAME', 'database');                                                      // DB_NAME
    define('DB_USER', 'username');                                                      // DB_USER
    define('DB_PASSWORD', 'password');                                                  // DB_PASSWORD
    define('DB_HOST', 'localhost');                                                     // DB_HOST
    
    define('AUTOMATIC_UPDATER_DISABLED', true );                                        // disable automatic updates on production server.
    define('DISALLOW_FILE_MODS', true );                                                // disable the file editor, automatic updates, and installing plugins/themes from within WP on production server.


}

$table_prefix  = 'wp_';
  
define( 'WPLANG', '' );

define('DB_CHARSET', 'utf8');                                                           // DB_CHARSET
define('DB_COLLATE', '');                                                               // DB_COLLATE

define('WP_SITEURL', 'http://' . $_SERVER['SERVER_NAME'] . '/core');                    // where your WordPress core files reside.
define('WP_HOME',    'http://' . $_SERVER['SERVER_NAME']);                              // where to reach your WordPress blog.
define('WP_CONTENT_DIR', dirname(__FILE__) . '/public_html/lib' );                      // move the wp-content directory outside of the WordPress application directory.
define('WP_CONTENT_URL', 'http://' . $_SERVER['SERVER_NAME'] . '/lib');                 // move the wp-content directory outside of the WordPress application directory.

define('DISALLOW_FILE_EDIT', true );                                                    // disable the file editor.
define('WP_POST_REVISIONS', false );                                                    // disable post revisions.

if ( WP_DEBUG ) {
    define('WP_DEBUG_LOG', true );                                                      // stored in wp-content/debug.log
    define('WP_DEBUG_DISPLAY', true );
    define('SCRIPT_DEBUG', true );                                                      // if you are planning on modifying some of WP built-in JS or CSS
    define('SAVEQUERIES', true );
    @ini_set('display_errors', 0 );
}

// =================================================================================
// Salts, for security
// Grab these from: https://api.wordpress.org/secret-key/1.1/salt
// =================================================================================
define( 'AUTH_KEY',         'put your unique phrase here' );
define( 'SECURE_AUTH_KEY',  'put your unique phrase here' );
define( 'LOGGED_IN_KEY',    'put your unique phrase here' );
define( 'NONCE_KEY',        'put your unique phrase here' );
define( 'AUTH_SALT',        'put your unique phrase here' );
define( 'SECURE_AUTH_SALT', 'put your unique phrase here' );
define( 'LOGGED_IN_SALT',   'put your unique phrase here' );
define( 'NONCE_SALT',       'put your unique phrase here' );

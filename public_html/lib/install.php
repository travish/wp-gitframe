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


!defined( 'ABSPATH' ) AND exit;

/*
  Plugin Name: Custom Installation Script
*/

// Fix media directory location.
function wp_install_defaults( $user_id )
{
    global $wpdb, $wp_rewrite, $current_site, $table_prefix;

    // Fix upload directory location.
    if (WP_ENV == 'dev'){
        update_option( 'upload_path', untrailingslashit( str_replace( 'core', 'lib\uploads', ABSPATH ) ) );
    } else {
        update_option( 'upload_path', untrailingslashit( str_replace( 'core', 'lib/uploads', ABSPATH ) ) );
    }
    
    update_option( 'upload_url_path', home_url( '/lib/uploads' ) );
    
}
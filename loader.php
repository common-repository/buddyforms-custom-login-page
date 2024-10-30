<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Plugin Name: BuddyForms Custom Login Page
 * Plugin URI: https://themekraft.com/products/custom-login/
 * Description: Select a Custom Login Page
 * Version: 1.1.14
 * Author: ThemeKraft
 * Author URI: https://themekraft.com/
 * License: GPLv2 or later
 * Network: false
 * Text Domain: buddyforms
 * Svn: buddyforms-custom-login-page
 *
 * ****************************************************************************
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * ***************************************************************************
 */
// BuddyForms Members init
add_action( 'init', 'buddyforms_custom_login_init' );
function buddyforms_custom_login_init()
{
    require dirname( __FILE__ ) . '/includes/admin/custom-login-settings.php';
}

add_filter(
    'buddyforms_login_form_redirect_url',
    'buddyforms_custom_login_redirect_url',
    10,
    1
);
function buddyforms_custom_login_redirect_url( $redirect )
{
    $custom_login_settings = get_option( 'buddyforms_custom_login_settings' );
    $redirect_page = ( empty($custom_login_settings['redirect_page']) && $custom_login_settings['redirect_page'] === 'default' ? '' : $custom_login_settings['redirect_page'] );
    $display_login_form = ( empty($custom_login_settings['display_login_form']) ? '' : $custom_login_settings['display_login_form'] );
    $caller = ( !empty($_REQUEST['caller']) ? sanitize_key( $_REQUEST['caller'] ) : '' );
    $caller_redirect = empty($caller) || $caller === 'direct';
    
    if ( !empty($redirect_page) && !empty($display_login_form) && $caller_redirect ) {
        $redirect_page_url = get_permalink( $redirect_page );
        if ( !empty($redirect_page_url) ) {
            return $redirect_page_url;
        }
    }
    
    return $redirect;
}

add_action( 'template_redirect', 'buddyforms_custom_login_page' );
function buddyforms_custom_login_page()
{
    global  $pagenow ;
    if ( is_user_logged_in() ) {
        return;
    }
    $custom_login_settings = get_option( 'buddyforms_custom_login_settings' );
    $login_page = ( empty($custom_login_settings['login_page']) ? 'none' : $custom_login_settings['login_page'] );
    $register_page = ( empty($custom_login_settings['register_page']) ? 'none' : $custom_login_settings['register_page'] );
    $redirect_logged_off_user = ( empty($custom_login_settings['redirect_logged_off_user']) ? 'No' : $custom_login_settings['redirect_logged_off_user'] );
    $public_accessible_pages = ( empty($custom_login_settings['public_accessible_pages']) ? array() : $custom_login_settings['public_accessible_pages'] );
    $public_accessible_post_type = ( empty($custom_login_settings['public_accessible_post_types']) ? array() : $custom_login_settings['public_accessible_post_types'] );
    
    if ( empty($login_page) || $login_page == 'default' || $login_page == 'none' ) {
        $new_login_page_url = wp_login_url();
    } else {
        $new_login_page_url = get_permalink( $login_page );
    }
    
    if ( array_key_exists( 'use_custom_redirect_url', $custom_login_settings ) ) {
        if ( $redirect_logged_off_user == 'Yes' && !empty($custom_login_settings['set_custom_redirect_url']) ) {
            $new_login_page_url = $custom_login_settings['set_custom_redirect_url'];
        }
    }
    
    if ( !is_user_logged_in() && $redirect_logged_off_user != 'No' ) {
        if ( !get_the_ID() || in_array( get_the_ID(), $public_accessible_pages ) ) {
            return;
        }
        if ( in_array( get_post_type(), $public_accessible_post_type ) ) {
            return;
        }
        if ( is_page( $public_accessible_pages ) ) {
            return;
        }
        if ( isset( $_SERVER['REQUEST_URI'] ) && strpos( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'activate' ) !== false ) {
            return;
        }
        $buddyforms_registration_page = get_option( 'buddyforms_registration_page' );
        if ( !empty($buddyforms_registration_page) ) {
            if ( is_page( $buddyforms_registration_page ) ) {
                return;
            }
        }
        if ( is_page( $login_page ) ) {
            return;
        }
        if ( is_page( $register_page ) ) {
            return;
        }
        if ( $pagenow == 'wp-login.php' && isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] == 'GET' ) {
            if ( !(isset( $_GET['action'] ) && $_GET['action'] == 'lostpassword' || isset( $_GET['action'] ) && $_GET['action'] == 'rp') ) {
                
                if ( !(isset( $_GET['checkemail'] ) && $_GET['checkemail'] == 'confirm') ) {
                    wp_redirect( $new_login_page_url );
                    exit;
                }
            
            }
        }
        wp_redirect( $new_login_page_url );
        exit;
    }

}

add_action( 'init', 'buddyforms_custom_login_page_init' );
function buddyforms_custom_login_page_init()
{
    global  $pagenow ;
    $custom_login_settings = get_option( 'buddyforms_custom_login_settings' );
    $login_page = ( empty($custom_login_settings['login_page']) ? 'none' : $custom_login_settings['login_page'] );
    
    if ( empty($login_page) || $login_page == 'default' || $login_page == 'none' ) {
        return;
    } else {
        $new_login_page_url = get_permalink( $login_page );
    }
    
    if ( isset( $_GET['action'] ) && $_GET['action'] == 'logout' ) {
        return;
    }
    if ( isset( $_GET['action'] ) && $_GET['action'] == 'switch_to_user' ) {
        return;
    }
    if ( isset( $_GET['action'] ) && $_GET['action'] == 'switch_to_olduser' ) {
        return;
    }
    if ( isset( $_GET['action'] ) && $_GET['action'] == 'confirm_admin_email' ) {
        return;
    }
    if ( $pagenow == 'wp-login.php' && isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] == 'GET' ) {
        if ( !(isset( $_GET['action'] ) && $_GET['action'] == 'lostpassword' || isset( $_GET['action'] ) && $_GET['action'] == 'rp') ) {
            
            if ( !(isset( $_GET['checkemail'] ) && $_GET['checkemail'] == 'confirm') ) {
                wp_redirect( $new_login_page_url );
                exit;
            }
        
        }
    }
}

add_filter( 'login_form_bottom', 'buddyforms_site_register_link', 9999 );
function buddyforms_site_register_link( $wp_login_form )
{
    $custom_login_settings = get_option( 'buddyforms_custom_login_settings' );
    $register_page = ( empty($custom_login_settings['register_page']) ? 'none' : $custom_login_settings['register_page'] );
    $login_page = ( empty($custom_login_settings['login_page']) ? 'none' : $custom_login_settings['login_page'] );
    if ( get_the_ID() != $login_page ) {
        return $wp_login_form;
    }
    
    if ( empty($register_page) || $register_page == 'default' || $register_page == 'none' ) {
        $url = wp_registration_url();
    } else {
        $url = get_permalink( $register_page );
    }
    
    $wp_login_form = '<a href="' . $url . '">' . __( 'Register', 'buddyforms' ) . '</a> ';
    $lost_password_url = apply_filters( 'buddyforms_custom_login_lost_password_url', wp_lostpassword_url() );
    $wp_login_form .= '<a href="' . esc_url( $lost_password_url ) . '">' . __( 'Lost Password?', 'buddyforms' ) . '</a> ';
    return $wp_login_form;
}

add_filter( 'the_content', 'buddyforms_custom_login_the_content' );
function buddyforms_custom_login_the_content( $content )
{
    $custom_login_settings = get_option( 'buddyforms_custom_login_settings' );
    $login_page = ( empty($custom_login_settings['login_page']) ? '' : $custom_login_settings['login_page'] );
    $display_login_form = ( empty($custom_login_settings['display_login_form']) ? 'overwrite' : $custom_login_settings['display_login_form'] );
    $redirect_page = ( empty($custom_login_settings['redirect_page']) ? '' : $custom_login_settings['redirect_page'] );
    if ( get_the_ID() != $login_page ) {
        return $content;
    }
    
    if ( empty($redirect_page) || $redirect_page == 'default' ) {
        $form = do_shortcode( '[bf_login_form title=" "]' );
    } else {
        $redirect_url = get_permalink( $redirect_page );
        $form = do_shortcode( '[bf_login_form title=" " redirect_url="' . $redirect_url . '"]' );
    }
    
    if ( $display_login_form == 'overwrite' ) {
        return $form;
    }
    if ( $display_login_form == 'above' ) {
        return $form . $content;
    }
    if ( $display_login_form == 'under' ) {
        return $content . $form;
    }
    return $content;
}

add_filter( 'buddyforms_loggin_settings', 'buddyforms_custom_login_remember_me_as_default' );
function buddyforms_custom_login_remember_me_as_default( $settings )
{
    $bf_custom_login_settings = get_option( 'buddyforms_custom_login_settings' );
    $login_page = ( !empty($bf_custom_login_settings['login_page']) ? (int) $bf_custom_login_settings['login_page'] : '' );
    $remember_me_as_default = ( !empty($bf_custom_login_settings['remember_me_as_default']) ? true : false );
    if ( get_the_ID() === $login_page && $remember_me_as_default === true ) {
        $settings['value_remember'] = true;
    }
    return $settings;
}

// Create a helper function for easy SDK access.
function buddyforms_clp_fs()
{
    global  $buddyforms_clp_fs ;
    
    if ( !isset( $buddyforms_clp_fs ) ) {
        // Include Freemius SDK.
        // Include Freemius SDK.
        
        if ( file_exists( dirname( dirname( __FILE__ ) ) . '/buddyforms/includes/resources/freemius/start.php' ) ) {
            // Try to load SDK from parent plugin folder.
            require_once dirname( dirname( __FILE__ ) ) . '/buddyforms/includes/resources/freemius/start.php';
        } elseif ( file_exists( dirname( dirname( __FILE__ ) ) . '/buddyforms-premium/includes/resources/freemius/start.php' ) ) {
            // Try to load SDK from premium parent plugin folder.
            require_once dirname( dirname( __FILE__ ) ) . '/buddyforms-premium/includes/resources/freemius/start.php';
        }
        
        $buddyforms_clp_fs = fs_dynamic_init( array(
            'id'             => '1924',
            'slug'           => 'buddyforms-custom-login-page',
            'type'           => 'plugin',
            'public_key'     => 'pk_9e440e4e95f7a9556ae3c03c4c221',
            'is_premium'     => false,
            'has_paid_plans' => false,
            'parent'         => array(
            'id'         => '391',
            'slug'       => 'buddyforms',
            'public_key' => 'pk_dea3d8c1c831caf06cfea10c7114c',
            'name'       => 'BuddyForms',
        ),
            'menu'           => array(
            'first-path' => 'edit.php?post_type=buddyforms&page=buddyforms_welcome_screen',
            'support'    => false,
        ),
            'is_live'        => true,
        ) );
    }
    
    return $buddyforms_clp_fs;
}

function buddyforms_clp_fs_is_parent_active_and_loaded()
{
    // Check if the parent's init SDK method exists.
    return function_exists( 'buddyforms_core_fs' );
}

function buddyforms_clp_fs_is_parent_active()
{
    $active_plugins = get_option( 'active_plugins', array() );
    
    if ( is_multisite() ) {
        $network_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
        $active_plugins = array_merge( $active_plugins, array_keys( $network_active_plugins ) );
    }
    
    foreach ( $active_plugins as $basename ) {
        if ( 0 === strpos( strtolower( $basename ), 'buddyforms/' ) || 0 === strpos( strtolower( $basename ), 'buddyforms-premium/' ) ) {
            return true;
        }
    }
    return false;
}

function buddyforms_custom_login_need_buddyforms()
{
    ?>
	<style>
		.buddyforms-notice label.buddyforms-title {
			background: rgba(0, 0, 0, 0.3);
			color: #fff;
			padding: 2px 10px;
			position: absolute;
			top: 100%;
			bottom: auto;
			right: auto;
			-moz-border-radius: 0 0 3px 3px;
			-webkit-border-radius: 0 0 3px 3px;
			border-radius: 0 0 3px 3px;
			left: 10px;
			font-size: 12px;
			font-weight: bold;
			cursor: auto;
		}

		.buddyforms-notice .buddyforms-notice-body {
			margin: .5em 0;
			padding: 2px;
		}

		.buddyforms-notice.buddyforms-title {
			margin-bottom: 30px !important;
		}

		.buddyforms-notice {
			position: relative;
		}
	</style>
	<div class="error buddyforms-notice buddyforms-title">
		<label class="buddyforms-title">BuddyForms Custom Login Page</label>
		<div class="buddyforms-notice-body">
			<b>Oops...</b> <a href="https://themekraft.com/products/buddyforms-custom-login/" target="_blank">BuddyForms Custom Login Page</a> cannot run without <a target="_blank" href="https://themekraft.com/buddyforms/">BuddyForms</a>.
		</div>
	</div>
	<?php 
}

function buddyforms_clp_fs_init()
{
    
    if ( buddyforms_clp_fs_is_parent_active_and_loaded() ) {
        // Init Freemius.
        buddyforms_clp_fs();
        // Parent is active, add your init code here.
    } else {
        add_action( 'admin_notices', 'buddyforms_custom_login_need_buddyforms' );
    }

}


if ( buddyforms_clp_fs_is_parent_active_and_loaded() ) {
    // If parent already included, init add-on.
    buddyforms_clp_fs_init();
} elseif ( buddyforms_clp_fs_is_parent_active() ) {
    // Init add-on only after the parent is loaded.
    add_action( 'buddyforms_core_fs_loaded', 'buddyforms_clp_fs_init' );
} else {
    // Even though the parent is not activated, execute add-on for activation / uninstall hooks.
    buddyforms_clp_fs_init();
}

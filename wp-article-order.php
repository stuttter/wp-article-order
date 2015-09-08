<?php

/**
 * Plugin Name: WP Article Order
 * Plugin URI:  http://wordpress.org/plugins/wp-article-order/
 * Description: Move articles to the end of post titles
 * Author:      John James Jacoby
 * Author URI:  https://profiles.wordpress.org/johnjamesjacoby/
 * Version:     0.1.1
 * License:     GPL v2 or later
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Look for articles in post titles and move them to the end
 *
 * @todo Every other language, prefix vs. suffix, etc...
 * @since 0.1.0
 *
 * @param array $data
 */
function wp_article_order_save_post( $data = '' ) {

	// Bail if no title
	if ( empty( $data['post_title'] ) ) {
		return $data;
	}

	// Short circuit
	if ( ! apply_filters( 'wp_article_order_pre_save_post', $data ) ) {
		return $data;
	}

	// Get articles
	$lang     = get_site_option( 'WPLANG', 'en_US' );
	$articles = wp_article_order_get_articles( $lang );

	// Explode words into an array
	$words = explode( ' ', $data['post_title'] );

	// Look for leading article
	if ( in_array( strtolower( $words[0] ), $articles ) ) {

		// Save the first word
		$first = $words[0];

		// Add a comma to the last word
		$words[ count( $words ) - 1 ] .= ',';

		// Unset the first word
		unset( $words[0] );

		// Append the first word
		$words[] = $first;

		// Implode them back together
		$data['post_title'] = implode( ' ', $words );
	}

	// Return maybe modified data
	return $data;
}
add_filter( 'wp_insert_post_data',       'wp_article_order_save_post' );
add_filter( 'wp_insert_attachment_data', 'wp_article_order_save_post' );

/**
 * Return array of articles
 *
 * @todo Every other language
 *
 * @since 0.1.0
 *
 * @return array
 */
function wp_article_order_get_articles( $locale = 'en_US' ) {

	switch ( $locale ) {

		// English
		case 'en_US' :
		default :
			$retval = array( 'the', 'a', 'an', 'some' );
			break;
	}

	return apply_filters( 'wp_article_order_get_articles', $retval, $locale );
}

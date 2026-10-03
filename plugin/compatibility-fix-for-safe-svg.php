<?php
/**
 * Plugin Name:       Compatibility Fix for Safe SVG
 * Plugin URI:        https://github.com/jgorres/compatibility-fix-for-safe-svg-plugin
 * Description:       Fixes a conflict between "Safe SVG" and "Enable Media Replace" that prevents replacing existing media items with SVG files. Registers the required MIME-type filters globally, but only when Safe SVG is active, so that no unsanitized SVG upload path is opened.
 * Version:           1.1.3
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Author:            Jörn Gorres
 * Author URI:        https://joern.gorres.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       compatibility-fix-for-safe-svg
 * Domain Path:       /languages
 *
 * @package Compatibility_Fix_For_Safe_SVG
 */

defined( 'ABSPATH' ) || exit;

/**
 * Check whether the "Safe SVG" plugin (by 10up) is active.
 *
 * Important safety guard: the `upload_mimes` whitelist is extended to include
 * SVG only when Safe SVG is present and its sanitizer is hooked to
 * `wp_handle_upload_prefilter` / `wp_handle_sideload_prefilter`. Without
 * Safe SVG, this plugin would otherwise open an unsanitized SVG upload path.
 *
 * @return bool
 */
function compatibility_fix_for_safe_svg_is_active(): bool {
	return class_exists( 'SafeSvg\\safe_svg' ) || class_exists( 'safe_svg' );
}

/**
 * Add SVG/SVGZ to the list of allowed MIME types.
 *
 * The `upload_mimes` filter applies to every upload, including the direct
 * `wp_check_filetype_and_ext()` call that "Enable Media Replace" makes from
 * its own submenu page (which bypasses `wp_handle_upload`).
 *
 * No capability check is performed here on purpose: this filter is queried
 * by core only inside upload-handling code paths that are themselves gated
 * by capability checks. Removing the redundant check makes the filter work
 * in non-user contexts as well (WP-CLI, Cron, REST sideloads).
 *
 * @param array<string,string> $mimes Allowed MIME types.
 * @return array<string,string>
 */
add_filter(
	'upload_mimes',
	static function ( $mimes ) {
		if ( ! is_array( $mimes ) ) {
			return $mimes;
		}
		if ( ! compatibility_fix_for_safe_svg_is_active() ) {
			return $mimes;
		}
		$mimes['svg']  = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';
		return $mimes;
	}
);

/**
 * Correct the file-type check for SVG uploads.
 *
 * `wp_check_filetype_and_ext()` performs an additional real MIME check via
 * `finfo_file()`. For SVG files this often yields "application/xml",
 * "text/xml" or "text/plain", causing `ext = false` and the well-known
 * "Sorry, this file type is not permitted for security reasons." error.
 *
 * Whenever the extension is .svg or .svgz, we restore `type` and `ext`
 * to the correct values. Safe SVG itself ships the same filter, but only
 * registers it on certain admin-page hooks; those hooks do not reliably
 * fire for the EMR replace page in practice.
 *
 * @param array       $data     ['ext' => string, 'type' => string, 'proper_filename' => string].
 * @param string|null $file     Full path to the file (unused).
 * @param string|null $filename File name.
 * @param array|null  $mimes    Allowed MIME types (unused).
 * @return array
 */
add_filter(
	'wp_check_filetype_and_ext',
	static function ( $data, $file, $filename, $mimes ) {
		unset( $file, $mimes );

		if ( ! is_array( $data ) ) {
			return $data;
		}
		if ( ! compatibility_fix_for_safe_svg_is_active() ) {
			return $data;
		}

		$ext = '';
		if ( ! empty( $data['ext'] ) ) {
			$ext = strtolower( (string) $data['ext'] );
		}
		if ( '' === $ext && is_string( $filename ) ) {
			$ext = strtolower( (string) pathinfo( $filename, PATHINFO_EXTENSION ) );
		}

		if ( 'svg' === $ext || 'svgz' === $ext ) {
			$data['type'] = 'image/svg+xml';
			$data['ext']  = $ext;
		}

		return $data;
	},
	10,
	4
);

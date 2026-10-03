=== Compatibility Fix for Safe SVG ===
Contributors: jgorres
Tags: svg, enable-media-replace, safe-svg, mime, compatibility
Requires at least: 6.5
Tested up to: 7.1
Stable tag: 1.1.3
Requires PHP: 7.4
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Fixes a conflict between "Safe SVG" and "Enable Media Replace" when replacing existing media files with SVG uploads.

== Description ==

**Problem:**

With "Safe SVG" alone, uploading SVG files into the WordPress Media Library works fine. As soon as you try to replace an existing file with an SVG via the **"Enable Media Replace"** plugin, the operation aborts with the message *"Sorry, this file type is not permitted for security reasons."*.

Root cause: EMR calls `wp_check_filetype_and_ext()` directly from its own submenu (`upload.php?page=enable-media-replace/...`) without going through the standard upload path (`wp_handle_upload`). Safe SVG, however, registers its MIME-type fix only on certain admin page hooks — and those do not reliably fire on the EMR replace page in practice.

**Solution:**

This plugin registers the two required filters (`upload_mimes`, `wp_check_filetype_and_ext`) globally so that SVG uploads work through EMR as well.

**Safety guard:**

So that this plugin does not open an unsanitized SVG upload path, the filters are active only when the "Safe SVG" plugin itself is active. Safe SVG then handles the SVG sanitization on `wp_handle_upload_prefilter` / `wp_handle_sideload_prefilter`. Without an active Safe SVG, SVG remains non-uploadable.

== Installation ==

1. In the WordPress admin go to Plugins → Add New → Upload Plugin and upload the ZIP file.
2. Activate the plugin.
3. Make sure that "Safe SVG" and "Enable Media Replace" are also active.
4. Replace an existing media item with an SVG file via "Replace media" — it now works.

== Frequently Asked Questions ==

= Do I really need Safe SVG? =

Yes. Without Safe SVG the filters of this plugin stay inactive. This is intentional: SVG uploads without a sanitizer would be an XSS risk (embedded JavaScript / SVG smuggling).

= Does the plugin also work with other replace plugins? =

Yes. The two filters are generic and apply to any plugin that calls `wp_check_filetype_and_ext()` directly.

= Does the plugin also work in WP-CLI / Cron / REST sideloads? =

Yes. As of version 1.1.0 the filters no longer perform a capability check, so SVG uploads also work in contexts without a logged-in user (for example programmatic sideloads via REST or WP-CLI). Security is still enforced through the Safe-SVG-active guard and Safe SVG's sanitization on `wp_handle_upload_prefilter`.

= Is the plugin still needed once EMR is updated and fixes the conflict itself? =

No. The plugin can be deactivated and removed without leftovers (no postmeta, no options, no cron).

== Changelog ==

= 1.1.3 =
* Plugin header: `Plugin URI` links to the source code repository on GitHub; `Tested up to` is declared in the readme only. No code changes.

= 1.1.2 =
* Compatibility: `Tested up to` raised to WordPress 7.1. No code changes.
* Assets: cosmetic corrections to the banner graphics on WordPress.org.

= 1.1.1 =
* Metadata: `Contributors` slug in `readme.txt` changed from `joerngorres` to `jgorres` to match the WordPress.org user name. No code changes.

= 1.1.0 =
* Plugin renamed from "Safe SVG Upload Fix" to "Compatibility Fix for Safe SVG" (slug: `compatibility-fix-for-safe-svg`). Reason: the WP.org Plugin Directory does not accept plugin names that start with the name of another plugin; the "for Safe SVG" pattern is explicitly allowed.
* Function prefix `ssuf_` → `compatibility_fix_for_safe_svg_`.
* Removed the capability check (`current_user_can( 'upload_files' )`) from the filter callbacks. As a result SVG uploads now also work in non-user contexts (WP-CLI, Cron, REST sideloads). Security is still guaranteed by the Safe-SVG-active guard and Safe SVG's sanitizer hook.
* Plugin header description translated to English.
* Added a `languages/index.php` stub against direct access.
* Translated all `readme.txt` sections (Installation, FAQ, Changelog, Upgrade Notice) to English.

= 1.0.1 =
* Plugin Check compliance, no functional changes: Short Description and `== Description ==` translated to English.

= 1.0.0 =
* Initial release.
* Registers the `upload_mimes` and `wp_check_filetype_and_ext` filters globally, gated by an active-Safe-SVG check.

== Upgrade Notice ==

= 1.1.2 =
Metadata-only update: `Tested up to` raised to WordPress 7.1. No functional changes.

= 1.1.1 =
Metadata-only update: `Contributors` slug in `readme.txt` adjusted to match the WordPress.org user name. No functional changes.

= 1.1.0 =
Plugin renamed for the WP.org submission; capability check removed so that SVG uploads also work in WP-CLI / Cron / REST contexts. Manual reactivation in the plugins list required (the slug changed).

= 1.0.1 =
Pure documentation update: readme description translated to English for Plugin Check compliance. No functional changes.

= 1.0.0 =
Initial release — fixes the SVG-replace conflict between Safe SVG and Enable Media Replace.

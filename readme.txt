=== Spam Slayer for Gravity Forms ===
Contributors: miiira
Donate link: https://github.com/sponsors/LeMiira
Tags: gravity forms, spam, cleanup, regex, gravity
Requires at least: 5.0
Tested up to: 7.0
Stable tag: 1.5
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Advanced spam cleanup and regex-powered spam detection for Gravity Forms entries.

== Description ==

Spam Slayer for Gravity Forms is a lightweight cleanup utility for Gravity Forms that helps you detect, review, mark, and remove spam-like submissions already stored in your database.

Instead of blocking spam during submission, the plugin works as a post-submission analysis tool using regex-powered pattern matching.

Perfect for:
* Bot attack cleanup
* Fake name detection
* Spam audits
* Bulk spam removal
* Regex-based moderation workflows

== Features ==

* Regex-powered spam detection
* Built-in spam detection presets
* Custom regex support
* Scan specific forms and fields
* Preview suspicious entries before deletion
* Test scan mode
* Bulk spam marking
* Bulk spam deletion
* Track which pages/posts are using Gravity Forms (shortcodes or Gutenberg blocks)
* Lightweight admin interface
* Native WordPress UI
* No telemetry or tracking
* No external API calls
* Minimal performance impact

== Installation ==

1. Upload the `spam-slayer-for-gravity-forms` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Access the plugin via the 'Spam Slayer Tools' menu in your WordPress dashboard.

== Frequently Asked Questions ==

= Does this plugin block spam submissions? =
No, this plugin is designed for post-submission cleanup and audit of existing entries, not for preventing spam in real time.

= Can I use custom regex rules? =
Yes, you can write custom regular expressions in the admin dashboard to search through submissions.

== Screenshots ==

1. The Spam Slayer for Gravity Forms dashboard under the Spam Slayer Tools menu.

== Upgrade Notice ==

= 1.5 =
Renamed plugin to Spam Slayer for Gravity Forms, upgraded admin interface with a new card-based layout, and added new plugin icons/banners.

= 1.4 =
Restructured menus to Spam Slayer Tools, added activation/runtime safety checks for Gravity Forms, and cleaned up PHP coding standard issues.

== Changelog ==

= 1.5 (2026-06-01) =
* Renamed plugin to Spam Slayer for Gravity Forms to comply with WordPress directory trademark guidelines.
* Completely overhauled the admin settings UI with a modern, card-based flexbox layout for better usability and aesthetics.
* Added new high-quality plugin icons and banners to the assets directory for the WordPress directory listing.
* Updated "GF Usage" page title to "Pages which uses gravity forms" and improved detection to find forms used in Elementor widgets (e.g. bdt-gravity-forms).

= 1.4 (2026-05-26) =
* Restructured admin interface into a custom Spam Slayer Tools top-level menu containing Spam Slayer for Gravity Forms and Usage submenu pages.
* Added checks on plugin activation, admin panels, and AJAX routines to gracefully handle scenarios when Gravity Forms is not installed or active, preventing any fatal errors.
* Resolved prefixing errors, added missing translator comment annotations, formatted raw error logs, and updated readme configurations to match WordPress repository requirements.

= 1.3 (2026-05-26) =
* Added a Buy Me a Coffee donate link directly to the plugin action links on the main Plugins directory screen.
* Added a Support card in the admin tools panel featuring links to GitHub Sponsors and Buy Me a Coffee.

= 1.2 (2026-05-26) =
* Added a Gravity Forms Usage page under the Tools management menu to see which posts and pages are using each Gravity Form (via shortcodes or Gutenberg blocks).

= 1.1 (2026-05-26) =
* Fixed critical bug where undefined wp_error_log function could cause fatal PHP crash when logging errors.
* Implemented server-side regex validation check before running preg_match to prevent PHP runtime warnings or crashes on invalid patterns.
* Removed inline Javascript script block from the admin dashboard to comply with modern security guidelines.
* Optimized Gravity Forms loop performance by caching the form object retrieval outside the loop, avoiding redundant database lookups.
* Localized all remaining hardcoded strings in the admin interface and JavaScript logic.
* Bumped minimum required PHP version to 7.4.

= 1.0 (2026-05-25) =
* Initial release.

# Spam Slayer for Gravity Forms

Advanced spam cleanup and regex-powered spam detection for Gravity Forms entries.

- **Contributors:** miiira
- **Donate link:** [Buy Me A Coffee](https://www.buymeacoffee.com/miiiira)
- **Tags:** gravity forms, spam, cleanup, regex, gravity
- **Requires at least:** 5.0
- **Tested up to:** 7.0
- **Stable tag:** 1.5
- **Requires PHP:** 7.4
- **License:** GPLv2 or later
- **License URI:** [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)

## Description

<p>
  <img src="https://img.shields.io/badge/WordPress-%23117B85.svg?style=for-the-badge&logo=wordpress&logoColor=white" alt="WordPress" />
  <img src="https://img.shields.io/badge/PHP-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
  <img src="https://img.shields.io/badge/Gravity_Forms-%23FF6347.svg?style=for-the-badge&logo=wordpress&logoColor=white" alt="Gravity Forms" />
  <img src="https://img.shields.io/badge/Vanilla_JS-%23F7DF1E.svg?style=for-the-badge&logo=javascript&logoColor=black" alt="Vanilla JS" />
  <img src="https://img.shields.io/badge/CSS3-%231572B6.svg?style=for-the-badge&logo=css3&logoColor=white" alt="CSS3" />
  <img src="https://img.shields.io/badge/License_GPLv2-%2300599C.svg?style=for-the-badge&logo=open-source-initiative&logoColor=white" alt="License GPLv2" />
</p>

---

## What Is It?

**Spam Slayer for Gravity Forms** is a lightweight cleanup utility for Gravity Forms that helps you detect, review, mark, and remove spam-like submissions already stored in your database.

Instead of blocking spam during submission, the plugin works as a post-submission analysis tool using regex-powered pattern matching.

Perfect for:
- bot attack cleanup
- fake name detection
- spam audits
- bulk spam removal
- regex-based moderation workflows

---

## Features

- Regex-powered spam detection
- Built-in spam detection presets
- Custom regex support
- Scan specific forms and fields
- Preview suspicious entries before deletion
- Test scan mode
- Bulk spam marking
- Bulk spam deletion
- Track which pages/posts are using Gravity Forms (shortcodes or Gutenberg blocks)
- Lightweight admin interface
- Native WordPress UI
- No telemetry or tracking
- No external API calls
- Minimal performance impact

---

## Example Use Cases

### Clean Up Bot Attacks

Detect and remove thousands of spam submissions after automated attacks.

### Detect Fake Names

Identify entries containing suspicious random strings or malformed content.

### Filter Promo Spam

Locate repetitive promotional phrases, URLs, or injected text.

### Advanced Regex Filtering

Create custom regex workflows tailored to your own moderation strategy.

---

## Important Note

This plugin does **NOT** prevent spam during submission.

It is designed specifically for:
- spam detection
- spam analysis
- spam cleanup
- post-submission moderation

For prevention, combine it with:
- reCAPTCHA
- Cloudflare Turnstile
- Gravity Forms anti-spam tools
- honeypot fields

---

## Installation

```bash
cd wp-content/plugins
git clone https://github.com/LeMiira/GForm-Spam-Slayer.git
```

---

## Changelog

### 1.5 (2026-06-01)
- **Plugin Rename:** Renamed plugin to Spam Slayer for Gravity Forms to comply with WordPress directory trademark guidelines.
- **UI Enhancement:** Completely overhauled the admin settings UI with a modern, card-based flexbox layout for better usability and aesthetics.
- **Visual Assets:** Added new high-quality plugin icons and banners to the assets directory for the WordPress directory listing.
- **Improved Detection:** Updated "GF Usage" page title to "Pages which uses gravity forms" and improved detection to find forms used inside Elementor widgets (e.g. bdt-gravity-forms).

### 1.4 (2026-05-26)
- **New Feature:** Restructured admin interface into a custom **Spam Slayer Tools** top-level menu containing Spam Slayer for Gravity Forms and Usage submenu pages.
- **Security Hardening & Stability:** Added checks on plugin activation, admin panels, and AJAX routines to gracefully handle scenarios when Gravity Forms is not installed or active, preventing any fatal errors.
- **Coding Standards Compliance:** Resolved prefixing errors, added missing translator comment annotations, formatted raw error logs, and updated readme configurations to match WordPress repository requirements.

### 1.3 (2026-05-26)
- **New Feature:** Added a Buy Me a Coffee donate link directly to the plugin action links on the main Plugins directory screen.
- **New Feature:** Added a Support card in the admin tools panel featuring links to GitHub Sponsors and Buy Me a Coffee.

### 1.2 (2026-05-26)
- **New Feature:** Added a Gravity Forms Usage page under the Tools management menu to see which posts and pages are using each Gravity Form (via shortcodes or Gutenberg blocks).

### 1.1 (2026-05-26)
- **Security Fix:** Fixed critical bug where undefined `wp_error_log` function could cause fatal PHP crash when logging errors.
- **Security Hardening:** Implemented server-side regex validation check before running `preg_match` to prevent PHP runtime warnings or crashes on invalid patterns.
- **Security Hardening:** Removed inline Javascript script block from the admin dashboard to comply with modern security guidelines (CSP compatibility). All JS handling is now fully localized and loaded from an external file.
- **Performance Optimization:** Optimized Gravity Forms loop performance by caching the form object retrieval outside the loop, avoiding redundant database lookups.
- **Localization:** Localized all remaining hardcoded strings in the admin interface and JavaScript logic.
- **Requirement Updates:** Bumped minimum required PHP version to 7.4.

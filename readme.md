# GForm Spam Slayer

Advanced spam cleanup and regex-powered spam detection for Gravity Forms entries.

![WordPress](https://img.shields.io/badge/WordPress-6.0+-111111?style=flat-square&logo=wordpress&logoColor=ffffff&labelColor=A93D21)
![PHP](https://img.shields.io/badge/PHP-7.4+-111111?style=flat-square&logo=php&logoColor=ffffff&labelColor=A93D21)
![Gravity_Forms](https://img.shields.io/badge/Gravity_Forms-supported-111111?style=flat-square&logoColor=ffffff&labelColor=A93D21)
![License](https://img.shields.io/badge/license-GPLv2+-111111?style=flat-square&logoColor=ffffff&labelColor=A93D21)
![Status](https://img.shields.io/badge/status-lightweight-111111?style=flat-square&logoColor=ffffff&labelColor=A93D21)

---

## What Is It?

**GForm Spam Slayer** is a lightweight cleanup utility for Gravity Forms that helps you detect, review, mark, and remove spam-like submissions already stored in your database.

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

### 1.1 (2026-05-26)
- **Security Fix:** Fixed critical bug where undefined `wp_error_log` function could cause fatal PHP crash when logging errors.
- **Security Hardening:** Implemented server-side regex validation check before running `preg_match` to prevent PHP runtime warnings or crashes on invalid patterns.
- **Security Hardening:** Removed inline Javascript script block from the admin dashboard to comply with modern security guidelines (CSP compatibility). All JS handling is now fully localized and loaded from an external file.
- **Performance Optimization:** Optimized Gravity Forms loop performance by caching the form object retrieval outside the loop, avoiding redundant database lookups.
- **Localization:** Localized all remaining hardcoded strings in the admin interface and JavaScript logic.
- **Requirement Updates:** Bumped minimum required PHP version to 7.4.

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

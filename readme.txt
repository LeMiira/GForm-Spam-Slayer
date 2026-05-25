```txt id="gform-readme-updated"
=== GForm Spam Slayer ===

Contributors: miiira  
Tags: gravity forms, spam cleanup, regex scanner, bot cleanup, entry management  
Requires at least: 6.0  
Tested up to: 6.9  
Requires PHP: 7.4  
Stable tag: 1.0.0  
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

Advanced spam cleanup and regex-powered spam detection for Gravity Forms entries.

== Description ==

**GForm Spam Slayer** is a lightweight and focused cleanup utility for Gravity Forms that helps you detect, review, mark, and remove spam-like submissions that already exist in your database.

Instead of trying to block spam during form submission, the plugin works as a post-submission analysis tool. It scans stored entries using regex-powered pattern matching to identify suspicious submissions such as:
- long alphanumeric strings
- bot-generated fake names
- promo spam
- random text injections
- malformed email patterns
- repetitive automated content

Perfect for sites that:
- experienced bot attacks
- migrated old forms
- accumulated spam over time
- need safer manual cleanup tools
- want more control than basic spam filtering

---

== Features ==

✔ Scan existing Gravity Forms entries using regex patterns  
✔ Select specific forms and fields to analyze  
✔ Built-in spam detection presets  
✔ Custom regex support for advanced filtering  
✔ Preview suspicious entries before deletion  
✔ Test mode for safer regex validation  
✔ Bulk mark spam entries  
✔ Bulk delete spam entries  
✔ Lightweight and fast admin interface  
✔ Native WordPress admin UI  
✔ No tracking, telemetry, or external API calls  
✔ Minimal performance impact  
✔ Works with existing Gravity Forms data  

---

== Example Use Cases ==

### Clean Up Bot Attacks
Detect and remove thousands of spam submissions after an attack.

### Detect Fake Names
Find entries containing suspicious random character strings.

### Filter Promo Spam
Locate entries containing repetitive promotional phrases or URLs.

### Advanced Regex Filtering
Use custom regex patterns to create highly targeted spam cleanup workflows.

---

== Important Note ==

This plugin does **NOT** block spam during form submission.

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

== Installation ==

1. Upload the `gform-spam-slayer` folder to `/wp-content/plugins/`
2. Activate the plugin from the WordPress Plugins screen
3. Navigate to:
   `Tools → GForm Spam Slayer`
4. Select:
   - a Gravity Form
   - fields to analyze
   - a regex pattern or custom filter
5. Run:
   - test scan
   - full scan
   - spam marking
   - spam deletion

---

== Frequently Asked Questions ==

= Does this plugin block spam submissions? =

No.  
GForm Spam Slayer is intentionally focused on detecting and cleaning spam entries that already exist.

---

= How does the spam detection work? =

The plugin scans selected form fields using regex pattern matching.

You can:
- use built-in presets
- create custom regex patterns
- test patterns before applying them

---

= Can I preview entries before deleting them? =

Yes.  
The plugin includes preview and test scan tools to reduce false positives and improve cleanup safety.

---

= Can I test regex patterns safely? =

Yes.  
You can:
- test against sample text
- run limited scans (e.g. latest 10 entries)
- validate patterns before bulk actions

---

= Does the plugin send data externally? =

No.  
Everything runs locally inside your WordPress installation.

No external APIs, tracking systems, analytics, or telemetry are used.

---

= Where can I access the plugin? =

After activation:

`Tools → GForm Spam Slayer`

---

== Security & Privacy ==

GForm Spam Slayer:
- does not collect user data
- does not transmit entries externally
- does not use remote APIs
- operates entirely within WordPress admin

Recommended best practices:
- always test regex patterns first
- backup your database before large cleanup operations
- review suspicious entries before deletion

---

== Performance ==

The plugin is designed to remain lightweight:
- no frontend scripts
- no tracking
- no background API calls
- optimized admin-only operations
- minimal database overhead

---

== Screenshots ==

1. Main admin interface under Tools → GForm Spam Slayer  
2. Gravity Forms field selector and regex configuration  
3. Preview mode and suspicious entry detection  
4. Bulk spam marking and deletion tools  
5. Test scan interface for safe regex validation  

---

== Changelog ==

= 1.0.0 =
* Initial public release
* Regex-powered spam detection
* Full scan and test scan modes
* Spam marking support
* Bulk deletion tools
* Gravity Forms field targeting
* Lightweight admin interface

---

== Upgrade Notice ==

= 1.0.0 =

Initial release of GForm Spam Slayer.

Use regex-powered analysis tools to clean and manage existing Gravity Forms spam entries safely.

---

== License ==

This plugin is licensed under the GPL v2 or later.

You are free to:
- use
- modify
- distribute
- fork

under the terms of the GPL license.
```
```txt id="donate-section"
== Support ==

If GForm Spam Slayer helps you clean up your forms and fight spam chaos, you can support development here:

GitHub Sponsors:
https://github.com/sponsors/LeMiira

Buy Me a Coffee:
https://buymeacoffee.com/miiiira

Website:
https://miiiira.com

GitHub:
https://github.com/LeMiira

Your support helps maintain:
- plugin updates
- compatibility improvements
- security fixes
- new features
- future WordPress tools and experiments

Thank you for supporting independent development.
```

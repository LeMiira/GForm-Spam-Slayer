=== GForm Spam Slayer ===
Contributors: miiira  
Tags: gravity forms, spam, spam detection, spam cleanup
Requires at least: 5.0  
Tested up to: 6.8  
Stable tag: 1.0  
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

A powerful tool to detect and manage spam-like entries already submitted via Gravity Forms.

== Description ==

**GForm Spam Slayer** is a post-submission spam detection and cleanup tool for Gravity Forms. It helps administrators locate suspicious or spam-like entries that have already been submitted by analyzing specific form fields using regular expression (regex) matching.

Rather than preventing spam from being submitted, this plugin scans your stored form entries to identify those that match patterns commonly used in spam — such as long alphanumeric strings or unnatural text. It’s a great tool for maintaining clean, reliable form data and reducing the noise caused by bot submissions.

**Key Features:**

- Select any Gravity Form and choose specific fields to scan.
- Use built-in or custom regex patterns to detect spammy content.
- Perform full scans or test scans on just a few entries.
- Bulk mark or delete spam entries.
- Clean, intuitive interface under **Tools > GForm Spam Slayer**.

**Example Use Cases:**

- Detect entries with long alphanumeric values (e.g., fake names or promo messages).
- Clean up form submissions after a bot attack.
- Apply fine-tuned regex filtering to specific form fields (like names, emails, or comments).

**Note:** This plugin does **not prevent** spam at the time of submission. It is designed to **detect and help clean up** spam entries already present in your Gravity Forms entries.

== Installation ==

1. Upload the `gform-spam-slayer` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Navigate to **Tools > GForm Spam Slayer** to use the plugin.
4. Select a form, choose which fields to analyze, and either select a regex pattern or define your own.
5. Run a scan, mark spam entries, or delete them as needed.

== Frequently Asked Questions ==

= Q: Does this plugin block spam submissions? =  
**A:** No. GForm Spam Slayer is designed to **detect spam after submission**, not prevent it at the entry point. It scans existing entries based on field values and regex patterns.

= Q: How does the regex detection work? =  
**A:** You can choose a preset regex pattern (e.g., long alphanumeric strings) or enter your own custom pattern. The plugin then checks your selected form field(s) for matches.

= Q: Can I test the regex before applying it? =  
**A:** Yes. The UI includes a sample text field and a "Test 10" option to scan a small batch of recent entries before committing to a full scan or deletion.

= Q: Will it affect legitimate entries? =  
**A:** It’s important to choose or test regex patterns carefully. The plugin provides a preview/test scan feature to minimize false positives.

= Q: Where is the plugin located in WordPress? =  
**A:** After activation, go to **Tools > GForm Spam Slayer** in the WordPress dashboard.

== Screenshots ==

1. Admin interface under Tools > GForm Spam Slayer.
2. Field selector and regex input area.
3. Action buttons for scanning, marking, and deleting entries.

== Changelog ==

= 1.0 =  
* Initial release.  
* Includes full scan, test scan, spam marking, and deletion based on field-level regex patterns.

== Upgrade Notice ==

= 1.0 =  
Initial release – use this tool to clean up existing Gravity Forms spam entries using custom regex filters.

== License ==

This plugin is licensed under the GPL v2 or later.  
You are free to use, modify, and distribute it under the same license.


<?php
/*
Plugin Name: Spam Slayer for Gravity Forms
Plugin URI: https://github.com/LeMiira/spam-slayer-for-gravity-forms
Description: A WordPress plugin to detect and manage spam entries in Gravity Forms
Version: 1.5
Requires at least: 5.0
Requires PHP: 7.4
Author: Mira
Author URI: https://profiles.wordpress.org/miiira
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: gform-spam-slayer
Domain Path: /languages
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Check for Gravity Forms during activation
register_activation_hook(__FILE__, 'spam_slayer_for_gravity_forms_activate');
function spam_slayer_for_gravity_forms_activate() {
    if (!class_exists('GFAPI')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            esc_html__('Spam Slayer for Gravity Forms requires Gravity Forms to be installed and active.', 'gform-spam-slayer'),
            esc_html__('Plugin Activation Error', 'gform-spam-slayer'),
            array('back_link' => true)
        );
    }
}

// Show notice if Gravity Forms is not active
add_action('admin_notices', 'spam_slayer_for_gravity_forms_admin_notices');
function spam_slayer_for_gravity_forms_admin_notices() {
    if (!class_exists('GFAPI')) {
        echo '<div class="notice notice-error"><p>';
        echo esc_html__('Spam Slayer for Gravity Forms requires Gravity Forms to be installed and active.', 'gform-spam-slayer');
        echo '</p></div>';
    }
}

// Plugin text domain is automatically loaded by WordPress.org

// Add the plugin menu to the WordPress admin
add_action('admin_menu', 'spam_slayer_for_gravity_forms_add_admin_menu');
function spam_slayer_for_gravity_forms_add_admin_menu() {
    add_menu_page(
        __('Spam Slayer Tools', 'gform-spam-slayer'),
        __('Spam Slayer Tools', 'gform-spam-slayer'),
        'manage_options',
        'spam-slayer-tools',
        'spam_slayer_for_gravity_forms_render_admin_page',
        'dashicons-admin-tools',
        80
    );

    add_submenu_page(
        'spam-slayer-tools',
        __('Spam Slayer for Gravity Forms', 'gform-spam-slayer'),
        __('Spam Slayer for Gravity Forms', 'gform-spam-slayer'),
        'manage_options',
        'spam-slayer-tools',
        'spam_slayer_for_gravity_forms_render_admin_page'
    );

    add_submenu_page(
        'spam-slayer-tools',
        __('Usage', 'gform-spam-slayer'),
        __('Usage', 'gform-spam-slayer'),
        'manage_options',
        'gf-usage',
        'spam_slayer_for_gravity_forms_render_gf_usage_page'
    );
}

// Add settings and donate links to plugin action links
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'spam_slayer_for_gravity_forms_plugin_action_links');
function spam_slayer_for_gravity_forms_plugin_action_links($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=spam-slayer-tools') . '">' . __('Settings', 'gform-spam-slayer') . '</a>';
    $donate_link = '<a href="https://github.com/sponsors/LeMiira" target="_blank" style="color: #C41E3A; font-weight: bold;">' . __('Donate', 'gform-spam-slayer') . '</a>';
    array_unshift($links, $settings_link, $donate_link);
    return $links;
}


/**
 * Get predefined regex patterns for spam detection.
 *
 * @return array Predefined regex patterns.
 */
function spam_slayer_for_gravity_forms_get_regex_patterns() {
    return array(
        'gibberish_mix' => array(
            'pattern' => '/^[a-z0-9\s]{10,}$/i',
            'description' => __('Letter-Number-Mix pattern (like asad5gbgfbdsz)', 'gform-spam-slayer'),
            'example' => 'asad5gbgfbdsz',
            'hit_test' => true
        ),
        'alphanumeric' => array(
            'pattern' => '/^[a-zA-Z0-9]{15,}$/',
            'description' => __('Alphanumeric characters only, 15 or more characters', 'gform-spam-slayer'),
            'example' => 'AbCdEfGhIjKlMnOp12345',
            'hit_test' => true
        ),
        'random_alphanumeric' => array(
            'pattern' => '/^[a-zA-Z0-9]{8,}$/',
            'description' => __('Random Alphanumeric, 8 or more characters', 'gform-spam-slayer'),
            'example' => 'FzMXhddP',
            'hit_test' => true
        ),
        'random_alphanumeric_uppercase' => array(
            'pattern' => '/^[A-Z0-9]{8,}$/',
            'description' => __('Random Uppercase Alphanumeric, 8+ chars', 'gform-spam-slayer'),
            'example' => 'FMXHPOTW',
            'hit_test' => true
        ),
        'long_word' => array(
            'pattern' => '/\b\w{20,}\b/',
            'description' => __('Matches any word longer than 20 characters', 'gform-spam-slayer'),
            'example' => 'ThisIsAVeryLongWordToDetect',
            'hit_test' => true
        ),
        'short_random_alphanumeric' => array(
            'pattern' => '/^[a-zA-Z0-9]{10,12}$/',
            'description' => __('Short Random Alphanumeric, 10-12 characters', 'gform-spam-slayer'),
            'example' => 'FzMXhddPOt',
            'hit_test' => true
        ),
        'short_random_alphanumeric_upeer' => array(
            'pattern' => '/^[A-Z0-9]{10,12}$/',
            'description' => __('Short Random, 10-12 Uppercase Alphanumeric characters', 'gform-spam-slayer'),
            'example' => 'CDDJHFJEKE',
            'hit_test' => true
        ),
        'email_like' => array(
            'pattern' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'description' => __('Basic email-like pattern', 'gform-spam-slayer'),
            'example' => 'test@example.com',
            'hit_test' => false
        ),
    );
}

// Enqueue admin scripts
add_action('admin_enqueue_scripts', 'spam_slayer_for_gravity_forms_enqueue_admin_scripts');
function spam_slayer_for_gravity_forms_enqueue_admin_scripts($hook) {
    if ('toplevel_page_spam-slayer-tools' !== $hook && 'spam-slayer-tools_page_spam-slayer-tools' !== $hook) {
        return;
    }

    // Register and enqueue admin scripts and styles only on plugin page
    $version = '1.1';

    wp_register_script(
        'spamslayergf-admin', 
        plugin_dir_url(__FILE__) . 'js/admin.js',
        array('jquery'),
        $version,
        true
    );

    wp_register_style(
        'spamslayergf-styles',
        plugin_dir_url(__FILE__) . 'css/style.css',
        array(),
        $version
    );

    wp_enqueue_script('spamslayergf-admin');
    wp_enqueue_style('spamslayergf-styles');

    wp_localize_script('spamslayergf-admin', 'spam_slayer_for_gravity_forms_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('spamslayergf-nonce'),
        'regex_patterns' => spam_slayer_for_gravity_forms_get_regex_patterns(),
        'i18n' => array(
            'example_text' => __('Example text using this pattern:', 'gform-spam-slayer'),
            'this_regex' => __('This Regex:', 'gform-spam-slayer'),
            'select_pattern' => __('Please select or enter a pattern to view an example.', 'gform-spam-slayer'),
        )
    ));
}

// Render the admin page for the plugin
function spam_slayer_for_gravity_forms_render_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (!class_exists('GFAPI')) {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Spam Slayer for Gravity Forms - Spam Management', 'gform-spam-slayer') . '</h1>';
        echo '<div class="notice notice-error inline"><p>' . esc_html__('Gravity Forms is not installed or active.', 'gform-spam-slayer') . '</p></div>';
        echo '</div>';
        return;
    }

    echo '<div class="wrap">';
    echo '<h1>' . esc_html__('Spam Slayer for Gravity Forms - Spam Management', 'gform-spam-slayer') . '</h1>';
    echo '<p>' . esc_html__('Use this tool to detect and manage spam entries in Gravity Forms.', 'gform-spam-slayer') . '</p>';

    $forms = GFAPI::get_forms();
    if (empty($forms)) {
        echo '<p>' . esc_html__('No forms available. Please create a form first.', 'gform-spam-slayer') . '</p>';
        echo '</div>';
        return;
    }

    echo '<div class="spam-slayer-for-gravity-forms-admin-container">';
    echo '<div class="spam-slayer-for-gravity-forms-main-content">';

    // Retrieve stored settings
    $stored_form_id = get_option('spam_slayer_for_gravity_forms_form_id', '');
    $stored_field_ids = get_option('spam_slayer_for_gravity_forms_field_ids', '');
    $stored_regex_pattern = get_option('spam_slayer_for_gravity_forms_regex_pattern', '');
    $stored_custom_pattern = get_option('spam_slayer_for_gravity_forms_custom_pattern', '');
    $stored_action = get_option('spam_slayer_for_gravity_forms_action', '');

    // Predefined Regex Patterns
    $regex_patterns = spam_slayer_for_gravity_forms_get_regex_patterns();

    ?>

    <form id="spam-slayer-for-gravity-forms-form" method="post" class="spam-slayer-settings-form">
        <div class="spam-slayer-card">
            <h3><?php esc_html_e('1. Select Form & Fields', 'gform-spam-slayer'); ?></h3>
            <div class="spam-slayer-field-group">
                <label for="form_id"><?php esc_html_e('Select Form:', 'gform-spam-slayer'); ?></label>
                <div class="spam-slayer-input-with-button">
                    <select name="form_id" id="form_id" class="regular-text">
                        <?php foreach ($forms as $form): ?>
                            <option value="<?php echo esc_attr($form['id']); ?>" <?php selected($stored_form_id, $form['id']); ?>>
                                <?php echo esc_html($form['title']) . ' :: ID - ' . esc_html($form['id']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" id="load-fields-btn" class="button"><?php esc_html_e('Load Fields', 'gform-spam-slayer'); ?></button>
                </div>
                <div id="field-list" class="spam-slayer-field-list"></div>
            </div>

            <div class="spam-slayer-field-group">
                <label for="field_ids"><?php esc_html_e('Fields to Check (comma-separated field IDs):', 'gform-spam-slayer'); ?></label>
                <input type="text" name="field_ids" id="field_ids" class="large-text" placeholder="<?php esc_attr_e('e.g., 1.3, 1.6', 'gform-spam-slayer'); ?>" value="<?php echo esc_attr($stored_field_ids); ?>">
            </div>
        </div>

        <div class="spam-slayer-card">
            <h3><?php esc_html_e('2. Define Spam Pattern', 'gform-spam-slayer'); ?></h3>
            <div class="spam-slayer-field-group">
                <label for="regex_pattern"><?php esc_html_e('Regex Pattern:', 'gform-spam-slayer'); ?></label>
                <select name="regex_pattern" id="regex_pattern" class="large-text">
                    <option value=""><?php esc_html_e('Custom Pattern', 'gform-spam-slayer'); ?></option>
                    <?php foreach ($regex_patterns as $key => $pattern): ?>
                        <option value="<?php echo esc_attr($pattern['pattern']); ?>" <?php selected($stored_regex_pattern, $pattern['pattern']); ?>>
                            <?php echo esc_html($pattern['description']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="spam-slayer-field-group">
                <label for="custom_pattern"><?php esc_html_e('Custom Regex (if selected above):', 'gform-spam-slayer'); ?></label>
                <input type="text" name="custom_pattern" id="custom_pattern" class="large-text" placeholder="<?php esc_attr_e('Enter custom regex pattern', 'gform-spam-slayer'); ?>" value="<?php echo esc_attr($stored_custom_pattern); ?>">
            </div>

            <div id="pattern-example" class="spam-slayer-pattern-preview">
                <?php
                // Display Example
                $effective_pattern = !empty($stored_custom_pattern) ? $stored_custom_pattern : $stored_regex_pattern;

                if (!empty($effective_pattern)) {
                    echo '<p><strong>' . esc_html__('Pattern Example:', 'gform-spam-slayer') . '</strong></p>';
                    $matched = false;
                    foreach ($regex_patterns as $pattern) {
                        if ($pattern['pattern'] == $stored_regex_pattern || $pattern['pattern'] == $stored_custom_pattern) {
                            $matched = $pattern['example'];
                            break;
                        }
                    }
                    if (isset($matched) && !empty($matched)) {
                        echo '<p>' . esc_html($matched) . '</p>';
                    } else {
                        echo '<p>' . esc_html__('Enter custom regex and test to see matches here', 'gform-spam-slayer') . '</p>';
                    }
                } else {
                    echo '<p><strong>' . esc_html__('Select a pattern or enter a custom one', 'gform-spam-slayer') . '</strong></p>';
                }
                ?>
            </div>
        </div>

        <div class="spam-slayer-card spam-slayer-actions-card">
            <h3><?php esc_html_e('3. Actions', 'gform-spam-slayer'); ?></h3>
            <div class="spam-slayer-action-buttons">
                <button type="button" class="button button-primary button-hero main-button" data-action="find_spam">
                    <span class="dashicons dashicons-search"></span> <?php esc_html_e('Find Spam (Full Scan)', 'gform-spam-slayer'); ?>
                </button>
                <button type="button" class="button button-secondary button-hero main-button" data-action="test_spam">
                    <span class="dashicons dashicons-admin-plugins"></span> <?php esc_html_e('Test Scan (10 Entries)', 'gform-spam-slayer'); ?>
                </button>
                <button type="button" class="button button-secondary button-hero main-button" data-action="mark_spam">
                    <span class="dashicons dashicons-flag"></span> <?php esc_html_e('Mark Spam Entries', 'gform-spam-slayer'); ?>
                </button>
                <button type="button" class="button button-hero main-button spam-slayer-btn-danger" data-action="delete_spam">
                    <span class="dashicons dashicons-trash"></span> <?php esc_html_e('Delete Spam Entries', 'gform-spam-slayer'); ?>
                </button>
            </div>
        </div>

    </form>
        <div id="debug-results" style="display:none;"></div>
    <div id="loading-indicator" style="display:none;"><?php esc_html_e('Loading...', 'gform-spam-slayer'); ?></div>
    <?php
    echo '</div>'; // End .spam-slayer-for-gravity-forms-main-content

    echo '<div class="spam-slayer-for-gravity-forms-sidebar">';
    ?>
    <div class="spam-slayer-for-gravity-forms-support-card">
        <h3><?php esc_html_e('Support the Developer', 'gform-spam-slayer'); ?></h3>
        <p><?php esc_html_e('If Spam Slayer for Gravity Forms is saving you time and keeping your database clean, please consider supporting my work.', 'gform-spam-slayer'); ?></p>
        <div class="spam-slayer-for-gravity-forms-support-buttons">
            <a href="https://github.com/sponsors/lemiira" target="_blank" class="spam-slayer-for-gravity-forms-btn spam-slayer-for-gravity-forms-btn-github">
                <svg class="icon" viewBox="0 0 16 16" width="16" height="16" fill="currentColor"><path fill-rule="evenodd" d="M8 1.482c1.6-1.518 4.5-1.18 5.923.491 1.422 1.67 1.242 4.416-.395 6.006L8 14.102 2.472 7.98c-1.637-1.59-1.817-4.336-.395-6.006C3.5 2.296 6.4 1.958 8 1.482zm0 11.238l4.908-5.437c1.176-1.144 1.3-3.036.197-4.335-1.122-1.319-3.344-1.503-4.553-.338L8 3.256 7.448 2.61c-1.21-1.165-3.43-0.98-4.553.338-1.103 1.3-0.98 3.19.197 4.335L8 12.72z"/></svg>
                <?php esc_html_e('GitHub Sponsor', 'gform-spam-slayer'); ?>
            </a>
            <a href="https://www.buymeacoffee.com/miiiira" target="_blank" class="spam-slayer-for-gravity-forms-btn spam-slayer-for-gravity-forms-btn-bmc">
                <svg class="icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M20 3H4v10c0 2.21 1.79 4 4 4h6c2.21 0 4-1.79 4-4v-3h2c1.66 0 3-1.34 3-3V6c0-1.66-1.34-3-3-3zm1 5h-3V5h3v3zM2 19h18v2H2v-2z"/></svg>
                <?php esc_html_e('Buy Me a Coffee', 'gform-spam-slayer'); ?>
            </a>
        </div>
    </div>
    <?php
    echo '</div>'; // End .spam-slayer-for-gravity-forms-sidebar
    echo '</div>'; // End .spam-slayer-for-gravity-forms-admin-container
    echo '</div>'; // End .wrap
}

// Function to load fields via AJAX
add_action('wp_ajax_spam_slayer_for_gravity_forms_load_fields', 'spam_slayer_for_gravity_forms_load_fields');
function spam_slayer_for_gravity_forms_load_fields() {
    if (!class_exists('GFAPI')) {
        wp_send_json_error(__('Gravity Forms is not installed or active.', 'gform-spam-slayer'));
        return;
    }

    // Check nonce and permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(__('Insufficient permissions.', 'gform-spam-slayer'));
        return;
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'spamslayergf-nonce')) {
        wp_send_json_error(__('Invalid security token.', 'gform-spam-slayer'));
        return;
    }

    if (!isset($_POST['form_id'])) {
        wp_send_json_error(__('Invalid request', 'gform-spam-slayer'));
        return;
    }

    $form_id = intval($_POST['form_id']);
    $form = GFAPI::get_form($form_id);
    if (!$form) {
        wp_send_json_error(__('Form not found', 'gform-spam-slayer'));
    }

    if (empty($form['fields'])) {
        wp_send_json_error(__('No fields found in this form.', 'gform-spam-slayer'));
        return;
    }

    $fields = $form['fields'];
    $output = '<h4>' . esc_html__('Available Fields:', 'gform-spam-slayer') . '</h4><ul>';

    foreach ($fields as $field) {
        // Output the main field
        $output .= sprintf(
            '<li><strong>%s</strong> - ID: %s</li>',
            esc_html($field['label']),
            esc_html($field['id'])
        );

        // Check if the field has inputs (subfields)
        if (isset($field['inputs']) && is_array($field['inputs'])) {
            $output .= '<ul>'; // Start a nested list for subfields
            foreach ($field['inputs'] as $input) {
                $output .= sprintf(
                    '<li>&nbsp;&nbsp;&nbsp;↳ %s - ID: %s</li>', // Indent subfields
                    esc_html($input['label']),
                    esc_html($input['id'])
                );
            }
            $output .= '</ul>'; // Close the nested list
        }
    }

    $output .= '</ul>';

    wp_send_json_success($output);
}


// AJAX handler for the main form submission
add_action('wp_ajax_spam_slayer_for_gravity_forms_process_form', 'spam_slayer_for_gravity_forms_process_form');
function spam_slayer_for_gravity_forms_process_form() {
    if (!class_exists('GFAPI')) {
        wp_send_json_error(__('Gravity Forms is not installed or active.', 'gform-spam-slayer'));
        return;
    }

    // Verify nonce and user capabilities
    if (!current_user_can('manage_options')) {
        wp_send_json_error(__('Insufficient permissions.', 'gform-spam-slayer'));
        return;
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'spamslayergf-nonce')) {
        wp_send_json_error(__('Invalid security token.', 'gform-spam-slayer'));
        return;
    }

    $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
    $field_ids = isset($_POST['field_ids']) ? sanitize_text_field(wp_unslash($_POST['field_ids'])) : '';
    $regex_pattern = isset($_POST['regex_pattern']) ? sanitize_text_field(wp_unslash($_POST['regex_pattern'])) : '';
    $custom_pattern = isset($_POST['custom_pattern']) ? sanitize_text_field(wp_unslash($_POST['custom_pattern'])) : '';
    $sub_action = isset($_POST['sub_action']) ? sanitize_text_field(wp_unslash($_POST['sub_action'])) : '';

    $fields_to_check = array_map('trim', explode(',', $field_ids));
    $effective_pattern = !empty($custom_pattern) ? $custom_pattern : $regex_pattern;

    // Validation
    if (empty($fields_to_check)) {
        wp_send_json_error(__('Error: Please select fields to check for spam.', 'gform-spam-slayer'));
        return;
    }

    if (empty($effective_pattern)) {
        wp_send_json_error(__('Error: Please enter a custom regex or select a pattern.', 'gform-spam-slayer'));
        return;
    }

    if (@preg_match($effective_pattern, '') === false) {
        wp_send_json_error(__('Error: The regular expression pattern is invalid.', 'gform-spam-slayer'));
        return;
    }

    switch ($sub_action) {
        case 'find_spam':
        case 'test_spam':
            $limit = ($sub_action === 'test_spam') ? 10 : 0;
            $result = spam_slayer_for_gravity_forms_process_spam_finding($form_id, $fields_to_check, $limit, $effective_pattern);
            break;
        case 'mark_spam':
            $result = spam_slayer_for_gravity_forms_process_spam_marking($form_id, $fields_to_check, $effective_pattern);
            break;
        case 'delete_spam':
            $result = spam_slayer_for_gravity_forms_process_spam_deletion($form_id);
            break;
        default:
            wp_send_json_error(__('Invalid action.', 'gform-spam-slayer'));
            return;
    }

    // Update stored settings
    update_option('spam_slayer_for_gravity_forms_form_id', $form_id);
    update_option('spam_slayer_for_gravity_forms_field_ids', $field_ids);
    update_option('spam_slayer_for_gravity_forms_regex_pattern', $regex_pattern);
    update_option('spam_slayer_for_gravity_forms_custom_pattern', $custom_pattern);
    update_option('spam_slayer_for_gravity_forms_action', $sub_action);

    wp_send_json_success($result);
}


/**
 * Function to find spam entries
 *
 * @param int $form_id Gravity Forms form ID
 * @param array $fields_to_check Array of field IDs to check
 * @param int $limit Number of entries to check. 0 for all.
 * @param string $regex_pattern Regular expression pattern
 *
 * @return string HTML output of spam detection results
 */
function spam_slayer_for_gravity_forms_process_spam_finding( $form_id, $fields_to_check, $limit = 0, $regex_pattern = '/^[a-zA-Z0-9]{15,}$/' ) {

    // Validation to ensure arguments are of expected data type
    if ( ! is_int( $form_id ) ) {
        return '<p class="spam-slayer-for-gravity-forms-error">Error: Form ID must be an integer.</p>';
    }
    if ( ! is_array( $fields_to_check ) ) {
        return '<p class="spam-slayer-for-gravity-forms-error">Error: Fields to check must be an array.</p>';
    }
    if ( ! is_int( $limit ) ) {
        return '<p class="spam-slayer-for-gravity-forms-error">Error: Limit must be an integer.</p>';
    }
    if ( ! is_string( $regex_pattern ) ) {
        return '<p class="spam-slayer-for-gravity-forms-error">Error: Regex pattern must be a string.</p>';
    }

    $output = '<h3>' . esc_html__( 'Spam Detection Results:', 'gform-spam-slayer' ) . '</h3>';

    $spam_count = 0;
    $total_checked = 0;

    $paging = $limit > 0 ? [ 'page_size' => $limit ] : [];

    // **MODIFIED: Add search criteria to exclude spam entries**
    $search_criteria = array(
        'status' => 'active' // Only get entries with a status of "active" (not spam)
    );

    try {
        $entries = GFAPI::get_entries(
            $form_id,
            $search_criteria, // Pass the search criteria
            [
                'paging' => $paging,
                'sorting' => [ 'key' => 'id', 'direction' => 'DESC' ],
            ]
        );

        if ( empty( $entries ) ) {
            return '<div class="spam-slayer-for-gravity-forms-notice"><p>' . esc_html__('There are no entries yet for this form. Please add some entries first.', 'gform-spam-slayer') . '</p></div>';
        }
    } catch (Exception $e) {
        return '<div class="spam-slayer-for-gravity-forms-error"><p>' . esc_html__('Error retrieving entries. Please try again.', 'gform-spam-slayer') . '</p></div>';
    }

    $form = GFAPI::get_form($form_id);
    if (!$form) {
        return '<div class="spam-slayer-for-gravity-forms-error"><p>' . esc_html__('Error: Form not found.', 'gform-spam-slayer') . '</p></div>';
    }

    foreach ( $entries as $entry ) {
        $total_checked++;
        $is_spam = false;
        $entry_output = '';

        foreach ( $fields_to_check as $field_id ) {
            $field = GFFormsModel::get_field($form, $field_id);

            // Check if it's a Name field (has sub-fields)
            if ($field && $field->type === 'name') {
                // Get the inputs array which contains all subfields
                $inputs = $field->get_entry_inputs();
                $first_name_input = null;
                $last_name_input = null;

                // Find first and last name inputs
                foreach ($inputs as $input) {
                    if (strpos(strtolower($input['label']), 'first') !== false) {
                        $first_name_input = $input;
                    } else if (strpos(strtolower($input['label']), 'last') !== false) {
                        $last_name_input = $input;
                    }
                }

                // Check full name field
                $full_name = isset($entry[$field_id]) ? $entry[$field_id] : '';
                if (preg_match($regex_pattern, $full_name)) {
                    $is_spam = true;
                    $entry_output .= '<p class="spam-slayer-for-gravity-forms-spam-match">Full Name (' . esc_html($field_id) . ') SPAM MATCH: ' . esc_html($full_name) . '</p>';
                }

                // Get values using dynamic input IDs
                $first_name = ($first_name_input && isset($entry[$first_name_input['id']])) ? $entry[$first_name_input['id']] : '';
                $last_name = ($last_name_input && isset($entry[$last_name_input['id']])) ? $entry[$last_name_input['id']] : '';

                // Check First Name
                if (preg_match($regex_pattern, $first_name)) {
                    $is_spam = true;
                    $entry_output .= '<p class="spam-slayer-for-gravity-forms-spam-match">First Name (' . esc_html($first_name_input['id']) . ') SPAM MATCH: ' . esc_html($first_name) . '</p>';
                } else {
                    $entry_output .= '<p>First Name (' . esc_html($first_name_input['id']) . ') OK: ' . esc_html($first_name) . '</p>';
                }

                // Check Last Name
                if (preg_match($regex_pattern, $last_name)) {
                    $is_spam = true;
                    $entry_output .= '<p class="spam-slayer-for-gravity-forms-spam-match">Last Name (' . esc_html($last_name_input['id']) . ') SPAM MATCH: ' . esc_html($last_name) . '</p>';
                } else {
                    $entry_output .= '<p>Last Name (' . esc_html($last_name_input['id']) . ') OK: ' . esc_html($last_name) . '</p>';
                }
            } else {
                // Handle other field types

                if (isset($entry[$field_id])) {
                    $value = $entry[$field_id];
                    if (preg_match($regex_pattern, $value)) {
                        $is_spam = true;
                        $entry_output .= '<p class="spam-slayer-for-gravity-forms-spam-match">Field ID ' . esc_html($field_id) . ' SPAM MATCH: ' . esc_html($value ) . '</p>';
                    } else {
                        $entry_output .= '<p>Field ID ' . esc_html($field_id) . ' OK: ' . esc_html($value) . '</p>';
                    }
                } else {
                    $entry_output .= '<p class="spam-slayer-for-gravity-forms-warning">Warning: Field ID ' . esc_html($field_id) . ' not found in Entry #' . esc_html($entry['id']) . '</p>';
                }
            }
        }

        if ($is_spam) {
            $spam_count++;
            $output .= '<div class="spam-slayer-for-gravity-forms-entry spam-slayer-for-gravity-forms-spam-entry"><h4>Entry #' . esc_html( $entry['id'] ) . ' - <span class="spam-slayer-for-gravity-forms-spam-label">Identified as SPAM</span></h4>' . $entry_output . '</div>';
        } else {
            $output .= '<div class="spam-slayer-for-gravity-forms-entry"><h4>Entry #' . esc_html( $entry['id'] ) . ' - OK</h4>' . $entry_output . '</div>';
        }
    }

    /* translators: %d: number of checked entries */
    $output .= '<p>' . sprintf( esc_html__( 'Total entries checked: %d', 'gform-spam-slayer' ), $total_checked ) . '</p>';
    /* translators: %d: number of spam entries found */
    $output .= '<p class="spam-slayer-for-gravity-forms-spam-count">' . sprintf( esc_html__( 'Total spam entries found: %d', 'gform-spam-slayer' ), $spam_count ) . '</p>';

    return $output;
}


/**
 * Function to mark spam entries
 *
 * @param int $form_id Gravity Forms form ID
 * @param array $fields_to_check Array of field IDs to check (not used in this function, but kept for consistency)
 * @param string $regex_pattern Regular expression pattern (not used in this function, but kept for consistency)
 *
 * @return string HTML output of spam marking results
 */
function spam_slayer_for_gravity_forms_process_spam_marking( $form_id, $fields_to_check, $regex_pattern ) {
    // Process entries with timeout handling through WordPress functions
    if (!wp_doing_ajax()) {
        wp_raise_memory_limit('admin');
    }

    $marked_count = 0;
    $batch_size = 50; // Process entries in batches

    // Basic validation for form_id
    if ( ! is_int( $form_id ) || $form_id <= 0 ) {
        return '<p style="color:red;">' . esc_html__( 'Error: Invalid Form ID.', 'gform-spam-slayer' ) . '</p>';
    }

    // Get all entry IDs for the specified form that are *not* already marked as spam
    $entry_ids = GFAPI::get_entry_ids( $form_id, [ 'status' => 'active', 'field_filters' => [] ] ); // Get only active entries

    if ( empty( $entry_ids ) ) {
        return '<p>' . esc_html__( 'No entries found to mark as spam.', 'gform-spam-slayer' ) . '</p>';
    }

    // Chunk the entry IDs into smaller batches
    $entry_id_chunks = array_chunk( $entry_ids, $batch_size );

    foreach ( $entry_id_chunks as $entry_id_chunk ) {
        foreach ( $entry_id_chunk as $entry_id ) {
            try {
                if(!is_numeric($entry_id)) {
                    /* translators: %s: Entry ID that was invalid */
                    throw new Exception(sprintf(__('Invalid entry ID: %s', 'gform-spam-slayer'), $entry_id));
                }
                GFAPI::update_entry_property( $entry_id, 'status', 'spam' );
                $marked_count++;
            } catch (Exception $e) {
                if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
                    // translators: %1$s is the entry ID, %2$s is the error message
                    $error_message = sprintf('[Spam Slayer for Gravity Forms] Error marking entry %1$s: %2$s', $entry_id, $e->getMessage());
                    // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
                    error_log($error_message);
                }
                continue;
            }
        } // inner foreach (entries in chunk)
    } // outer foreach (chunks)

    /* translators: %d: number of entries marked as spam */
    return '<p>' . sprintf( esc_html__( 'Successfully marked %d entries as spam.', 'gform-spam-slayer' ), $marked_count ) . '</p>';
}

/**
 * Function to delete spam entries
 *
 * @param int $form_id Gravity Forms form ID
 *
 * @return string HTML output of spam deletion results
 */
function spam_slayer_for_gravity_forms_process_spam_deletion( $form_id ) {
    // Process entries with timeout handling through WordPress functions
    if (!wp_doing_ajax()) {
        wp_raise_memory_limit('admin');
    }

    $deleted_count = 0;
    $batch_size = 50; // Process entries in batches

    // Basic validation for form_id
    if ( ! is_int( $form_id ) || $form_id <= 0 ) {
        return '<p style="color:red;">' . esc_html__( 'Error: Invalid Form ID.', 'gform-spam-slayer' ) . '</p>';
    }

    // Get all entry IDs for the specified form that are currently marked as spam
    $entry_ids = GFAPI::get_entry_ids( $form_id, [ 'status' => 'spam', 'field_filters' => [] ] ); // Get spam entries

    if ( empty( $entry_ids ) ) {
        return '<p>' . esc_html__( 'No spam entries found to delete.', 'gform-spam-slayer' ) . '</p>';
    }

    // Chunk the entry IDs into smaller batches
    $entry_id_chunks = array_chunk( $entry_ids, $batch_size );

    foreach ( $entry_id_chunks as $entry_id_chunk ) {
        foreach ( $entry_id_chunk as $entry_id ) {
            try {
                if(!is_numeric($entry_id)) {
                    /* translators: %s: Entry ID that was invalid */
                    throw new Exception(sprintf(__('Invalid entry ID: %s', 'gform-spam-slayer'), $entry_id));
                }
                GFAPI::delete_entry( $entry_id );
                $deleted_count++;
            } catch (Exception $e) {
                if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
                    // translators: %1$s is the entry ID, %2$s is the error message
                    $error_message = sprintf('[Spam Slayer for Gravity Forms] Error deleting entry %1$s: %2$s', $entry_id, $e->getMessage());
                    // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
                    error_log($error_message);
                }
                continue;
            }
        } // inner foreach (entries in chunk)
    } // outer foreach (chunks)

    /* translators: %d: number of deleted spam entries */
    return '<p>' . sprintf( esc_html__( 'Successfully deleted %d spam entries.', 'gform-spam-slayer' ), $deleted_count ) . '</p>';
}

function spam_slayer_for_gravity_forms_add_result(&$results, $form_id, $post_url, $post_status) {
    if (!isset($results[$form_id])) {
        $results[$form_id] = [];
    }
    // Avoid duplicates
    foreach ($results[$form_id] as $res) {
        if ($res['url'] === $post_url) {
            return;
        }
    }
    $results[$form_id][] = ['url' => $post_url, 'status' => $post_status];
}

function spam_slayer_for_gravity_forms_find_gf_in_elementor($data, &$results, $post_url, $post_status) {
    if (is_array($data)) {
        if (isset($data['widgetType']) && (strpos($data['widgetType'], 'gravity') !== false || strpos($data['widgetType'], 'gform') !== false)) {
            if (isset($data['settings'])) {
                $form_id = null;
                if (isset($data['settings']['gravity_form_id'])) {
                    $form_id = $data['settings']['gravity_form_id'];
                } elseif (isset($data['settings']['form_id'])) {
                    $form_id = $data['settings']['form_id'];
                } elseif (isset($data['settings']['id'])) {
                    $form_id = $data['settings']['id'];
                }
                
                if ($form_id && is_numeric($form_id)) {
                    spam_slayer_for_gravity_forms_add_result($results, $form_id, $post_url, $post_status);
                }
            }
        }
        
        if (isset($data['widgetType']) && $data['widgetType'] === 'shortcode') {
            if (isset($data['settings']['shortcode'])) {
                if (preg_match_all('/\[gravityform.*?id=["\']?(\d+)["\']?.*?\]/i', $data['settings']['shortcode'], $matches)) {
                    foreach ($matches[1] as $form_id) {
                        spam_slayer_for_gravity_forms_add_result($results, $form_id, $post_url, $post_status);
                    }
                }
            }
        }

        foreach ($data as $value) {
            if (is_array($value)) {
                spam_slayer_for_gravity_forms_find_gf_in_elementor($value, $results, $post_url, $post_status);
            }
        }
    }
}

function spam_slayer_for_gravity_forms_render_gf_usage_page() {
    if (!current_user_can('manage_options')) return;

    if (!class_exists('GFAPI')) {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Pages which uses gravity forms', 'gform-spam-slayer') . '</h1>';
        echo '<div class="notice notice-error inline"><p>' . esc_html__('Gravity Forms is not installed or active.', 'gform-spam-slayer') . '</p></div>';
        echo '</div>';
        return;
    }

    $results = [];

    $posts = get_posts([
        'post_type'   => 'any',
        'post_status' => ['publish', 'private'],
        'numberposts' => -1,
    ]);

    foreach ($posts as $post) {
        $content = $post->post_content;

        // Match shortcode: [gravityform id="X"]
        if (preg_match_all('/\[gravityform.*?id=["\']?(\d+)["\']?.*?\]/i', $content, $matches)) {
            foreach ($matches[1] as $form_id) {
                spam_slayer_for_gravity_forms_add_result($results, $form_id, get_permalink($post->ID), $post->post_status);
            }
        }

        // Match Gutenberg block
        if (preg_match_all('/"formId":\s*(\d+)/i', $content, $matches)) {
            foreach ($matches[1] as $form_id) {
                spam_slayer_for_gravity_forms_add_result($results, $form_id, get_permalink($post->ID), $post->post_status);
            }
        }

        // Elementor Data Check
        $elementor_data = get_post_meta($post->ID, '_elementor_data', true);
        if (!empty($elementor_data)) {
            $data = json_decode($elementor_data, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                spam_slayer_for_gravity_forms_find_gf_in_elementor($data, $results, get_permalink($post->ID), $post->post_status);
            }
        }
    }

    echo '<div class="wrap"><h1>' . esc_html__('Pages which uses gravity forms', 'gform-spam-slayer') . '</h1>';

    if (empty($results)) {
        echo '<p>' . esc_html__('No pages using Gravity Forms found.', 'gform-spam-slayer') . '</p>';
        echo '</div>';
        return;
    }

    echo '<table class="widefat fixed striped">';
    echo '<thead>
            <tr>
                <th>' . esc_html__('Form ID', 'gform-spam-slayer') . '</th>
                <th>' . esc_html__('Form Title', 'gform-spam-slayer') . '</th>
                <th>' . esc_html__('Page Used', 'gform-spam-slayer') . '</th>
                <th>' . esc_html__('Edit Form', 'gform-spam-slayer') . '</th>
            </tr>
          </thead><tbody>';

    foreach ($results as $form_id => $pages) {
        $form_title = 'Unknown';

        if (class_exists('GFAPI')) {
            $form = \GFAPI::get_form($form_id);

            if (!is_wp_error($form) && !empty($form) && isset($form['title'])) {
                $form_title = $form['title'];
            }
        }

        $edit_link = admin_url('admin.php?page=gf_edit_forms&id=' . intval($form_id));

        foreach ($pages as $page) {
            $url = $page['url'];
            $status_label = $page['status'] === 'publish' ? esc_html__('Public', 'gform-spam-slayer') : esc_html__('Private', 'gform-spam-slayer');
            $status_color = $page['status'] === 'publish' ? '#46b450' : '#dc3232';
            
            echo '<tr>';
            echo '<td>' . esc_html($form_id) . '</td>';
            echo '<td>' . esc_html($form_title) . '</td>';
            echo '<td><a href="' . esc_url($url) . '" target="_blank">' . esc_html($url) . '</a> <span style="color: ' . esc_attr($status_color) . '; font-size: 0.9em; margin-left: 5px; font-weight: bold;">[' . esc_html($status_label) . ']</span></td>';
            echo '<td><a href="' . esc_url($edit_link) . '" target="_blank">' . esc_html__('Edit', 'gform-spam-slayer') . '</a></td>';
            echo '</tr>';
        }
    }

    echo '</tbody></table>';
    echo '</div>';
}
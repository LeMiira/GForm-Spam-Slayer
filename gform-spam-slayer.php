<?php
/**
 * Plugin Name: GForm Spam Slayer
 * Plugin URI: https://github.com/LeMiira/gform-spam-slayer
 * Description: A WordPress plugin to detect and manage spam entries in Gravity Forms.
 * Version: 1.0
 * Author: Mira
 * Author URI: https://github.com/LeMiira
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Plugin text domain is automatically loaded by WordPress.org

// Add the plugin menu to the WordPress admin tools section
add_action('admin_menu', 'gform_spam_slayer_add_admin_menu');
function gform_spam_slayer_add_admin_menu() {
    add_submenu_page(
        'tools.php', // Parent menu (Tools)
        __('GForm Spam Slayer', 'gform-spam-slayer'), // Page title
        __('GForm Spam Slayer', 'gform-spam-slayer'), // Menu title
        'manage_options', // Capability (admin-only access)
        'gform-spam-slayer', // Menu slug
        'gform_spam_slayer_render_admin_page' // Callback function
    );
}

// Enqueue admin scripts
add_action('admin_enqueue_scripts', 'gform_spam_slayer_enqueue_admin_scripts');
function gform_spam_slayer_enqueue_admin_scripts($hook) {
    if ('tools_page_gform-spam-slayer' !== $hook) {
        return;
    }

    // Register and enqueue admin scripts and styles only on plugin page
    $version = '1.0';

    wp_register_script(
        'gforspsl-admin', 
        plugin_dir_url(__FILE__) . 'js/admin.js',
        array('jquery'),
        $version,
        true
    );

    wp_register_style(
        'gforspsl-styles',
        plugin_dir_url(__FILE__) . 'css/style.css',
        array(),
        $version
    );

    wp_enqueue_script('gforspsl-admin');
    wp_enqueue_style('gforspsl-styles');

    wp_localize_script('gforspsl-admin', 'gforspsl_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('gforspsl-nonce')
    ));
}

// Render the admin page for the plugin
function gform_spam_slayer_render_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    echo '<div class="wrap">';
    echo '<h1>' . esc_html__('GForm Spam Slayer - Spam Management', 'gform-spam-slayer') . '</h1>';
    echo '<p>' . esc_html__('Use this tool to detect and manage spam entries in Gravity Forms.', 'gform-spam-slayer') . '</p>';

    $forms = GFAPI::get_forms();
    if (empty($forms)) {
        echo '<p>' . esc_html__('No forms available. Please create a form first.', 'gform-spam-slayer') . '</p>';
        return;
    }

    // Retrieve stored settings
    $stored_form_id = get_option('gform_spam_slayer_form_id', '');
    $stored_field_ids = get_option('gform_spam_slayer_field_ids', '');
    $stored_regex_pattern = get_option('gform_spam_slayer_regex_pattern', '');
    $stored_custom_pattern = get_option('gform_spam_slayer_custom_pattern', '');
    $stored_action = get_option('gform_spam_slayer_action', '');

    // Predefined Regex Patterns
    $regex_patterns = array(
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
        // Add more patterns as needed
    );

    ?>

    <form id="gform-spam-slayer-form" method="post">
        <label for="form_id"><?php esc_html_e('Select Form:', 'gform-spam-slayer'); ?></label>
        <select name="form_id" id="form_id">
            <?php foreach ($forms as $form): ?>
                <option value="<?php echo esc_attr($form['id']); ?>" <?php selected($stored_form_id, $form['id']); ?>>
                    <?php echo esc_html($form['title']) . ' :: ID - ' . esc_html($form['id']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="button" id="load-fields-btn" class="button"><?php esc_html_e('Load Fields', 'gform-spam-slayer'); ?></button>

        <hr>

        <label for="field_ids"><?php esc_html_e('Fields to Check (comma-separated field IDs):', 'gform-spam-slayer'); ?></label>
        <input type="text" name="field_ids" id="field_ids" placeholder="<?php esc_attr_e('e.g., 1.3, 1.6', 'gform-spam-slayer'); ?>" value="<?php echo esc_attr($stored_field_ids); ?>">

        <hr>
        <label for="regex_pattern"><?php esc_html_e('Regex Pattern:', 'gform-spam-slayer'); ?></label>
        <select name="regex_pattern" id="regex_pattern" onchange="updateExample()">
            <option value=""><?php esc_html_e('Custom Pattern', 'gform-spam-slayer'); ?></option>
            <?php foreach ($regex_patterns as $key => $pattern): ?>
                <option value="<?php echo esc_attr($pattern['pattern']); ?>" <?php selected($stored_regex_pattern, $pattern['pattern']); ?>>
                    <?php echo esc_html($pattern['description']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="custom_pattern" id="custom_pattern" placeholder="<?php esc_attr_e('Enter custom regex pattern', 'gform-spam-slayer'); ?>" value="<?php echo esc_attr($stored_custom_pattern); ?>">
        <div id="pattern-example">
            <?php
            // Display Example
            $effective_pattern = !empty($stored_custom_pattern) ? $stored_custom_pattern : $stored_regex_pattern;

            if (!empty($effective_pattern)) {
                echo '<p>' . esc_html__('Pattern Example:', 'gform-spam-slayer') . '</p>';
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
                    echo '<p>Enter custom regex and test to see matches here</p>';
                }
            } else {
                echo '<p>' . esc_html__('Select a pattern or enter a custom one', 'gform-spam-slayer') . '</p>';
            }
            ?>
        </div>

        <button type="button" class="button button-primary main-button" data-action="find_spam">
            <?php esc_html_e('Find Spam Entries (Full Scan)', 'gform-spam-slayer'); ?>
        </button>

        <button type="button" class="button main-button" data-action="test_spam">
            <?php esc_html_e('Find Spam Entries (Test 10)', 'gform-spam-slayer'); ?>
        </button>

        <button type="button" class="button button-secondary main-button" data-action="mark_spam">
            <?php esc_html_e('Mark Spam Entries', 'gform-spam-slayer'); ?>
        </button>

        <button type="button" class="button button-danger main-button" data-action="delete_spam">
            <?php esc_html_e('Delete Spam Entries', 'gform-spam-slayer'); ?>
        </button>

        <hr>

        <div id="field-list"></div>

    </form>
        <div id="debug-results" style="display:none;"></div>
    <div id="loading-indicator" style="display:none;">Loading...</div>
    <script>
        function updateExample() {
            var selectedPattern = document.getElementById("regex_pattern").value;
            var customPattern = document.getElementById("custom_pattern").value;

            if (selectedPattern) {
                var exampleText = '';

                // Check if selected pattern is one of the predefined patterns
                <?php foreach ($regex_patterns as $key => $pattern): ?>
                    if (selectedPattern == "<?php echo esc_attr($pattern['pattern']); ?>") {
                        exampleText = "<?php echo esc_js($pattern['example']); ?>";
                    }
                <?php endforeach; ?>

                document.getElementById("pattern-example").innerHTML = "<b>Example text using this pattern:</b><br> " + exampleText;
            } else if (customPattern) {
                document.getElementById("pattern-example").innerHTML = "<b>This Regex:</b> " + customPattern;
            } else {
                document.getElementById("pattern-example").innerHTML = "Please select or enter a pattern to view an example.";
            }
        }
    </script>
    <?php
    echo '</div>';
}

// Function to load fields via AJAX
add_action('wp_ajax_gform_spam_slayer_load_fields', 'gform_spam_slayer_load_fields');
function gform_spam_slayer_load_fields() {
    // Check nonce and permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(__('Insufficient permissions.', 'gform-spam-slayer'));
        return;
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'gforspsl-nonce')) {
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
add_action('wp_ajax_gform_spam_slayer_process_form', 'gform_spam_slayer_process_form');
function gform_spam_slayer_process_form() {
    // Verify nonce and user capabilities
    if (!current_user_can('manage_options')) {
        wp_send_json_error(__('Insufficient permissions.', 'gform-spam-slayer'));
        return;
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'gforspsl-nonce')) {
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

    switch ($sub_action) {
        case 'find_spam':
        case 'test_spam':
            $limit = ($sub_action === 'test_spam') ? 10 : 0;
            $result = process_spam_finding($form_id, $fields_to_check, $limit, $effective_pattern);
            break;
        case 'mark_spam':
            $result = process_spam_marking($form_id, $fields_to_check, $effective_pattern);
            break;
        case 'delete_spam':
            $result = process_spam_deletion($form_id);
            break;
        default:
            wp_send_json_error(__('Invalid action.', 'gform-spam-slayer'));
            return;
    }

    // Update stored settings
    update_option('gform_spam_slayer_form_id', $form_id);
    update_option('gform_spam_slayer_field_ids', $field_ids);
    update_option('gform_spam_slayer_regex_pattern', $regex_pattern);
    update_option('gform_spam_slayer_custom_pattern', $custom_pattern);
    update_option('gform_spam_slayer_action', $sub_action);

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
function gforspsl_process_spam_finding( $form_id, $fields_to_check, $limit = 0, $regex_pattern = '/^[a-zA-Z0-9]{15,}$/' ) {

    // Validation to ensure arguments are of expected data type
    if ( ! is_int( $form_id ) ) {
        return '<p class="gform-spam-slayer-error">Error: Form ID must be an integer.</p>';
    }
    if ( ! is_array( $fields_to_check ) ) {
        return '<p class="gform-spam-slayer-error">Error: Fields to check must be an array.</p>';
    }
    if ( ! is_int( $limit ) ) {
        return '<p class="gform-spam-slayer-error">Error: Limit must be an integer.</p>';
    }
    if ( ! is_string( $regex_pattern ) ) {
        return '<p class="gform-spam-slayer-error">Error: Regex pattern must be a string.</p>';
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
            return '<div class="gform-spam-slayer-notice"><p>' . esc_html__('There are no entries yet for this form. Please add some entries first.', 'gform-spam-slayer') . '</p></div>';
        }
    } catch (Exception $e) {
        return '<div class="gform-spam-slayer-error"><p>' . esc_html__('Error retrieving entries. Please try again.', 'gform-spam-slayer') . '</p></div>';
    }

    foreach ( $entries as $entry ) {
        $total_checked++;
        $is_spam = false;
        $entry_output = '';

        foreach ( $fields_to_check as $field_id ) {
            $field = GFFormsModel::get_field(GFAPI::get_form($form_id), $field_id);

            // Check if it's a Name field (has sub-fields)
            if ($field && $field->type === 'name') {
                $first_name_id = $field_id . '.3';  // First Name sub-field ID
                $last_name_id = $field_id . '.6';   // Last Name sub-field ID

                $first_name = isset($entry[ $first_name_id ]) ? $entry[ $first_name_id ] : '';
                $last_name = isset($entry[ $last_name_id ]) ? $entry[ $last_name_id ] : '';

                // Check First Name
                if (preg_match($regex_pattern, $first_name)) {
                    $is_spam = true;
                    $entry_output .= '<p class="gform-spam-slayer-spam-match">First Name (' . esc_html($first_name_id) . ') SPAM MATCH: ' . esc_html($first_name) . '</p>';
                } else {
                    $entry_output .= '<p>First Name (' . esc_html($first_name_id) . ') OK: ' . esc_html($first_name) . '</p>';
                }

                // Check Last Name
                if (preg_match($regex_pattern, $last_name)) {
                    $is_spam = true;
                    $entry_output .= '<p class="gform-spam-slayer-spam-match">Last Name (' . esc_html($last_name_id) . ') SPAM MATCH: ' . esc_html($last_name) . '</p>';
                } else {
                    $entry_output .= '<p>Last Name (' . esc_html($last_name_id) . ') OK: ' . esc_html($last_name) . '</p>';
                }
            } else {
                // Handle other field types

                if (isset($entry[$field_id])) {
                    $value = $entry[$field_id];
                    if (preg_match($regex_pattern, $value)) {
                        $is_spam = true;
                        $entry_output .= '<p class="gform-spam-slayer-spam-match">Field ID ' . esc_html($field_id) . ' SPAM MATCH: ' . esc_html($value ) . '</p>';
                    } else {
                        $entry_output .= '<p>Field ID ' . esc_html($field_id) . ' OK: ' . esc_html($value) . '</p>';
                    }
                } else {
                    $entry_output .= '<p class="gform-spam-slayer-warning">Warning: Field ID ' . esc_html($field_id) . ' not found in Entry #' . esc_html($entry['id']) . '</p>';
                }
            }
        }

        if ($is_spam) {
            $spam_count++;
            $output .= '<div class="gform-spam-slayer-entry gform-spam-slayer-spam-entry"><h4>Entry #' . esc_html( $entry['id'] ) . ' - <span class="gform-spam-slayer-spam-label">Identified as SPAM</span></h4>' . $entry_output . '</div>';
        } else {
            $output .= '<div class="gform-spam-slayer-entry"><h4>Entry #' . esc_html( $entry['id'] ) . ' - OK</h4>' . $entry_output . '</div>';
        }
    }

    $output .= '<p>Total entries checked: ' . esc_html( $total_checked ) . '</p>';
    $output .= '<p class="gform-spam-slayer-spam-count">Total spam entries found: ' . esc_html( $spam_count ) . '</p>';

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
function gforspsl_process_spam_marking( $form_id, $fields_to_check, $regex_pattern ) {
    global $wpdb;

    // Process entries with timeout handling through WordPress functions
    if (!wp_doing_ajax()) {
        wp_raise_memory_limit('admin');
    }

    $marked_count = 0;
    $batch_size = 50; // Process entries in batches

    // Basic validation for form_id
    if ( ! is_int( $form_id ) || $form_id <= 0 ) {
        return '<p style="color:red;">Error: Invalid Form ID.</p>';
    }

    // Get all entry IDs for the specified form that are *not* already marked as spam
    $entry_ids = GFAPI::get_entry_ids( $form_id, [ 'status' => 'active', 'field_filters' => [] ] ); // Get only active entries

    if ( empty( $entry_ids ) ) {
        return '<p>No entries found to mark as spam.</p>';
    }

    // Chunk the entry IDs into smaller batches
    $entry_id_chunks = array_chunk( $entry_ids, $batch_size );

    foreach ( $entry_id_chunks as $entry_id_chunk ) {
        foreach ( $entry_id_chunk as $entry_id ) {
            //try {
            if(is_numeric($entry_id)){
                GFAPI::update_entry_property( $entry_id, 'status', 'spam' );
                $marked_count++;
            }else{
                /* translators: %s: Entry ID */
                $error = new WP_Error('invalid_entry', sprintf(__('Invalid entry ID: %s', 'gform-spam-slayer'), $entry_id));
            }
            /*} catch (Exception $e) {
                error_log("Error marking entry ".$entry_id." as spam: ".$e->getMessage());
                // Potentially handle the error (e.g., log it, skip the entry, etc.)
                continue; // Skip to the next entry
            }*/

        } // inner foreach (entries in chunk)

    } // outer foreach (chunks)


    return '<p>Successfully marked ' . esc_html( $marked_count ) . ' entries as spam.</p>';
}

/**
 * Function to delete spam entries
 *
 * @param int $form_id Gravity Forms form ID
 *
 * @return string HTML output of spam deletion results
 */
function gforspsl_process_spam_deletion( $form_id ) {
    global $wpdb;

    // Process entries with timeout handling through WordPress functions
    if (!wp_doing_ajax()) {
        wp_raise_memory_limit('admin');
    }

    $deleted_count = 0;
    $batch_size = 50; // Process entries in batches

    // Basic validation for form_id
    if ( ! is_int( $form_id ) || $form_id <= 0 ) {
        return '<p style="color:red;">Error: Invalid Form ID.</p>';
    }

    // Get all entry IDs for the specified form that are currently marked as spam
    $entry_ids = GFAPI::get_entry_ids( $form_id, [ 'status' => 'spam', 'field_filters' => [] ] ); // Get spam entries

    if ( empty( $entry_ids ) ) {
        return '<p>No spam entries found to delete.</p>';
    }

    // Chunk the entry IDs into smaller batches
    $entry_id_chunks = array_chunk( $entry_ids, $batch_size );

    foreach ( $entry_id_chunks as $entry_id_chunk ) {
        foreach ( $entry_id_chunk as $entry_id ) {
           // try{
            if(is_numeric($entry_id)){
                GFAPI::delete_entry( $entry_id );
                $deleted_count++;
            }else{
                /* translators: %s: Entry ID */
                $error = new WP_Error('invalid_entry', sprintf(__('Invalid entry ID: %s', 'gform-spam-slayer'), $entry_id));
            }
        /*} catch (Exception $e) {
                error_log("Error deleting entry ".$entry_id.": ".$e->getMessage());
                // Potentially handle the error (e.g., log it, skip the entry, etc.)
                continue; // Skip to the next entry
            }*/
        } // inner foreach (entries in chunk)
    } // outer foreach (chunks)

    return '<p>Successfully deleted ' . esc_html( $deleted_count ) . ' spam entries.</p>';
}
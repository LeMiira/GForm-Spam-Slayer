(function($) {
    $(document).ready(function() {
        // Function to load fields via AJAX
        window.loadFields = function(formId) {
            const fieldList = $('#field-list');
            fieldList.removeClass('active');

            $.ajax({
                type: 'POST',
                url: gform_spam_slayer_params.ajax_url,
                data: {
                    action: 'gform_spam_slayer_load_fields',
                    form_id: formId,
                    nonce: gform_spam_slayer_params.nonce
                },
                success: function(response) {
                    if (response.success) {
                        fieldList.html(response.data).addClass('active');
                    } else {
                        fieldList.html('<div class="error-message">' + response.data + '</div>').addClass('active');
                        console.error('Error:', response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                }
            });
        };

        // Call loadFields when the form selection changes
        $('#form_id').on('change', function() {
            loadFields($(this).val());
        });

        // Function to update example text based on selected pattern or custom input
        function updateExample() {
            var selectedPattern = $('#regex_pattern').val();
            var customPattern = $('#custom_pattern').val();
            var exampleDiv = $('#pattern-example');

            if (selectedPattern) {
                var exampleText = '';
                var patterns = gform_spam_slayer_params.regex_patterns;

                for (var key in patterns) {
                    if (patterns.hasOwnProperty(key)) {
                        if (patterns[key].pattern === selectedPattern) {
                            exampleText = patterns[key].example;
                            break;
                        }
                    }
                }

                exampleDiv.html('<b>' + gform_spam_slayer_params.i18n.example_text + '</b><br> ' + exampleText);
            } else if (customPattern) {
                exampleDiv.html('<b>' + gform_spam_slayer_params.i18n.this_regex + '</b> ' + customPattern);
            } else {
                exampleDiv.html(gform_spam_slayer_params.i18n.select_pattern);
            }
        }

        // Call updateExample on page load, select change, or custom input keyup
        $('#regex_pattern').on('change', function() {
            updateExample();
        });

        $('#custom_pattern').on('input keyup change', function() {
            updateExample();
        });

        // Initialize example text
        updateExample();

        // Process form submission via AJAX
        $('.main-button').on('click', function(e) {
            e.preventDefault();

            const formId = $('#form_id').val();
            const fieldIds = $('#field_ids').val();
            const regexPattern = $('#regex_pattern').val();
            const customPattern = $('#custom_pattern').val();
            const subAction = $(this).data('action');
            const debugResults = $('#debug-results');
            const loadingIndicator = $('#loading-indicator');

            // Validation
            if (!fieldIds) {
                alert('Error: Please select fields to check for spam.');
                return;
            }

            if (!regexPattern && !customPattern) {
                alert('Error: Please select a regex pattern.');
                return;
            }
            
            // Hide debug results before showing loading indicator
            debugResults.hide();
            
            // Show loading indicator
            loadingIndicator.show();

            $.ajax({
                type: 'POST',
                url: gform_spam_slayer_params.ajax_url,
                data: {
                    action: 'gform_spam_slayer_process_form',
                    form_id: formId,
                    field_ids: fieldIds,
                    regex_pattern: regexPattern,
                    custom_pattern: customPattern,
                    sub_action: subAction,
                    nonce: gform_spam_slayer_params.nonce
                },
                success: function(response) {
                    loadingIndicator.hide();
                    if (response.success) {
                        debugResults.html(response.data).show();
                    } else {
                        alert('Error: ' + response.data);
                        console.error('Error:', response.data);
                    }
                },
                error: function(xhr, status, error) {
                    loadingIndicator.hide();
                    console.error('AJAX Error:', status, error);
                    var errorMessage = xhr.responseJSON ? xhr.responseJSON.data : 'Server error occurred. Please check if Gravity Forms is active and you have permissions.';
                    debugResults.html('<div class="error-message">' + errorMessage + '</div>').show();
                }
            });

        });

        // Load fields for the first selected form on page load
        var initialFormId = $('#form_id').val();
        if (initialFormId) {
            loadFields(initialFormId);
        }
    });
})(jQuery);

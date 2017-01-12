<?php

/*
 * Returns configuration for this plugin
 *
 * @return array
 */
function woochimp_plugin_settings()
{
    $settings = array(
        'woochimp' => array(
            'title' => __('MailChimp', 'woochimp'),
            'page_title' => __('MailChimp', 'woochimp'),
            'capability' => 'manage_options',
            'slug' => 'woochimp',
            'children' => array(
                'integration' => array(
                    'title' => __('Integration', 'woochimp'),
                    'icon' => '<i class="fa fa-cogs" style="font-size: 0.8em;"></i>',
                    'children' => array(
                        'general' => array(
                            'title' => __('General', 'woochimp'),
                            'children' => array(
                                'enabled' => array(
                                    'title' => __('Enable Integration', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => 0,
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>Enable or disable integration. If disabled, none of the features will be active.</p>', 'woochimp'),
                                ),
                                'api_key' => array(
                                    'title' => __('MailChimp API key', 'woochimp'),
                                    'type' => 'text',
                                    'default' => '',
                                    'validation' => array(
                                        'rule' => 'function',
                                        'empty' => array('enabled'),
                                    ),
                                    'hint' => __('<p>API key is required for this plugin to communicate with MailChimp servers.</p>', 'woochimp'),
                                ),
                            ),
                        ),
                        'settings' => array(
                            'title' => __('Settings', 'woochimp'),
                            'children' => array(
                                'enable_webhooks' => array(
                                    'title' => __('Enable Webhooks', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => 0,
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                   'hint' => __('<p>If enabled and configured, MailChimp will send this plugin instant updates about changes on your account.</p> <p>This is useful as it extends functionality of this plugin. For example, you can configure this plugin not to auto-subscribe user who has just manually unsubscribed from your list.</p>', 'woochimp'),
                                ),
                                'webhook_url' => array(
                                    'title' => __('Your Webhook URL', 'woochimp'),
                                    'type' => 'text',
                                    'default' => site_url('/?woochimp-webhook-call'),
                                    'validation' => array(
                                        'rule' => 'string',
                                        'empty' => true
                                    ),
                                   'hint' => sprintf(__('<p>Webhook must be set up on MailChimp side as well. Login to your MailChimp account, go to one of your lists, click on Settings / Webhooks and add this URL as a Callback URL for a new Webhook.</p> <p>You can repeat the same process for all lists that you are going to use.</p>', 'woochimp')),
                                ),
                                'enable_log' => array(
                                    'title' => __('Enable Woochimp Log', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => '0',
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                   'hint' => __('<p>If enabled, Woochimp will write a log of MailChimp errors (latest 50 error entries will be saved).</p>', 'woochimp'),
                                ),
                            ),
                        ),
                        'status' => array(
                            'title' => __('Status', 'woochimp'),
                            'children' => array(
                            ),
                        ),
                    ),
                ),
                'ecomm' => array(
                    'title' => __('Ecommerce 360', 'woochimp'),
                    'icon' => '<i class="fa fa-circle-o-notch" style="font-size: 0.7em;"></i>',
                    'children' => array(
                        'ecomm_enable' => array(
                            'title' => __('Ecommerce 360', 'woochimp'),
                            'children' => array(
                                'send_order_data' => array(
                                    'title' => __('Enable Ecommerce 360', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => 0,
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>If enabled, order data will be sent to MailChimp as soon as order is marked as completed.</p>', 'woochimp'),
                                ),
                                'delete_order_data' => array(
                                    'title' => __('Delete Cancelled Order Data', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => 0,
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>Delete order data from MailChimp if order is cancelled or refunded.</p>', 'woochimp'),
                                ),
                            ),
                        ),
                    ),
                ),
                'checkout_checkbox' => array(
                    'title' => __('Checkbox', 'woochimp'),
                    'icon' => '<i class="fa fa-check-square-o" style="font-size: 0.8em;"></i>',
                    'children' => array(
                        'subscription_checkout_checkbox' => array(
                            'title' => __('Subscribe On Checkout with Checkbox', 'woochimp'),
                            'children' => array(
                                'checkout_checkbox_subscribe_on' => array(
                                    'title' => __('Subscribe on', 'woochimp'),
                                    'type' => 'dropdown',
                                    'default' => '4',
                                    'validation' => array(
                                        'rule' => 'option',
                                        'empty' => false
                                    ),
                                    'values' => array(
                                        '4' => __('Disabled', 'woochimp'),
                                        '1' => __('Order placed', 'woochimp'),
                                        '3' => __('Payment received', 'woochimp'),
                                        '2' => __('Order completed', 'woochimp'),
                                    ),
                                    'hint' => __('<p>Choose whether to subscribe user as soon as order is placed (better for marketing) or wait until payment received or order is marked as completed (better for providing access to premium content delivered by email).</p>', 'woochimp'),
                                ),
                                'text_checkout' => array(
                                    'title' => __('Label', 'woochimp'),
                                    'type' => 'text',
                                    'default' => __('Subscribe to our newsletter', 'woochimp'),
                                    'validation' => array(
                                        'rule' => 'string',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>Text to display to customers next to singup checkbox.</p>', 'woochimp'),
                                ),
                                'checkbox_position' => array(
                                    'title' => __('Checkbox position', 'woochimp'),
                                    'type' => 'dropdown',
                                    'default' => 'woocommerce_checkout_after_customer_details',
                                    'validation' => array(
                                        'rule' => 'option',
                                        'empty' => false
                                    ),
                                    'values' => array(
                                        'woocommerce_checkout_before_customer_details'  => __('Above customer details', 'woochimp'),
                                        'woocommerce_checkout_after_customer_details'   => __('Below customer details', 'woochimp'),
                                        'woocommerce_review_order_before_submit'        => __('Order review above submit', 'woochimp'),
                                        'woocommerce_review_order_after_submit'         => __('Order review below submit', 'woochimp'),
                                        'woocommerce_review_order_before_order_total'   => __('Order review above total', 'woochimp'),
                                        'woocommerce_checkout_billing'                  => __('Above billing details', 'woochimp'),
                                        'woocommerce_checkout_shipping'                 => __('Above shipping details', 'woochimp'),
                                        'woocommerce_after_checkout_billing_form'       => __('Below Checkout billing form', 'woochimp'),
                                    ),
                                    'hint' => __('<p>Select checkbox position. Not all positions may be available on all themes and some may perform badly with some themes.</p>', 'woochimp'),
                                ),
                                'default_state' => array(
                                    'title' => __('Default state', 'woochimp'),
                                    'type' => 'dropdown',
                                    'default' => '1',
                                    'validation' => array(
                                        'rule' => 'option',
                                        'empty' => false
                                    ),
                                    'values' => array(
                                        '1' => __('Checked', 'woochimp'),
                                        '2' => __('Not checked', 'woochimp'),
                                    ),
                                    'hint' => __('<p>Select default state of the signup checkbox.</p> <p>If you select <strong>Checked</strong>, customers will have to unselect the checkbox in order not to be subscribed to the list.</p>', 'woochimp'),
                                ),
                                'checkout_groups_method' => array(
                                    'title' => __('Add to groups', 'woochimp'),
                                    'type' => 'dropdown_optgroup',
                                    'default' => 'auto',
                                    'validation' => array(
                                        'rule' => 'option',
                                        'empty' => false
                                    ),
                                    'values' => array(
                                        array(
                                            'title'     => __('Automatically', 'woochimp'),
                                            'children'  => array(
                                                'auto'  => __('All groups selected below', 'woochimp'),
                                            ),
                                        ),
                                        array(
                                            'title'     => __('Allow users to select (optional)', 'woochimp'),
                                            'children'  => array(
                                                'multi'         => __('Checkbox group for each grouping', 'woochimp'),
                                                'single'        => __('Radio button group for each grouping', 'woochimp'),
                                                'select'        => __('Select field (dropdown) for each grouping', 'woochimp'),
                                            ),
                                        ),
                                        array(
                                            'title'     => __('Require users to select (required)', 'woochimp'),
                                            'children'  => array(
                                                'single_req'    => __('Radio button group for each grouping', 'woochimp'),
                                                'select_req'    => __('Select field (dropdown) for each grouping', 'woochimp'),
                                            ),
                                        )
                                    ),
                                    'hint' => __('<p>Select how you would like interest groups to work with this form - you can either add all selected interest groups to subscribers profile by default or allow your visitors to manually select some.</p>', 'woochimp'),
                                ),
                                'do_not_resubscribe_checkbox' => array(
                                    'title' => __('Don\'t resubscribe unsubscribed', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => '0',
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>If selected, users who have unsubscribed from the selected list in the past, will not be automatically subscribed again.</p> <p>This requires MailChimp Webhook integration to be active.</p>', 'woochimp'),
                                ),
                                'replace_groups_checkout_checkbox' => array(
                                    'title' => __('Replace groups on MailChimp', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => '1',
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>If selected, interest groups provided above will replace any other interest groups for existing users.</p> <p>Otherwise, provided groups will be merged with existing interest groups on subscriber profile.</p>', 'woochimp'),
                                ),
                                'double_checkout_checkbox' => array(
                                    'title' => __('Double opt-in', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => 0,
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>Enable standard MailChimp double opt-in functionality. Customers will be sent an email and required to confirm subscription.</p>', 'woochimp'),
                                ),
                                'welcome_checkout_checkbox' => array(
                                    'title' => __('Send welcome email', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => 0,
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>If enabled, customers will receive welcome email as configured in MailChimp settings.</p>', 'woochimp'),
                                ),
                            ),
                        ),
                        'subscription_checkout_list_groups_checkbox' => array(
                            'title' => __('Lists, Groups And Fields', 'woochimp'),
                            'children' => array(
                            ),
                        ),
                    ),
                ),
                'checkout_auto' => array(
                    'title' => __('Automatic', 'woochimp'),
                    'icon' => '<i class="fa fa-star-o" style="font-size: 0.8em;"></i>',
                    'children' => array(
                        'subscription_checkout_auto' => array(
                            'title' => __('Automatically Subscribe On Checkout', 'woochimp'),
                            'children' => array(
                                'checkout_auto_subscribe_on' => array(
                                    'title' => __('Subscribe on', 'woochimp'),
                                    'type' => 'dropdown',
                                    'default' => '4',
                                    'validation' => array(
                                        'rule' => 'option',
                                        'empty' => false
                                    ),
                                    'values' => array(
                                        '4' => __('Disabled', 'woochimp'),
                                        '1' => __('Order placed', 'woochimp'),
                                        '3' => __('Payment received', 'woochimp'),
                                        '2' => __('Order completed', 'woochimp'),
                                    ),
                                    'hint' => __('<p>Choose whether to subscribe user as soon as order is placed (better for marketing) or wait until payment received or order is marked as completed (better for providing access to premium content delivered by email).</p>', 'woochimp'),
                                ),
                                'do_not_resubscribe_auto' => array(
                                    'title' => __('Don\'t resubscribe unsubscribed', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => '0',
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>If selected, users who have unsubscribed from the selected list in the past, will not be automatically subscribed again.</p> <p>This requires MailChimp Webhook integration to be active.</p>', 'woochimp'),
                                ),
                                'replace_groups_checkout_auto' => array(
                                    'title' => __('Replace groups on MailChimp', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => '1',
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>If selected, interest groups provided above will replace any other interest groups for existing users.</p> <p>Otherwise, provided groups will be merged with existing interest groups on subscriber profile.</p>', 'woochimp'),
                                ),
                                'double_checkout_auto' => array(
                                    'title' => __('Double opt-in', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => 0,
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>Enable standard MailChimp double opt-in functionality. Customers will be sent an email and required to confirm subscription.</p>', 'woochimp'),
                                ),
                                'welcome_checkout_auto' => array(
                                    'title' => __('Send welcome email', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => 0,
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>If enabled, customers will receive welcome email as configured in MailChimp settings.</p>', 'woochimp'),
                                ),
                            ),
                        ),
                        'subscription_checkout_list_groups_auto' => array(
                            'title' => __('Lists, Groups And Fields', 'woochimp'),
                            'children' => array(
                            ),
                        ),
                    ),
                ),
                'widget' => array(
                    'title' => __('Widget', 'woochimp'),
                    'icon' => '<i class="fa fa-puzzle-piece" style="font-size: 0.8em;"></i>',
                    'children' => array(
                        'subscription_widget' => array(
                            'title' => __('Signup Form Widget', 'woochimp'),
                            'children' => array(
                                'enabled_widget' => array(
                                    'title' => __('Enable', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => 0,
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>If enabled, a signup form will be displayed in a widget.</p> <p>You still must insert <strong>MailChimp Signup</strong> widget to one of the sidebars.</p>', 'woochimp'),
                                ),
                                'replace_groups_widget' => array(
                                    'title' => __('Replace groups on MailChimp', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => '1',
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>If selected, interest groups provided above will replace any other interest groups for existing users.</p> <p>Otherwise, provided groups will be merged with existing interest groups on subscriber profile.</p>', 'woochimp'),
                                ),
                                'double_widget' => array(
                                    'title' => __('Double opt-in', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => 0,
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>Enable standard MailChimp double opt-in functionality. Customers will be sent an email and required to confirm subscription.</p>', 'woochimp'),
                                ),
                                'welcome_widget' => array(
                                    'title' => __('Send welcome email', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => 0,
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>If enabled, customers will receive welcome email as configured in MailChimp settings.</p>', 'woochimp'),
                                ),
                            ),
                        ),
                        'subscription_widget_list_groups' => array(
                            'title' => __('List & Groups', 'woochimp'),
                            'children' => array(
                                'list_widget' => array(
                                    'title' => __('Mailing list', 'woochimp'),
                                    'type' => 'dropdown',
                                    'default' => '',
                                    'validation' => array(
                                        'rule' => 'option',
                                        'empty' => array('enabled_widget')
                                    ),
                                    'values' => array(),
                                    'hint' => __('<p>Select a MailChimp list to subscribe users to. You must choose a valid list to enable this feature.</p>', 'woochimp'),
                                ),
                                'groups_widget' => array(
                                    'title' => __('Groups', 'woochimp'),
                                    'type' => 'dropdown',
                                    'default' => array(),
                                    'validation' => array(
                                        'rule' => 'multiple_any',
                                        'empty' => true
                                    ),
                                    'values' => array(),
                                    'hint' => __('<p>Select one or multiple MailChimp groups to add subscribers to. Mailing list must be chosen for a list of groups to appear.</p>', 'woochimp'),
                                ),
                            ),
                        ),
                        'subscription_widget_fields' => array(
                            'title' => __('Fields', 'woochimp'),
                            'children' => array(
                            ),
                        ),
                        'subscription_widget_style' => array(
                            'title' => __('Style', 'woochimp'),
                            'children' => array(
                                'widget_show_labels_inline' => array(
                                    'title' => __('Show field labels inline', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => 1,
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>If enabled, field labels will be shown as placeholders for fields. Otherwise, they will be displayed separately before fields.</p>', 'woochimp'),
                                ),
                                'widget_skin' => array(
                                    'title' => __('Signup form style', 'woochimp'),
                                    'type' => 'dropdown',
                                    'default' => '2',
                                    'validation' => array(
                                        'rule' => 'option',
                                        'empty' => false
                                    ),
                                    'values' => array(
                                        '1' => __('Inherit theme style', 'woochimp'),
                                        '2' => __('WooChimp General', 'woochimp'),
                                    ),
                                    'hint' => __('<p>Select a style to apply to your signup form. If you choose to inherit the style from your current theme, styling will completely depend on it - in some cases you may get undesired results.</p>', 'woochimp'),
                                ),
                                'widget_css' => array(
                                    'title' => __('Custom CSS', 'woochimp'),
                                    'type' => 'textarea',
                                    'default' => '.woochimp_wg {}',
                                    'validation' => array(
                                        'rule' => 'string',
                                        'empty' => true
                                    ),
                                    'hint' => __('<p>You can further customize the appearance of the signup form by adding custom CSS to this field.</p> <p>To make changes to the style, simply use CSS class <strong>woochimp_wg</strong> as a basis.</p>', 'woochimp'),
                                ),
                            ),
                        ),
                        'subscription_widget_about' => array(
                            'title' => __('Usage', 'woochimp'),
                            'children' => array(
                            ),
                        ),
                    ),
                ),
                'shortcode' => array(
                    'title' => __('Shortcode', 'woochimp'),
                    'icon' => '<i class="fa fa-code" style="font-size: 0.8em;"></i>',
                    'children' => array(
                        'subscription_shortcode' => array(
                            'title' => __('Signup Shortcode', 'woochimp'),
                            'children' => array(
                                'enabled_shortcode' => array(
                                    'title' => __('Enable', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => 0,
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>If enabled, a signup form will be displayed wherever you place a shortcode.</p>', 'woochimp'),
                                ),
                                'replace_groups_shortcode' => array(
                                    'title' => __('Replace groups on MailChimp', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => '1',
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>If selected, interest groups provided above will replace any other interest groups for existing users.</p> <p>Otherwise, provided groups will be merged with existing interest groups on subscriber profile.</p>', 'woochimp'),
                                ),
                                'double_shortcode' => array(
                                    'title' => __('Double opt-in', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => 0,
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>Enable standard MailChimp double opt-in functionality. Customers will be sent an email and required to confirm subscription.</p>', 'woochimp'),
                                ),
                                'welcome_shortcode' => array(
                                    'title' => __('Send welcome email', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => 0,
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>If enabled, customers will receive welcome email as configured in MailChimp settings.</p>', 'woochimp'),
                                ),
                            ),
                        ),
                        'subscription_widget_list_groups' => array(
                            'title' => __('List & Groups', 'woochimp'),
                            'children' => array(
                                'list_shortcode' => array(
                                    'title' => __('Mailing list', 'woochimp'),
                                    'type' => 'dropdown',
                                    'default' => '',
                                    'validation' => array(
                                        'rule' => 'option',
                                        'empty' => array('enabled_shortcode')
                                    ),
                                    'values' => array(),
                                    'hint' => __('<p>Select a MailChimp list to subscribe users to. You must choose a valid list to enable this feature.</p>', 'woochimp'),
                                ),
                                'groups_shortcode' => array(
                                    'title' => __('Groups', 'woochimp'),
                                    'type' => 'dropdown',
                                    'default' => array(),
                                    'validation' => array(
                                        'rule' => 'multiple_any',
                                        'empty' => true
                                    ),
                                    'values' => array(),
                                    'hint' => __('<p>Select one or multiple MailChimp groups to add subscribers to. Mailing list must be chosen for a list of groups to appear.</p>', 'woochimp'),
                                ),
                            ),
                        ),
                        'subscription_shortcode_fields' => array(
                            'title' => __('Fields', 'woochimp'),
                            'children' => array(
                            ),
                        ),
                        'subscription_shortcode_style' => array(
                            'title' => __('Style', 'woochimp'),
                            'children' => array(
                                'shortcode_show_labels_inline' => array(
                                    'title' => __('Show field labels inline', 'woochimp'),
                                    'type' => 'checkbox',
                                    'default' => 1,
                                    'validation' => array(
                                        'rule' => 'bool',
                                        'empty' => false
                                    ),
                                    'hint' => __('<p>If enabled, field labels will be shown as placeholders for fields. Otherwise, they will be displayed separately before fields.</p>', 'woochimp'),
                                ),
                                'shortcode_skin' => array(
                                    'title' => __('Signup form style', 'woochimp'),
                                    'type' => 'dropdown',
                                    'default' => '2',
                                    'validation' => array(
                                        'rule' => 'option',
                                        'empty' => false
                                    ),
                                    'values' => array(
                                        '1' => 'Inherit theme style',
                                        '2' => 'WooChimp General',
                                    ),
                                    'hint' => __('<p>Select a style to apply to your signup form. If you choose to inherit the style from your current theme, styling will completely depend on it - in some cases you may get undesired results.</p>', 'woochimp'),
                                ),
                                'shortcode_css' => array(
                                    'title' => __('Custom CSS', 'woochimp'),
                                    'type' => 'textarea',
                                    'default' => '.woochimp_sc {}',
                                    'validation' => array(
                                        'rule' => 'string',
                                        'empty' => true
                                    ),
                                    'hint' => __('You can further customize the appearance of the signup form by adding custom CSS to this field.</p> <p>To make changes to the style, simply use CSS class <strong>woochimp_sc</strong> as a basis.</p>', 'woochimp'),
                                ),
                            ),
                        ),
                        'subscription_shortcode_about' => array(
                            'title' => __('Usage', 'woochimp'),
                            'children' => array(
                            ),
                        ),
                    ),
                ),
                'translation' => array(
                    'title' => __('Labels', 'woochimp'),
                    'icon' => '<i class="fa fa-font" style="font-size: 0.8em;"></i>',
                    'children' => array(
                        'form_field_translation' => array(
                            'title' => __('Frontend forms', 'woochimp'),
                            'children' => array(
                                'label_subscribe_widget' => array(
                                    'title' => __('Widget form title', 'woochimp'),
                                    'type' => 'text',
                                    'default' => __('Newsletter', 'woochimp'),
                                    'validation' => array(
                                        'rule' => 'string',
                                        'empty' => true
                                    ),
                                ),
                                'label_subscribe_shortcode' => array(
                                    'title' => __('Shortcode form title', 'woochimp'),
                                    'type' => 'text',
                                    'default' => __('Sign up for our newsletter!', 'woochimp'),
                                    'validation' => array(
                                        'rule' => 'string',
                                        'empty' => true
                                    ),
                                ),
                                'label_button' => array(
                                    'title' => __('Button label', 'woochimp'),
                                    'type' => 'text',
                                    'default' => __('Sign up', 'woochimp'),
                                    'validation' => array(
                                        'rule' => 'string',
                                        'empty' => true
                                    ),
                                ),
                                'label_email' => array(
                                    'title' => __('Email', 'woochimp'),
                                    'type' => 'text',
                                    'default' => __('Email', 'woochimp'),
                                    'validation' => array(
                                        'rule' => 'string',
                                        'empty' => true
                                    ),
                                ),
                                'label_error' => array(
                                    'title' => __('Error', 'woochimp'),
                                    'type' => 'text',
                                    'default' => __('Something went wrong. Please try again.', 'woochimp'),
                                    'validation' => array(
                                        'rule' => 'string',
                                        'empty' => true
                                    ),
                                ),
                                'label_success' => array(
                                    'title' => __('Success', 'woochimp'),
                                    'type' => 'text',
                                    'default' => __('Thank you for signing up!', 'woochimp'),
                                    'validation' => array(
                                        'rule' => 'string',
                                        'empty' => true
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    );

    return $settings;
}

?>

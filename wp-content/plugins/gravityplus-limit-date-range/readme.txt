=== Gravity Forms Limit Date Range ===
Contributors: gravityplus, naomicbush
Donate link: https://gravityplus.pro/gravity-forms-limit-date-range
Tags: form, forms, gravity, gravity form, gravity forms, gravityforms, date, dates, datepicker
Requires at least: 3.9
Tested up to: 4.2
Stable tag: 2.1.1

Limit the date range for a Gravity Forms Date field (Date Picker or Dropdown type)

== Description ==

Limit the date range for a Gravity Forms Date field (Date Picker or Dropdown type). This plugin requires JavaScript.

== Installation ==

This section describes how to install and setup the Gravity Forms Limit Date Range utility.

1. Make sure that Gravity Forms is activated
2. Either upload the plugin in your WordPress dashboard (Plugins->Add New->Upload Plugin button) or FTP upload the `gravityplus-limit-date-range` folder to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Create a form with a date field, and you'll see the new 'Limit date range' option in the date field settings for a Date Picker or Dropdown type

== Frequently Asked Questions ==

= Do I need to have my own copy of Gravity Forms for this plugin to work? =
Yes, you need to install the [Gravity Forms Plugin](http://gravityforms.com "visit the Gravity Forms website") for this plugin to work.

= Does this version work with the latest version of Gravity Forms? =
Yes

== Screenshots ==

== Changelog ==

= 2.1.1 =
* Fix fatal error when date is empty

= 2.1.0 =
* Update date dropdown for GF1.9

= 2.0.1 =
* Add support for Dropdown Date field
* Add API method is_datedropdown_date_field
* Change API method get_datepicker_fields_with_date_range_limit( $form ) to get_date_fields_with_date_range_limit( $form, $type, $only_return_type )

= 1.0.0 =
Let's get it started!

* Add limit date range option for Date Picker Date field
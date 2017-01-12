=== WooCommerce Checkout Manager ===
Contributors: nmedia
Tags: woocommerce, pesonalized products, variations
Donate link: http://www.najeebmedia.com/donate
Requires at least: 3.5
Tested up to: 4.5
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html


== Description ==

Woocommerce Checkout Field Editor plugin allow site admin to edit, add and remove Checkout’s fields. It is divided into three sections like Billing, Shipping and Order. Billing and shipping  are core fields and they can be managed like:

Change billing and shipping fields labels
Change billing and shipping fields description/placeholder text
Set billing and shipping fields  required/not-required
Arrange billing and shipping fields order (drag & drop)
Control billing and shipping fields
Add fields
Edit fields
Remove fields

== Installation ==
1. Upload plugin directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the `Plugins` menu in WordPress
3. After activation, you can set options from `Checkout Manager` menu


== Changelog ==
= 4.9 September 21, 2016 =
* Bug fixed: Error message was showing slashes if it contains ('), fixed now
* Bug fixed: Billing extra fields were not adding to email, now it's
* Bug fixed: Textarea fields were not working with conditional rules, not its fixed.
= 4.8 August 9, 2016 =
* Bug fixed: Warnings were generating in email when meta added, now it's fixed.
= 4.7 July 17, 2016 =
* Bug fixed: File input validation bug fixed
* Bug fixed: order comments were not showing in quick view.
= 4.6 Jun 5, 2016 =
* Bug fixed: Checkbox input validation issue fixed
* Bug fixed: Warning removed while validation
= 4.5 Jun 1, 2016 =
* Bug fixed: Conditional logic issue fixed for hidden field in Billing and Shipping Section
* Bug fixed: Image type input price were not adding to total, now it's fixed
* Bug fixed: Section input closing issue fixed
* Bug fixed: Multiple select field now working
* Tweeks: Error reporting set to 0 to prevent conflicts.
= 4.4 May 24, 2016 =
* Bug fixed: Select input field was not hiding when used as Conditional, fixed
* Tweeks: Some Warnings removed.
= 4.3 May 1, 2016 =
* Bug fixed: Order Notes field were duplicated and spellings were not correct :), fixed
= 4.2 April 14, 2016 =
* Bug fixed: [Potential] Feature image of products was not displaying due to media library conflict, now it's resolved.
= 4.1 March 24, 2016 =
* Feature Added: Much demanded feature Field Visibility based on Product, Category and User Added
* Feature Added: New DOB special input added
* Feature Add: New Color Palette input added
= 4.0 6 March, 2016 =
* Main Feature: Now conditional logic can be used for Billing and Shipping Field
* Feature: Placeholder text for Text/Textarea/Date/Number type fields.
* Feature: Year range can be added in Date type input.
* Bug Remove: Space added between Radio/Checkbox and options
= 3.9.2 January 14, 2016 =
* New admin UI added for extra fields
* Time input field is added
= 3.9.1 December 12, 2015 =
* Bug Fixed: Dynamic price issue fixed on checkbox page
= 3.9  November 24, 2015 =
* Bug Fixed: Conditional Logic issue fixed with Select type input
= 3.8  November 6, 2015 =
* New Input: Radio type input added 
= 3.7  17 September, 2015 =
* Bug fixed: Admin UI issue fixed while drag and drop extra fields
= 3.6 2015 =
* Feature: Dynamic price for select checkbox will be added to cart
* Featured: checkout total is updated when address fields are changed
* Feature: WPML Compatible
= 3.5 August 4, 2015 =
* BUG fixed: Validation disabled when field is hidden due to conditional logic
* BUG fixed: Double star removed from required fields
= 3.4 July 11, 2015 =
* BUG fixed: when only Billing or Shipping fields are provided it will render other fields from core.
= 3.3 June 6, 2015 =
* Capability change in admin menu to manage_options
* Warning and Notices removed
* Multiple/Autocomplete input added
* Layout fixed with classes form-row, form-row-first, form-row-last and form-row-wide

= 3.2 8/6/2014 =
* Feature: set default values for text and textarea
* Feature: Number type input added with max, min and step controlling
* BUG fixed: warnings, undefined variables and indexes errors have been removed
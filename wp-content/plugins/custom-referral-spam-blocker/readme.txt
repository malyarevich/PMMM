=== Custom Referral Spam Blocker ===
Contributors: csmicfool
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=C2A8GHH997USA
Tags: spam, referral spam, referrals, SEO
Requires at least: 3.0.1
Tested up to: 4.7
Stable tag: 1.4.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Custom Referral Spam Blocker gives you the control to ensure that dishonest referral sources are blocked from Google Analytics.

== Description ==

Custom Referral Spam Blocker gives you the control to ensure that dishonest referral sources are blocked from Google Analytics.

We provide a strong default set of spam referral sources and block them for you.  Referrers can quickly be added in moments using the WordPress Dashboard.

*At this time, we are unable to block referral spam when used in conjunction with most CDN configurations.*

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Use the Custom Referral Spam Blocker Settings screen to manage referral sources.

== Frequently Asked Questions ==

= If I customize my list and new default sources are added, will my list be updated? =

Future versions are planned with improved support for appending to default versus replacing it with your own list.

= What is the correct way to add new domains to the block list? =

This plugin blocks requests for all pages from a given domain or subdomain. Each domain should be added on a new line. For example, if you add "imaspammerandimok.com" to the list, requests with referrers such as "http://imaspammerandimok.com/spamallnight/spamallday.html" will also be blocked.  Do not include the protocol (https://, http://) or path (/folder/page.html) when adding new domains. Subdomains, however, must currently be entered individually.

= What happens to the evil spammers? =

Their requests are intentionally blocked from continuing to load any WordPress code and get directed away from your site. If they have a puppy, it cries.

== Screenshots ==

1. This is the options screen.

== Changelog ==

= 0.1 =
* Initial release.

= 0.2 =
* Bug fixes.

= 0.3 =
* Added optional data sharing features.

= 0.4 =
* Updated block list

= 0.5 =
* Updated block list

= 0.6 =
* Updated block list

= 0.7 =
* Updated block list

= 0.7.1 =
* Updated block list

= 1.0 =
* Non-destructive referer list upgrade.  Adds a more seamless upgrade path when database releases are available.  Safely merges custom user-defined spammers with those in the maintained list.

= 1.0.3 =
* Bug fixes for infinite redirects related to short or blank domains appearing in custom block lists.  As a result, only exact matches will be blocked and variants will need to be added to lists manually.

= 1.0.4 =
* New remote url for custom list submissions.  As we grow, we need to improve our spammer identification and list update tools.

= 1.0.6 =
* Added features to support plugin translation under wordpress standards.

= 1.0.7 =
* Blocklist update.

= 1.0.8 =
* Bugfix to ensure lowercase matches for blocks.

= 1.0.9 =
* Blocklist update.

= 1.0.91 =
* Bugfix for utf-8 character comparison.

= 1.1 =
* New support to help block domains from non-ANSI character sets such as cyrillic or chinese. 
* List update.

= 1.1.1 =
* 2000 download edition!
* List update

= 1.1.2 =
* Security fix for Multisite.
* Enables settings for Network Admins only.

= 1.1.3 =
* Improved multisite support.

= 1.1.35 =
* List update
* Added preliminary whitelist for t.co

= 1.1.36 =
* List update

= 1.1.37 =
* List update

= 1.1.4 =
* List update
* Bug fix for data sharing issue 

= 1.2 =
* List update
* Bug fix for data sharing issue

= 1.2.1 =
* List update

= 1.2.3 =
* Fixes errors saving settings changes in WPMU
* Unified site_options list for WPMU 

= 1.2.4 =
* List Update

= 1.2.5 =
* List Update

= 1.2.6 =
* List Update
* 4.5.1 Compatible

= 1.2.7 =
* List Update
* 4.5.2 Compatible

= 1.2.9 =
* List Update

= 1.3.0 =
* List Update
* 4.5.3 Support

= 1.4.0 =
* List Update
* Fixes Multisite Support

= 1.4.1 =
* List Update
* 4.6 Support

= 1.4.2 =
* List Update

= 1.4.3 =
* List Update
* cURL timeout fix

= 1.4.4 =
* List Update

= 1.4.5 =
* List Update

== Upgrade Notice ==

= 0.1 =
* This is the big bang, make it happen cap'n!

= 0.2 =
* Fixes setting screen 

= 0.3 =
* Added opt-in data sharing tool.  Helps us help you!

= 0.4 =
* Updated block list

= 0.5 =
* Updated block list

= 0.6 =
* Updated block list

= 0.7 =
* Updated block list

= 0.7.1 =
* Updated block list

= 1.0 =
* Non-destructive referer list upgrade.  Adds a more seamless upgrade path when database releases are available.  Safely merges custom user-defined spammers with those in the maintained list.

= 1.0.3 =
* Bug fixes for infinite redirects related to short or blank domains appearing in custom block lists.  As a result, only exact matches will be blocked and variants will need to be added to lists manually.

= 1.0.4 =
* New remote url for custom list submissions.  As we grow, we need to improve our spammer identification and list update tools.

= 1.0.6 =
* Added features to support plugin translation under wordpress standards.

= 1.0.7 =
* Blocklist update.

= 1.0.8 =
* Bugfix to ensure lowercase matches for blocks.

= 1.0.9 =
* Blocklist update.

= 1.0.91 =
* Bugfix for utf-8 character comparison.

= 1.1 =
* New support to help block domains from non-ANSI character sets such as cyrillic or chinese. 
* List update.

= 1.1.1 =
* 2000 download edition!
* List update

= 1.1.2 =
* Security fix for Multisite.
* Enables settings for Network Admins only.

= 1.1.3 =
* Improved multisite support.

= 1.1.35 =
* List update
* Added preliminary whitelist for t.co

= 1.1.36 =
* List update

= 1.1.37 =
* List update

= 1.1.4 =
* List update
* Bug fix for data sharing issue

= 1.2 =
* List update
* Bug fix for data sharing issue

= 1.2.1 =
* List update

= 1.2.3 =
* Fixes errors saving settings changes in WPMU
* Unified site_options list for WPMU 

= 1.2.4 =
* List Update

= 1.2.5 =
* List Update

= 1.2.6 =
* List Update
* 4.5.1 Compatible

= 1.2.7 =
* List Update
* 4.5.2 Compatible

= 1.2.9 =
* List Update

= 1.3.0 =
* List Update
* 4.5.3 Support

= 1.4.0 =
* List Update
* Fixes Multisite Support

= 1.4.1 =
* List Update
* 4.6 Support

= 1.4.2 =
* List Update

= 1.4.3 =
* List Update
* cURL timeout fix

= 1.4.4 =
* List Update

= 1.4.5 =
* List Update
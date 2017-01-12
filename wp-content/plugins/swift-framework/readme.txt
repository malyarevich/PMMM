=== Swift Framework ===
Contributors: SwiftIdeas
Tags: swift framework
Requires at least: 3.6
Tested up to: 4.7
Stable tag: 4.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Swift Framework plugin, for Swift Ideas themes.

== Description ==

The Swift Framework plugin provides the Custom Post Types, Swift Slider, and Swift Page Builder. Custom Post Types are available for any theme, but Swift Slider & Swift Page Builder require a Swift Ideas Theme (running the latest framework) to function correctly.

Supported Themes: Uplift, Atelier, Cardinal, JOYN

== Changelog ==

= 2.5.23 =
* FRONTEND: Icon Box Character mode now supports 2 characters
* FRONTEND: Minor fixes

= 2.5.21 =
* FRONTEND: Added testimonial image size filter
* FRONTEND: Fixed row styling issues
* FRONTEND: Fixed image caption output
* FRONTEND: Fixed image banner output
* BACKEND: Added author support for Portfolio
* BACKEND: Fixed minor responsive issues
* BACKEND: Fixed multi-select fields defaulting to first item

= 2.5.20 =
* FRONTEND: Set table shortcode to run on sf_table namespace
* FRONTEND: Fixed column parallax output
* FRONTEND: Added option of 3 or 5 column output to social grid when not using full width mode
* FRONTEND: Fixed window height parallax display issue
* FRONTEND: Fixed minor styling issues
* BACKEND: Fixed icon shortcode output

= 2.5.19 =
* FRONTEND: Fixed issue with parallax background positioning
* FRONTEND: Fixed issue with background size on columns
* FRONTEND: Fixed charts animation issue
* FRONTEND: Fixed issue with spacing on mobiles
* FRONTEND: Fixed icon box icons colour issue

= 2.5.18 =
* FRONTEND: Full width elements not in rows now render full width on the frontend
* FRONTEND: Fixed issue with client images being cropped
* FRONTEND: Fixed issue with recent posts view all link
* FRONTEND: Fixed issue with icon box icons

= 2.5.17 =
* FRONTEND: Fixed vertical column position output issue
* FRONTEND: Fixed column background issue
* FRONTEND: Fixed issue with "fade" on Swift Slider
* FRONTEND: Fixed issue with images showing 1/1 instead of their set width
* FRONTEND: Fixed issue with multi-layer parallax
* FRONTEND: Fixed row overflow issue
* BACKEND: Fixed issue with SVG icon picker in Uplift
* BACKEND: Default image size is now "Full"

= 2.5.16 =
* FRONTEND: Fixed issue with window height row option
* FRONTEND: Fixed mobile spacing with icon boxes
* FRONTEND: Fixed icon box grid display issue
* FRONTEND: Fixed vertical center for legacy display

= 2.5.15 =
* FRONTEND: Fixed row slants output
* FRONTEND: Fixed issue with full width elements being used within tab + tour elements
* BACKEND: Fixed issue with svg icon grid in Uplift icon box elements

= 2.5.13 =
* FRONTEND: Fixed full width row width on boxed layouts & vertical header setups
* BACKEND: Fixed issues with previous updates

= 2.5.1 =
* BACKEND: Re-added redux framework for themes that don't include it

= 2.5.0 =
* FRONTEND: Improved row & full width row performance
* FRONTEND: Row now has standard width, full width contained, and full width stretch modes
* FRONTEND: Added row column position option, allowing you to set the position of columns within the row
* FRONTEND: Added row column spacing option, allowing you to set the spacing between columns within the row
* FRONTEND: Added row column content position, allowing you to set the position of content within columns of a row
* FRONTEND: Added row column equal heights option, allowing you to set the columns within a row to be equal height
* FRONTEND: Improved overally frontend performance with conditional loading where possible
* FRONTEND: Various fixes
NOTE: This version requires the following theme versions: Atelier v2.4.0, Cardinal v2.5.6, Joyn v2.5.6, Uplift v1.3.0

= 2.4.15 =
* FRONTEND: Fixed offset class addition on column offset
* BACKEND: Performance improvements with builder history functionality

= 2.4.12 =
* FRONTEND: Fixed off-grid columns being hidden outside of row boundaries
* FRONTEND: Fixed view all button link when multiple categories selected in an element
* FRONTNED: Added customer supplied Russian translation
* BACKEND: Minor styling fixes

= 2.4.11 =
* FRONTEND: Testimonial excerpt now respects formatting
* FRONTEND: Fixed alignment issue with Swift Slider slides
* FRONTEND: Fixed issue with testimonial slider HTML output
* BACKEND: Fixed text input alignment issue
* BACKEND: Fixed column edit modal display - design tab was dropping down to next row due to width

= 2.4.1 =
* FRONTEND: Added option of custom X/Y column offset, for advanced layouts (inc custom z-index value)
* FRONTEND: Added link target option to client element
* FRONTEND: Fixed Swift Slider issue with trackpad scrolling in curtain mode
* FRONTEND: Fixed mobile display issue
* BACKEND: Set google fonts to weekly update
* BACKEND: Fixed tabs helper display issue
* BACKEND: Set vertical center defaulted to off in row edit options
* BACKEND: Fixed issue with GoPricing table select
* BACKEND: Fixed issue with multilayer parallax text
* BACKEND: Fixed slider input field width, added a bit more space

= 2.4.0 =
* FRONTEND: Added User Directory Listings page builder element
* FRONTEND: Fixed issue with rows introduced in v2.3.13
* FRONTEND: Fixed issue with curtain slider glitching with trackpad in chrome
* BACKEND: Added authentication option panel for Google Maps. Please go to Swift Framework > Google Maps Auth.
* BACKEND: Fixed issue with address field label not showing on map element edit modal

= 2.3.14 = 
* FRONTEND: Fixed issue with parallax areas on mobile

= 2.3.13 =
* FRONTEND: Fixed issue with gallery slider thumbs in Uplift
* FRONTEND: Fixed issue with remove element spacing option
* FRONTEND: Fixed issue with vertical element spacing not working
* FRONTEND: Fixed issue with team member facebook URL
* BACKEND: Fixed issue with gallery element height

= 2.3.12 =
* FRONTEND: Added tag to remove swift slider script from cloudflare minification
* FRONTEND: Fixed issue with column background/padding
* FRONTEND: Fixed issue with client carousel autoplaying without it being enabled
* BACKEND: Fixed issue with blog pagination not being selectable
* BACKEND: Removed "Grid" Layout as an option for products, as didn't make sense with the column options

= 2.3.11 =
* BACKEND: Fixed minified files

= 2.3.1 =
* BACKEND: Fixed issue with add images button in v2.3.0

= 2.3.0 =
- ADDED: Compatibility for the latest Instagram API. Please go to Swift Framework > Instagram Auth. to authorize your account to show your photos
- ADDED: Option of setting custom md / lg / xs column sizes for columns, for advanced column size control
- ADDED: Product Reviews asset to the Page Builder
- ADDED: Fixed image height option to image banner page builder element, ideal for grid layouts.
* FRONTEND: Added option to Swift Slider slides to set alternate background alignment on mobile devices
* FRONTEND: Fixed issue with curtain slider on mobile
* FRONTEND: Fixed issue with swift slider autoloading video before the slider loads
* FRONTEND: Fixed issue with swift slider parallax on vertial header setup
* FRONTEND: Added option for basic blog filter option within the Blog asset
* FRONTEND: Fixed issue with animated headline not wrapping on mobile
* FRONTEND: Fixed column design options to be applied to the correct element
* BACKEND: Fixed default column padding to 15px
* BACKEND: Added migration for old values of column padding when editing a column
* BACKEND: Fixed Tabs elements losing the settings after dragging
* BACKEND: Fixed issue with column height not expanding when adding new elements inside

= 2.2.61 =
* FRONTEND: Fix for fade transition stalling progress on Swift Slider
* BACKEND: Re-added missing Campaign element
* BACKEND: Custom post types now all support revisions

= 2.2.52 =
* FRONTEND: Fix for nucleo icon output

= 2.2.51 =
* FRONTEND: Fix for row background issues

= 2.2.5 =
* FRONTEND: Added WhatsApp & Snapchat social icon output
* BACKEND: Fixed issue where 30px vertical padding was reseted when editing the row
* BACKEND: Fixed issue where percentage button wasn't saving the option
* BACKEND: Fixed issue where Google Map was appearing blank by default

= 2.2.2 =
* FRONTEND: Inital improvements to FAQ styling & functionality
* FRONTEND: Fixed issue with Swift Slider fade on Firefox
* FRONTEND: Excluded Swift Slider slides from WordPress search results
* FRONTEND: Fixed social share output

= 2.2.1 =
* BACKEND: Products - fixed issue with certain fields showing when not needed
* BACKEND: Fixed edit modal layout issues

= 2.2.0 =
* FRONTEND: Improved setup of row slants
* BACKEND: Added repsonsive visibility indictator to elements, with tooltips
* BACKEND: Fixed issue with design options not saving correctly
* BACKEND: Other minor fixes

= 2.1.7 =
* FRONTEND: Fixed FAQs category issue.
* BACKEND: Fixed issue with return key not working in text view.

= 2.1.6 =
* BACKEND: Fixed unexpected error with v2.1.5

= 2.1.5 =
* FRONTEND: Improved image loading by adding srcset functionality in various places such as image element, image banner element, and gallery sliders
* FRONTEND: Fixed issue with tour display
* FRONTEND: Fixed ajax add to cart issue with add to cart button shortcode
* BACKEND: Fixed issue with text block having extra margin by default if override margin has been set
* BACKEND: Fixed legacy design fields not showing as inactive
* BACKEND: Fixed Google maps buttons fields
* BACKEND: Fixed extra class field being overridden in child elements by the row
* BACKEND: Removed duplicate extra class field on row
* BACKEND: Fixed asset name enter keypress event
* BACKEND: Text in text blocks is now forced dark colour, to avoid being unable to see the text when being set to a light colour
* BACKEND: Multi-layer Parallax display fix
* BACKEND: Various design option fixes

= 2.1.1 =
- FRONT-END: Fixed issue with Portfolio Showcase error.
* BACKEND: Re-added Redux Framework to the plugin.

= 2.1.0 =
- FRONT-END: Added overflow left/right option for image asset for use in full-width rows
- FRONT-END: You can now select a display layout in the Products page builder asset
* BACKEND: Added design tab for margin/padding/border controls on columns, rows, and text blocks
* BACKEND: Improved responsive + touch capabilities for editing pages on touch/mobile devices.
* BACKEND: Added option to set the default width of the element edit modal
* BACKEND: Performance improvements
* BACKEND: Fixed issue with edit modal header disappearing.
* BACKEND: Fixed issue with controls on 1/6 elements + columns
* BACKEND: Edit modal header is now stuck to the top of the edit modal, allowing for save/cancel without scrolling
* BACKEND: Improved element searching, showing no results if none are found rather than all.
* BACKEND: Fixed compatibility issue with NinjaForms
* BACKEND: Removed redux framework as provided with the theme or plugin instead.
* BACKEND: Boxed Content now shows inner content if the Text Block show content option is enabled in the page builder options

= 2.0.3 =
- FRONT-END: reverted button changes to use previous parameter names.
* BACKEND: Removed empty page builder animation
* BACKEND: Added most used functionality, allowing you to quickly access your most used elements
* BACKEND: Alert Asset fixes
* BACKEND: Fixed Scroll bar in the Edit modal that couldn’t be selected
* BACKEND: The sizes (height) of the resize handle was changed to avoid resize conflicts in elements inside each others (like in tabs, accordions, tours, Row).
* BACKEND: Fixed issues with dragging elements to the bottom of the page builder area. Was fixed in Row, Tour, Tabs, Accordions
* BACKEND: Fixed issues with elements added to the tabs, Accordions, tours

= 2.0.2 =
* FRONTEND: further icon box fixes
* FRONTEND: Added compatibility for Portfolio WPML duplication
* BACKEND: Added option to show text block text within the element preview, like in older versions
* BACKEND: Fixed issue with Toggle display
* BACKEND: Fixed issue with responsive visibility dropdown being cut off
* BACKEND: Fixed issue with Widget Area element
* BACKEND: Fixed issue with products element

= 2.0.1 =
* FRONTEND: Fixed issue with button output
* FRONTEND: Fixed issue with icon box image display
* FRONTEND: Fixed issue with icon box width
* BACKEND: Fixed issue with product category select
* BACKEND: Fixed PHP7 issue which prevented edit fields from loading data
* BACKEND: Fixed resize issue
* BACKEND: Fixed image banner element styling to match image element
* BACKEND: Fixed issue with dragging and dropping resized elements

= 2.0.0 =
* Completely overhauled the Swift Page Builder, now much slicker, quicker and easier to use!
* If you have any issues with the new builder, please log a support topic related to your specific theme at http://swiftideas.com/support

= 1.7.7 =
* SPB Search now can set post type to Any or Products
* Minor fixes

= 1.7.6 =
* Fixed missing content overview on page builder assets

= 1.7.5 =
* Added support for PHP7
* Added Animated Headline page builder asset
* Added ordering options for Directory page builder asset
* Updated Redux framework
* Fixed compatibility issue with Visual Composer
* Fixed WPML duplication issue with Swift Slider

= 1.7.0 =
* Minor fixes

= 1.66 =
* Migrated to new plugin update hosting.
* Added post type override option to recent posts page builder asset.
* Fixed issue with product category setting in products page builder asset.
* Fixed issue where lightbox icon would show on galleries where no lightbox image was present.

= 1.65 =
* Temporary fix for image caption, reverted to a textfield input until html input is resolved 

= 1.64 =
* Fixed progress bar styling
* Fixed stroke-to-fill button styling
* Fixed issue with page builder options not showing when certain requirements were met
* Fixed bootstrap conflict
* Fixed issue with image caption formatting
* Removed unecessary admin script from loading

= 1.63 =
* Updated Redux framework

= 1.62 =
* Team gallery display images are now clickable on mobile
* Updated minified files after previous update
* Updated Redux framework

= 1.61 =
* Slide video now only starts playing when the slider is in the viewport
* Image caption field now supports HTML (line breaks etc)
* Updated Redux framework

= 1.60 =
* Added loop on/off option to Row background video
* Videos set not to loop now won't restart when scrolled into view
* Fixed row/column layout issues
* Fixed issue with social output
* Fixed issue with page builder not loading when on the s"Text" content tab
* Fixed background position align of Swift Slider

= 1.51 =
* Disabled options developer mode
* Multi-layer parallax display fixes
* Updated language files


= 1.50 =
* Added options panel for all framework related options
* Added background image horizontal alignment and size options to Swift Slider slides
* Fixed further issues with intro animations
* Fixed issue with campaign category display

= 1.43 =
* Fixed issues with certain animation issues

= 1.42 =
* Properly fixed missing css images

= 1.41 =
* Swift Slider videos now don't reset when scrolled off the page
* Fixed missing css images

= 1.40 =
* Consolidated Page Builder asset css
* Added option to add full slide link to Swift Slider slides
* Fixed issue with translation files being named incorrectly
* Fixed page builder controls not being accessible on touch devices
* Fixed issue with map asset display type select
* Minor improvements to SPB drag & drop

= 1.32 =
* Removed selected product select options to greatly increase performance, now comma delimited text input
* Fixed faqs content output

= 1.31 =
* Refactored selected products query to massively improve performance
* Adjusted page builder asset loading script to ignore non-php files
* Adjusted swift slider caption size on mobile

= 1.30 =
* Added "Title" field to Go Pricing asset
* Added Carousel & Column Count options to "Standard Row" Recent Posts display type
* Added 1 column option to Products asset
* Updated noUISlider plugin script
* Fixed issue with column padding
* Fixed issue with swift slider parallax causing items to not be clickable in the main content

= 1.21 =
* Minor bug fixes

= 1.20 =
* Swift Slider pagination now shows permanently on mobile
* Improved controls display on 1/6 column elements
* Fixed issue where captions could not be disabled on gallery asset masonry display
* Fixed issue with Swift Slider on boxed layout modes
* Fixed issue with map controls not disabling/enabling
* Fixed issue with Swift Slider parallax on mobile

= 1.10 =
* Greatly improved Page Builder save page speed
* Improved theme compatibility 
* Fixed issue with Swift Slider background vertical alignment

= 1.03 =
* Fixed comaptibility issues with Visual Composer
* Fixed issue with Swift Slider curtain not allowing scroll down

= 1.02 =
* Added fixes for potential missing function errors
* Portfolio post type now supports revisions

= 1.01 =
* Added language files
* Fixed issue where image option wouldn’t show when using Video for a Row
* Added option to set a custom image height in the Team asset

= 1.0 =
* Inital version for release with Atelier
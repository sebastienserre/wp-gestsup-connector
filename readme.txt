=== WP GestSup Connector ===
Contributors: audi63
Tags: gestsup, connector, helpdesk, ticket, support
Requires at least: 4.6
Tested up to: 6.3.1
Stable tag: 1.5.5

WP GestSup Connector allow you to connect your WordPress Website to the helpdesk GestSup

== Description ==
WP GestSup Connector allow you to connect your WordPress Website to the helpdesk [GestSup](http://gestsup.fr)

Add a form to your website allowing your customer to open ticket directly in your GestSup Helpdesk

/!\ New in WP Gestsup Connector 1.5.0 => Add the form with a new editor ( Gutenberg ) Block

== Installation ==
1. unzip
2. upload to wp-content/plugin
3. Go to your dashboard to activate it
4. have fun!

== Frequently Asked Questions ==
= How to add an \"add ticket form\" to my website? =

Use the Gutenberg block

Or use the old Shortcode:

1. Create a page with the name you want.
2. Add the Shortcode [gestsup_add_ticket]
3. That\'s all!

= May I allow not registered visitor to contact us with the same form? =
yes! Just allow this by checking the box and add a mail address to reach the support team.
If a registred customer is requesting support, a ticket is directly created in GestSup. if not registred, support team will receive a mail with the details.

= May I use a Recaptcha? =
Yes! the Google Recaptcha V2 ( invisible and Checkbox ) are allowed.

== Screenshots ==
1. GestSup Options
2. GestSup Shortcode
3. GestSup Databases options
4. Gutenberg Block settings
5. Gutenberg Block preview
6. Dashboard Widget


= 1.5.5 = 05 oct 2023
* Bugfix
* Code reorganisation : Correction de l'erreur de namespace
* Tested on WP 6.3.1

Source Contributors: sebastienserre
== Changelog ==
= 1.5.4 = 14 feb 2019
* happy Valentine's Day
* Add link to reach the GestSup directly from the Admin Widget
* Code reorganisation

= 1.5.3 = 12 feb 2019
* add settings to admin widget

= 1.5.2 = 27 jan 2019
* add Admin Widget

= 1.5.1 = 18 jan 2019
* Happy New Year Everybody!
* Add Category selection on Gutenberg Block

= 1.5.0 = 20 dec 2018
* Improve Options Page
* Add a new editor (Gutenberg) Block to add a form.

= 1.4.2 = 12 déc 2018
* Don't forget the Class!

= 1.4.1 = 11 dec 2018
* add language selection on account creation

= 1.4.0 = 29 Nov 2018
* Release Pro features to Free one
* Tested on WP 5.0

= 1.1.0 =
* Add Google Recaptcha (optional)

= 1.0.1 =
* Bugfix
* add l10n functions
* add .pot
* add French Translation (fr_FR)

= 1.0.0 =
* initial version

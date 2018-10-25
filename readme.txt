=== Elementor Login Protected Sections ===
Contributors: teolaz
Tags: elementor, login, section
Requires at least: 4.6
Tested up to: 4.9.8
Stable tag: 4.3
Requires PHP: 7.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

This plugin allows to have a protection layer in Elementor content and show it only if a visitor is logged in or logged out from your WP site.

== Description ==

Something which really miss in Elementor and Elementor Pro is the possibility to hide or show blocks of content (sections) for logged in and non logged in users.
This plugin aims to reach that exact goal, allowing to selected if a section:

* should be visible to everyone
* should be visible to only logged in users
* should be visible to only logged out users

So, in a typical situation, you could decide to protect all sections of a page and show a login widget (already available in Elementor PRO) and a registration widget.


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/elementor-plugin-protected-sections` directory, OR install the plugin using composer and require the autoload.php file inside vendor dir on your functions.php file.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Remember the plugin works (naturally) only in presence of Elementor plugin

== Frequently Asked Questions ==

= Why this plugin isn't in the wordpress repo? =

I have not enough time to handle both github and wp trac, sorry.

= So what about updates? =

I'm using the amazing YahnisElsts/plugin-update-checker package to check updates directly on Github.

= Could i request more functionalities? =

Of course you can! Please be aware I have a fulltime job and cannot handle all requests.
PRs on Github are welcome!

= I found a bug, now what? =

You can open an issue on Github plugin's page here:

= Why are you using a buffer to erase shown content? Code isn't optimized written in this way! =

Absolutely, you're right, but this is the only way i found to not have a single line of HTML code printed in frontend.
Infact, Elementor doesn't offer an actual filer to remove a section, it only offers an action before and after the section is printed.
So, the only hook I had in mind was to incercept a section before printing it and insert the whole HTML, CSS and JS printed code inside a buffer which will be cleaned up anytime I don't want to show a section.

== Screenshots ==

1. This is where you can select which user should see this section

== Changelog ==

= 1.0.0 =
* First fresh released version

= 1.0.1 =
* Fix on activation hooks, need to check if Elementor is present and working
===Easy Sign Up===
Contributors: Greenweb
Donate link: http://www.beforesite.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: auto responder, auto-responder, autoresponder, html autoresponder, jump page, squeeze page, squeeze form, form, emailer, email, redirection, leads, mailing list, newsletter, newsletter signup, sign up, beforesite
Requires at least: 3.5
Tested up to: 3.5.9
Stable tag: 3.1.3

This Plugin creates a form to collect the name and email from your visitors, who are then redirected to the web address of your choice. 

== Description ==
This plugin generates a customizable HTML thank you email that is sent to the visitor, the visitor's email and name are sent to an email address of your choosing.
Possible use is collecting email address for a newsletter, or leads for your sales force before redirecting to a brochure.

= Main Functions =
 * Email address collection
 * User redirection
 * Auto-responder
 * Lead collection
 * Squeeze Page

= Extending Easy Sign Up =
Plugins that integrate, Database, Customizable layouts, and easy translation can be found at [beforesite.com](http://www.beforesite.com/)

Additional form fields, check boxes, text boxes etc can be added with the integration of [WordPress Hooks, Actions and Filters](http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters)

== Installation == 
 * Upload the easy_sign_up folder to the /wp-content/plugins/ directory
 * Activate the plugin through the 'Plugins' menu in WordPress
 * Change the options under the Easy Sign Up Menu

== Use ==
 * Add the form to your site as a widget.

= OR =

 * Use the following short code in your pages and posts:
 * `[easy_sign_up]`
 * Optional short tag values are:
  * **title** allows you to customize the title. The short code `[easy_sign_up]` default title is "Easy Sign Up"
  * **from_id** (removed) allowed you add a custom id to the form - if you need this for CSS layout please use the `esu_class` attribute instead as this will be available in the future.
  * **esu_class** allows you add a custom class.
  * **fnln** splits the Name field into first and Last name fields.
  * **phone** adds a phone field to the form.
  * **esu_label** an unique identifier for your form, useful if you have forms on multiple pages.
  * i.e. `[easy_sign_up title="Your Title Here" fnln="1" phone="1" esu_label="A unique identifier for your form" esu_class="your-class-here"]`.

== Frequently Asked Questions ==
[FAQ](http://www.beforesite.com/easy-sign-up-faq)
== Upgrade Notice ==
See Installation
== Screenshots ==
1. Easy Sign Up Form
2. Easy Sign Up Options Page
3. Shortcode button
[More info here](http://www.beforesite.com/plugins/whats-new-in-easy-sign-up-plugin-version-3/)
== Changelog ==
= 3.1.3 =
  Disabled transient
= 3.1.2 =
  * Added Dutch language support thanks to __Luc van Geenen__ @ [decoda](http://www.decoda.nl)
= 3.1.1 =
 * Corrected Language domain
= 3.1 =
 * Removed support for depreciated shortcode attribute
 * Added a forward slash (/) on the end of the website's URL as this was in a few rare cases tripping servers and preventing them from posting the from data
= 3.0 =
 * 3.0 is coded to take full advantage of WordPress's API - keeping the plugin fast and lightweight and eliminating redundant code. 
 * New validation JavaScript code
 * New default layout
 * Screen reader support
 * Language support - feel free to translate 
 * New shortcode tag attributes 
 * New add Easy Sign Up shortcode via the page/post editor 
 * New form fields, *phone, first* and *last name*
 * HTML auto responder emails 
 * WYSWYG editor for auto responder email 
 * Change the way extra's are installed, these are now stand alone plugins and are installed like any other WordPress plugin 
 * Added Spam protection via the **Akismet** API. *This requires that Akismet is installed and activated.*
 * Added Hooks and Filters to allow other Plugin and Theme Developers to interact with the plugin, adding extra fields and capturing form data
  * Go to [www.beforesite.com](http://www.beforesite.com/) for more info
= 2.1.1 =
  * Added [stripslashes_deep()](http://codex.wordpress.org/Function_Reference/stripslashes_deep) function to remove the the backslash character used to escape quotes in auto-responder email - March 14, 2012
= 2.1 =
  * Fixed a IIS bug with the way the unzip class was handling the windows folder system
  * Getting the plugin ready to work in 3.3
   * Ensuring that the code contains no deprecated functions or hooks
= 2 =
 * Replaced jQuery validation with standard javascript. Saving 35 kb and minimizing clashes with other javascript libraries used by themes and plugins
 * Replaced mail() with wp_mail()
 * Added support for Easy Extras
  * [More info here](http://www.beforesite.com) 
 * Added support for localization.
  * To translate into your language use the easy-sign-up.pot file in /languages folder.
  * Poedit is a good tool to for translating. @link http://poedit.net
  * Please contact me at [www.beforesite.com/support](www.beforesite.com/support) with any translations so I can make them available to others.

= 1.2 =
 * Bug fix: Fixed a problem with a clash between the Easy Sign Up plugin and the Atahualpa Theme. 

 * Added the form as a widget.

 * Changed the validation script to jQuery validation plugin 1.7
 
 * Added form_id as an optional attribute to the short code, this will allow the form to be in more then one area of your page without confusing the form validation script.
  * Note that you need to make your id one word i.e. form_id="my_id" OR form_id="myID" is correct, But form_id="My ID" is wrong.

= 1.1 =
Removed direct access to the wp-config.php file.
Added custom tag to the auto reply email. If you would like to include the name person who signed up in the *Thank You Email* just paste #fullname# into the Thank You Email text field where you'd like to see it.

= 1 =
First Version
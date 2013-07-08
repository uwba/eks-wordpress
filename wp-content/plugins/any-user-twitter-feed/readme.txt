=== Any User Twitter Feed ===
Donate link: http://www.webdesignservices.net/
Tags: twitter, twitter sidebar, twitter timeline, search, twitter search, sidebar, social sidebar, widget, plugin, post, posts, links, twitter widget, twitter feed, simple twitter, twitter api 1.1, api 1.1, oauth, twitter oauth
Requires at least: 3.5+
Tested up to: 3.5.1
Stable tag: 1.1
License: GPLv2 or later

Embed Twitter Timelines or display tweets based on a keyword. Fully compatible with Twitter API 1.1, with many options for customization!

== Description ==

<a href="http://www.webdesignservices.net/free-wordpress-twitter-plugin.html" target="_blank" title="Free Wordpress Twitter Widget">Free Wordpress Twitter Widget</a>. Embed anyone's Twitter Timeline using only their username, or display tweets based on a keyword. Fully compatible with the latest Twitter API and guaranteed to work even with the forthcoming Twitter changes!

 Features:

 1. Embed timelines using only username
 2. Show tweets which contain a keyword
 3. Highly configurable, many visual options
 4. Using Twitter 1.1 API with authentication
 5. No JavaScript

== Installation ==

Important

To use this widget, please follow the steps bellow:

1) Register at https://dev.twitter.com/apps/new and create a new app.

2) After registering, fill in App name, e.g. "_domain name_ App", description, e.g "My Twitter App", and write the address of your website. Check "I agree" next to their terms of service and click "create your Twitter application"

3) After this you app will be created. Click "Create my access token" and you should see at the bottom "access token" and "access token secret". Refresh the page if you don't see them.

4) Copy to widget settings "Consumer key", "Consumer secret", "Access token" and "Access secret"

== Frequently Asked Questions ==

Q: Why do I have to trouble with all of this?
A: Twitter is removing access for all unauthorized requests, so every extension which wants to connect to Twitter must use authentication, otherwise it will stop working (many already have).

Q: My widget doesn't work!
A: Make sure that you have copied the correct keys. If widget type is set to timeline, make sure you chose a valid Twitter username. If widget type is set to search, Twitter may return error if search query is extremely complex.

Q: Do you cache results?
A: Yes, but you should almost always see the latest tweets. Widget will always try to get the latest tweets and save them to a cache. In case of a problem, tweets will be retrived from the cache. For high traffic sites (more than 10.000 visits per day), you may occasionally get tweets from the cache, as Twitter doesn't allow more than 180 requests per 15 minutes. If you use more requests than allowed, widget will display latest saved tweets from the cache, until new 15 minute window opens.

== Changelog ==

= 1.0 =
* Initial version
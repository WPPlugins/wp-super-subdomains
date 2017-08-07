=== WP Super Subdomains ===
Contributors: mcjambi
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: http://www.jamviet.com
Tags: subdomain, subdomains, categories, post, posts, page, pages, author page, category, Viet Nam, login page
Requires at least: 3.0
Tested up to: 4.7
Stable tag: 1.1

This plugin allow you create subdomain without using Wordpress Multisite ! Setup your main categories, tag, pages, and authors as subdomains !

== Description ==

If you do not like Wordpress Multisite and do not want to separate Your database, WP Super Subdomains will help you, this plugin work like charm without complicate setting, just active it and enjoy !

All your tag, Category, page or author will turn to subdomains and it depend on your setting !

Works perfectly with cache plugin like W3C Total Cache or WP Super Cache ! And thanks to Erick Tampubolon ( http://www.lontongcorp.com ) for create `WP subdomains revisited` ! i have some idea from that plugin but my plugin run faster and better than his one !

= Features =
* Setup main categories as subdomains
* Setup tag as subdomains
* Setup main pages as subdomains
* Setup author archive as subdomains
* Auto redirect to new links using 301 redirect ! Do not harm your Backlink or Visitor !

If you want to create login page like this: `login.domain.com` please do something in wp-config.php file !

Please go to [My English Post](http://www.jamviet.com/2016/03/plugin-wp-super-subdomains-create-subdomains-second.html) to read more, or if you have a new idea please tell me there !

== Installation ==

* Download `wp-super-subdomains.zip` manually or automatically `Plugins/Add New`
* Unzip
* Upload `wp-super-subdomains` directory to your `/wp-content/plugins` directory
* Activate the plugin
* Configure the options from the Subdomain in Setting page


== Frequently Asked Questions ==


= How to add subdomains? =

Please go to your domain manager or contact your provider

= Do I have to add each subdomain manually? =

You can add wildcard (A or CNAME) *.domain.com to the same installations path 

= Is this plugins works with cache? =

Sure things. I recommend it using W3 Total Cache by setting one page as "cdn" or anypage.domain.com, and make as Generic Mirror CDN and add as cdn for image, css or javascript without any technical difficulties anymore.


== Screenshots ==

1. Setting Page !


== Changelog ==

= 1.1 =

* Fixed error 404 in Author page, something change since WordPress 4.6
* Fixed title missing in subdomain
* Improve speed in subdomain
* Fixed some other bug, thanks for all !

= 1.0 =

Start the first version


== Instructions ==

Remember to read instruction in Setting Page
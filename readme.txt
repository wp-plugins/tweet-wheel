=== Plugin Name ===
Contributors: NerdCow
Tags: auto tweeting, auto tweet, automated tweeting, blog, blogging, cron, feed, social, timeline, twitter, tweet, publish, free, google, manage, post, posts, pages, plugin, seo, profile, sharing, social, social follow, social following, social share, social media, community, wp cron, traffic, optimization, conversion, drive traffic, schedule, scheduling, timing, loop, custom post type, woocommerce, shop, products, easy digital downloads, portfolio, tweet content, pages, page, e-commerce
Requires at least: 3.8
Tested up to: 4.2
Stable tag: 0.5.1
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SCXXGUX47LL4E
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automated and redefined post tweeting for every Wordpress website. Precisely schedule your posts for tweeting using various and engaging templates.

== Description ==

**Since version 0.5 Tweet Wheel handles custom post types meaning you can tweet about whatever type of content you like: blog entries, shop products, portfolio items and whatever you wish!**

Tweet Wheel is a simple and yet powerful tool that every website owner will fall in love with. The idea behind Tweet Wheel is to automatically tweet posts from users' website and take the burden off their shoulders and let them focus on the thing they are best at. Turn your website into a traffic-and-business-driving tool in no time!

First, install and activate the plugin. You may notice not many options visible at start, but it's only until you authorise our Twitter app to access your Twitter account. Once authorised, you can enjoy your website gaining on social media attention even when you are not looking.

Unlike other Twitter plugins, this one works automatically and does not require your constant care. You can get up and running in a few clicks, but if you want to make more out of our solution, you can add multiple, interesting templates for each post. This will reduce your chance of sounding robotic and will let you test headings to see which one comes the most engaging.

**Current features**

* Automated queueing system, which is the core of the plugin. It handles all the automation.
* Multi-templating for posts helps you to specify limitless amount of tweet variations for each post.
* Advanced scheduling gives you more control over time of tweetings. Specify days and times at which you want your post published.
* Handling of custom post types - fully compatible with woocommerceshop products!
* Customising the queue let's you to supervise the order in which posts are tweeted.
* Looping is optional, but very useful. If on, it will automatially append just tweeted post at the end of queue. Keeps going infinitely this way.
* Queue posts on their publishing. When you create a new post you can ask plugin to automatically queue it for you.
* Pausing and resuming queue comes useful when you need a bit more control. No need to deactivate the plugin to put it on hold.
* Convenient bulk actions - queue, dequeue and exclude multiple posts at once.
* Option to tweet instantly without waiting for post's turn - perfect for hot news!
* Simple view which minifies the queue look so you can fit more items on your screen - helpful for shuffling!
* Health check tab that let's you know if your website is ready for Tweet Wheel and what to fix.


#### Upgrade to PRO
* Attach **featured images** to your tweets with one click.
* Use your favorite domain for **shortening URLs** (by Bit.ly).
* **Track clicks** and tweets history of individual tweeted templates.
* Fill up the queue using **filtering by date range, amount and post type**.
* Plenty minor improvements which overally boost user experience and easy of use.
* **Premium support**

[CLICK HERE TO UPGRADE](http://codecanyon.net/item/tweet-wheel-pro-automated-tweeting-for-wordpress/11802003)

**If upgrading, please uninstall free version first!**

If you have a suggestion for improvement or a new feature, feel free to use [the Support forum](https://wordpress.org/support/plugin/tweet-wheel) or contact us directly via [our website](https://nerdcow.co.uk/contact-us)

Want regular updates? Follow us on [Twitter](https://twitter.com/NerdCowUK) 

== Installation ==

**Minimum requirements**

* WordPress 3.8 or greater
* PHP version 5.2 or greater
* MySQL version 5.0 or greater
* WP Cron enabled in your WordPress installation

There are two ways to install Tweet Wheel

### via WordPress plugin upload - automated
Navigate to Plugins > Upload New Plugin and upload the zip file you have downloaded. Voilla!

### via FTP client - manual
1. Unzip the zip file you have downloaded
1. Upload unzipped folder `tweet-wheel` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Tweet Wheel > Authorize and authorize our plugin to access your Twitter acount

== Frequently Asked Questions ==

#### Can I get banned by Twitter for using Tweet Wheel?
It is as likely as you were tweeting yourself. Tweet Wheel is 100% safe tool to use as long as you do not abuse it's purpose. Whether you tweet yourself or use our plugin to do so, you still need to obey to The Twitter Rules. Read more  [here](https://support.twitter.com/articles/18311-the-twitter-rules).

#### Where can I get support?
If you are having a trouble setting up Tweet Wheel feel free to use [the Support forum](https://wordpress.org/support/plugin/tweet-wheel) or contact us directly via [our website](https://nerdcow.co.uk/contact-us).

#### I have a brilliant idea. Where can I suggest it?
We would love to hear from you! In this case, [drop us a line](https://nerdcow.co.uk/contact-us) with a good description of your request and we will get back to you with a comment. 

#### You are doing absolutely fabulous job. Can I support you?
Your happiness is the best reward for our work! Although, if you feel like contributing, feel free to [buy us a pint of milk](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SCXXGUX47LL4E) (we are nerd cows, you know...).

== Screenshots ==

1. Authorisation screen. Nice and simple. Click the button to start working with the plugin within seconds!
2. General plugin settings. You can easily deauthorize connected account and attach another one.
3. Advanced scheduling allows you to select days and add times at which to tweet.
4. Full-view of the queue.
5. Simplified view of the queue. Helpful for shuffling posts.
6. Health check page to make sure Tweet Wheel has everything it needs to run properly.

== Changelog ==

= 0.5.1 =
* Fixed jQuery conflict with other plugins eg. MailPoet

= 0.5 =
* Added support for custom post types
* Ensured compatibility with latest WordPress to date (4.2)
* Fixed bug which prevented to delete scheduled times

= 0.4 =

* Added multi-templating for posts
* Added precise scheduling of posts
* Added health check page to verify settings of WordPress installation
* Wrapped cron handling into it's own class
* Ditched used metabox framework
* Replaced complex settings framework with Geczy's for simplicity sake
* Fixed bugs around scheduling plugin's cron job
* Plugin on uninstallation will now properly remove cron job and all options
* Reduced cron time interval down to 1 minute for improved precision
* Removed tweet template preview for time being
* Improved text counter to exactly reflect Twitter's one when adding a template
* Added custom validation for tweet templates eg. you have to include {{URL}} tag
* Fixed restoring previous state of the queue if saving new order failed for some reason
* Improved styling, spacing and alignement for better user experience
* Changed hundreds lines of code for better stability and security

= 0.3.2 =

* Fixed a bug preventing Tweet Wheel cron job being added to the scheduler. It fixed your issues with tweets not being send out automatically.

= 0.3.1 =
* Important security fix around AJAX functionality

= 0.3 =
* Fixed the issue with saving the queue after removing posts (they were being readded regardless)
* Improved code quality and removed a few bugs
* Fixed option names inconsistency
* Changed queue's default state to paused (after fresh installation)
* You can now queue and dequeue single post without reloading the post edit list screen
* Introduced bulk actions: queue, dequeue and exclude multiple posts at once
* Added uninstallation side of the plugin - leaves your WordPress website untouched (removes added database table and options)
* Minor fixes and changes

= 0.2 =
* Forced a tweet length to 140 characters and ditched anything after the limit
* Fixed characters counter on the post edit screen - it now properly calculates a link length
* Fixed pausing and resuming the queue
* Prevented a queue item duplication after post's title has changed
* Replaced drag icon with an image to improve it's displaying across all browsers
* Fixed character encoding in the tweet preview on the post edit screen
* Fixed character encoding for sent tweets
* Wording has been amended in a few places
* Decreased WP Cron interval from 1 hour to 15 minutes to improve accuracy
* Added a preview of recently tweeted tweet at the top of the queue
* Done some code cleanings

= 0.1 =
* Initial release
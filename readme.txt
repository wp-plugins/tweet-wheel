=== Plugin Name ===
Contributors: NerdCow
Tags: auto tweeting, auto tweet, automated tweeting, blog, blogging, cron, feed, social, timeline, twitter, tweet, publish, free, google, manage, post, posts, pages, plugin, seo, profile, sharing, social, social follow, social following, social share, social media, community, wp cron, traffic, optimization, conversion, drive traffic
Requires at least: 3.8
Tested up to: 4.1.1
Stable tag: 0.4
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SCXXGUX47LL4E
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automated and redefined post tweeting for every Wordpress blog.

== Description ==

### If there is one desire of every blogger, it is the exposure.

Tweet Wheel is a simple and yet powerful tool that every blogger will fall in love with. The idea behind Tweet Wheel is to automatically tweet posts from user's blog and take the burden off bloggers' shoulders and let them focus on the thing they are best at - writing.

Thanks to a built-in queueing system, Tweet Wheel is as easy to manage as a music playlist!

### Automated queueing system
In the core of the Tweet Wheel lays automated system that tweets your posts when it's time. It is very stable, safe and keeps your Twitter profile active all the time. Anyone could use a hand in driving extra traffic to their blog, right?

### Multi-templating
We introduced multiple tweet templates to avoid sounding like a broken record. Now you can set as many tweet variations for each post as you like!

### Advanced scheduling
With an in-built scheduler, you can tweet regularly on specific days at specific time. Just the way your readers would expect you to.

### Customise the queue
Add, remove, exclude and shuffle the queue the way you please! Tweet Wheel will never tweet without your consent.

### Tweet once or infinitely
Let Tweet Wheel reschedule every tweeted post.. or don't. It's up to you!

### Automatically queue new posts
Add new posts to the queue on publish. Why bother when it can be done automagically!

### Engage with your audience
Comment on your blog, reply to blog-related tweets and more. Leave the rest to Tweet Wheel.

### Other features
1. Added a Health Check to tell you if your website is ready to handle Tweet Wheel
1. Very user-friendly interface that every one can familiarise with in minutes
1. Convenient bulk actions - queue, dequeue and exclude multiple posts at once
1. Easy pausing and resuming of the queue - no need to deactivate the plugin
1. Option to tweet instantly - perfect for hot news!
1. Simple view which minifies the queue look so you can fit more items on your screen - helpful for shuffling!
1. Many other awesome thingies! Just check it out, it's a free plugin of quality of a premium one.

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

== Frequently Asked Questions ==

#### Can I get banned by Twitter for using Tweet Wheel?
It is as likely as you were tweeting yourself. Tweet Wheel is 100% safe tool to use as long as you do not abuse it's purpose. Whether you tweet yourself or use our plugin to do so, you still need to obey to The Twitter Rules. Read more  [here](https://support.twitter.com/articles/18311-the-twitter-rules).

#### Where can I get support?
If you are having a trouble setting up Tweet Wheel feel free to use [the Support forum](https://wordpress.org/support/plugin/tweet-wheel) or contact us directly via [our website](https://nerdcow.co.uk/contact-us).

#### I have a brilliant idea. Where can I suggest it?
We would love to hear from you! In this case, [drop us a line](https://nerdcow.co.uk/contact-us) with a good description of your request and we will get back to you with a comment. 

#### You are doing absolutely fabulous job. Can I support you?
Your happiness is the best reward for our work! Although, if you feel like contributing, feel free to [buy us a pint of milk](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SCXXGUX47LL4E) (we are nerd cows, you know...)

== Screenshots ==

1. Authorisation screen. Nice and simple. Click the button to start working with the plugin within seconds!
2. General plugin settings. You can easily deauthorize connected account and attach another one.
3. Advanced scheduling allows you to select days and add times at which to tweet.
4. Full-view of the queue.
5. Simplified view of the queue. Helpful for shuffling posts.
6. Health check page to make sure Tweet Wheel has everything it needs to run properly.

== Changelog ==

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
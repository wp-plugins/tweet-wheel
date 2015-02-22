=== Plugin Name ===
Contributors: nerdcow
Tags: auto tweeting, auto tweet, automated tweeting, blog, blogging, cron, feed, social, timeline, twitter, tweet, publish, free, google, manage, post, posts, pages, plugin, seo, profile, sharing, social, social follow, social following, social share, social media, community, wp cron, traffic, optimization, conversion, drive traffic
Requires at least: 3.8
Tested up to: 4.1
Stable tag: 0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automated and redefined post tweeting for every Wordpress blog.

== Description ==

<strong>If there is one desire of every blogger, it is the exposure.</strong>

Tweet Wheel is a simple and yet powerful tool that every blogger will fall in love with. The idea behind Tweet Wheel is to automatically tweet posts from user's blog and take the burden off bloggers' shoulders and let them focus on the thing they are best at - writing.

Thanks to a built-in queueing system, Tweet Wheel is as easy to manage as a music playlist!

### Automated queueing system
In the core of the Tweet Wheel lays automated system that tweets your posts when it's time. It is very stable, safe and keeps your Twitter profile active all the time. Anyone could use a hand in driving extra traffic to their blog, right?

### Features

#### Customise the queue
Add, remove, exclude and shuffle the queue the way you please! Tweet Wheel will never tweet without your consent.

#### Control the timing
Keep your profile consistent and organised. Adjust the break between each tweet made by Tweet Wheel.

#### Tweet once or infinitely
Let Tweet Wheel reschedule every tweeted post.. or don't. It's up to you!

#### Benefit from templating
Set a default post tweet template or overwrite it with each post's custom one!

#### Automatically queue new posts
Add new posts to the queue on publish. Why bother when it can be done automagically!

#### Engage with your audience
Comment on your blog, reply to blog-related tweets and more. Leave the rest to Tweet Wheel.

### Future development
This is a basic idea we have in our minds. However, our ideas to expand Tweet Wheel are endless. We will bring them all to you as long as you show an interest in the plugin! Here are some of them:

1. Multi Twtter account support
1. Adding more queues than just one
1. Use post tags as hashtags
1. Use custom post types to fill in a queue
1. Fill a queue with a category's posts
1. Add custom tweets
1. Multiple tweet template per post support
1. .. many more

All we need is your support :) Any feedback is welcomed.

== Installation ==

## Minimum requirements
* WordPress 3.8 or greater
* PHP version 5.2 or greater
* MySQL version 5.0 or greater
* WP Cron enabled in your WordPress installation

## Installation
There are two ways to install Tweet Wheel

### via WordPress plugin upload - automated
Navigate to Plugins > Upload New Plugin and upload the zip file you have downloaded. Voilla!

### via FTP client - manual
1. Unzip the zip file you have downloaded
1. Upload unzipped folder `tweet-wheel` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

None yet. Feel free to ask them though and we will update this section.

== Screenshots ==

1. General settings screen.
2. The queue. Very controlable and customisable. You can shuffle tweets, tweet them on go, pause / resume the queue. Just check it out!

== Changelog ==

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
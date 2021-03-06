<?php

/**
 * Dashboard class
 *
 */

class TW_Dashboard {

    public static $_instance = null;
    
    // ...
    
	/**
	 * Main TweetWheel Twitter Instance
	 *
	 * Ensures only one instance of TweetWheel Twitter is loaded or can be loaded.
	 *
	 * @since 0.1
	 * @static
	 * @return TweetWheel - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    // ...
    
    /**
     * Construct
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return N/A
     **/
    
    public function __construct() {}
    
    // ...
    
    /**
     * About us page content
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return N/A
     **/
    
    public static function page() {

        ?>
        
		<div class="wrap tweet-wheel about-wrap">
            
			<h1>Welcome to Tweet Wheel!</h1>
        
            <div class="about-text">
                Thank you for joining a happy family of all Tweet Wheel users!<br/>
                Tweet Wheel <?php echo TW_VERSION; ?> is now ready to keep your <br/>
                Twitter profile active and engaging at all times!
            </div>
            
            <div class="tw-badge">Version <?php echo TW_VERSION; ?></div>
            
            <hr>
            
            <div class="changelog point-releases">
            	<h3>What's new</h3>
            	<p><strong>Version <?php echo TW_VERSION; ?></strong> introduces some improvements! For more information, see <a href="https://wordpress.org/plugins/tweet-wheel/changelog/" target="_blank">the changelog notes</a>. 	</p>
            </div>

            <div class="headline-feature">
                <h2>Let's meet</h2>
                <div class="feature-image">
                    <img src="<?php echo TW_PLUGIN_URL ?>/assets/images/featured-image.png">
                </div>
                
                <div class="feature-section">
                    <h3>If there is one desire of every website owner, it is the exposure.</h3>
                    <p>Tweet Wheel is a simple and yet powerful tool that everyone will fall in love with. The idea behind Tweet Wheel is to take the burden off website owners' shoulders and let them focus on the thing they are best at.</p>
                    <p>Promote your blog entires, shop products, case studies, pages and anything you like!</p>
                    <p>Thanks to a built-in queueing system, Tweet Wheel is as easy to manage as a music playlist!</p>
                </div>
            
            </div>
            
            <hr>
            
            <div class="headline-feature">
                <h2>Beauty of the automation</h2>
                
                <p style="text-align:center">Never worry again tweeting regularly about your content. Tweet Wheel will do it for you. Automatically.</p>
                
                <div class="feature-image">
                    
                    <img src="<?php echo TW_PLUGIN_URL ?>/assets/images/queue-explained.png">
                    
                </div>
                
                <hr>
                
                <h2>Say it in many ways</h2>
                
                <p style="text-align:center">We introduced multiple tweet templates to avoid sounding like a broken record. Now you can set as many tweet variations for each post as you like!</p>
                
                <div class="feature-image">
                    
                    <img src="<?php echo TW_PLUGIN_URL ?>/assets/images/multitemplate.png">
                    
                </div>
                
                <hr>
                
                <h2>Benefit from controlled regularity</h2>
                
                <p style="text-align:center">With an in-built scheduler, you can tweet regularly on specific days at specific time. Just the way your followers would expect you to.</p>
                
                <div class="feature-image">
                    
                    <img src="<?php echo TW_PLUGIN_URL ?>/assets/images/scheduling.png">
                    
                </div>
                
                <hr>
                
                <div class="feature-list">
                    <h2>The amazing bit</h2>
                    <div class="feature-section col two-col">
                        <div>
                            <h4>Customise the queue</h4>
                            <p>Add, remove, exclude and shuffle the queue the way you please! Tweet Wheel will never tweet without your consent.</p>
                        </div>
                        <div class="last-feature">
                            <h4>Control the timing</h4>
                            <p>Keep your profile consistent and organised. Schedule posts on specific days at specific times!</p>
                        </div>
                    </div>
                    <div class="feature-section col two-col">
                        <div>
                            <h4>Tweet once or infinitely</h4>
                            <p>Let Tweet Wheel reschedule every tweeted post.. or don't. It's up to you!</p>
                        </div>
                        <div class="last-feature">
                            <h4>Benefit from templating</h4>
                            <p>Set a default post tweet template or overwrite it with each post's custom one! Now multi-templating available, too!</p>
                        </div>
                    </div>
                    <div class="feature-section col two-col">
                        <div>
                            <h4>Automatically queue new posts</h4>
                            <p>Why bother when it can be done automagically!</p>
                        </div>
                        <div class="last-feature">
                            <h4>Engage with your audience</h4>
                            <p>Focus on running your blog or your business. Leave the rest to Tweet Wheel.</p>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <div class="return-to-dashboard">
                <a class="tw-start-button button" href="<?php echo TW()->twitter()->is_authed() == false ? admin_url('/admin.php?page=tw_twitter_auth') : admin_url('/admin.php?page=tw_queue') ?>">Start wheelin' !</a>
            </div>
            
        </div>
        
        <?php
        
    }
    
    // ...
    
    public static function upgrade() {

        ?>
        
		<div class="wrap tweet-wheel about-wrap brap-wrap">
            
			<h1>Upgrade to Tweet Wheel PRO!</h1>
        
            <div class="about-text">
                Tweet Wheel Pro brings new key features to promote your website on the whole level and includes <strong></strong>lifetime updates</strong> for one-off fee!
            </div>
            
            <hr>
               
            <div class="feature-section two-col">
                <div class="col">
                    <h4>Attach Featured Images</h4>
                    <p>Simply tick a checkbox next to the feature image upload for each post and our plugin will enrish your tweets with a photo card!</p>
                    <a class="button tw-start-button" href="http://nrdd.co/upgrade_to_twp" target="_blank">UPGRADE NOW</a>
                </div>

                <div class="col">
                    <img src="//i.imgur.com/Ff4dTYX.jpg">
                </div>
            </div>
               
            <div class="feature-section two-col">
                <div class="col">
                    <img src="//i.imgur.com/jZwsfTn.png">
                </div>
                
                <div class="col">
                    <h4>Fill Up the Queue Like a Pro</h4>
                    <p>Import posts to the queue filtering all fo them by post type, amount and date range!</p>
                    <a class="button tw-start-button" href="http://nrdd.co/upgrade_to_twp" target="_blank">UPGRADE NOW</a>
                </div>
            </div>
                
            <div class="feature-section two-col">
                <div class="col">
                    <h4>Shorten URLs</h4>
                    <p>Authorise with Bit.ly -the most popular shortening URL website - and enjoy beautiful links being visible in all your tweets!</p>
                    <a class="button tw-start-button" href="http://nrdd.co/upgrade_to_twp" target="_blank">UPGRADE NOW</a>
                </div>

                <div class="col">
                    <img src="//i.imgur.com/Y0kG3zL.png">
                </div>
            </div>
            
            <div class="feature-section two-col">
                <div class="col">
                    <img src="//i.imgur.com/2bMbLJl.png">
                </div>
                
                <div class="col">
                    <h4>Track Results</h4>
                    <p>Track engagement of all your tweet templates by analysing the Analytics page showing favorites, retweets and clicks.</p>
                    <a class="button tw-start-button" href="http://nrdd.co/upgrade_to_twp" target="_blank">UPGRADE NOW</a>
                </div>
            </div>
            
            <div class="feature-section two-col">
                <div class="col">
                    <h4>Premium Support</h4>
                    <p>We were there, we are here and we will be there for you! Now with fastest response and one-to-one support we can sort any issues in a blink of an eye.</p>
                    <a class="button tw-start-button" href="http://nrdd.co/upgrade_to_twp" target="_blank">UPGRADE NOW</a>
                </div>

                <div class="col">
                    <img src="//i.imgur.com/RnxvuzW.png">
                </div>
            </div>

            <div class="feature-section two-col">
                <div class="col">
                    <img src="//i.imgur.com/mb6CktW.png">
                </div>
                
                <div class="col">
                    <h4>Lifetime Automated Upgrades</h4>
                    <p>We introduced FREE lifetime upgrades so you can reap even more benefits as a loyal Tweet Wheel Pro user!</p>
                    <a class="button tw-start-button" href="http://nrdd.co/upgrade_to_twp" target="_blank">UPGRADE NOW</a>
                </div>
            </div>
            
        </div>
        
        <?php
        
    }
    
}

/**
 * Returns the main instance of TW_Dashboard
 *
 * @since  0.1
 * @return TW_Dashboard
 */
function TW_Dashboard() {
	return TW_Dashboard::instance();
}
TW_Dashboard();
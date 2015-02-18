<?php

/**
 * Dashboard class
 *
 * @TODO: Eventually, this will be an actual dashboard with useful widgets and shortcuts, but for now let's leave it as About page.
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
    
    public function __construct() {
        
    }
    
    public function page() {
        
        tw_load_settings();
        
        ?>
        
		<div class="wrap tweet-wheel about-wrap">
            
			<h1>Welcome at Tweet Wheel!</h1>
        
            <div class="about-text">
                Thank you for joining a happy family of all Tweet Wheel users!<br/>
                Tweet Wheel <?php echo TW()->version; ?> is now ready to keep your <br/>
                Twitter profile active and engaging at all times!
            </div>
            
            <hr>
            
            <div class="headline-feature">
                <h2>Let's meet</h2>
                <div class="feature-image">
                    <img src="<?php echo TW_PLUGIN_URL ?>/assets/images/featured-image.png">
                </div>
                
                <div class="feature-section">
                    <h3>If there is one desire of every blogger, it is the exposure.</h3>
                    <p>Tweet Wheel is a simple and yet powerful tool that every blogger will fall in love with. The idea behind Tweet Wheel is to take the burden off bloggers' shoulders and let them focus on the thing they are best at - writing.</p>
                    <p>Thanks to a built-in queueing system, Tweet Wheel is as easy to manage as a music playlist!</p>
                </div>
            
            </div>
            
            <hr>
            
            <div class="headline-feature">
                <h2>Beauty of the automation</h2>
                
                <div class="feature-image">
                    
                    <img src="<?php echo TW_PLUGIN_URL ?>/assets/images/queue-explained.png">
                    
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
                            <p>Keep your profile consistent and organised. Adjust the break between each tweet made by Tweet Wheel.</p>
                        </div>
                    </div>
                    <div class="feature-section col two-col">
                        <div>
                            <h4>Tweet once or infinitely</h4>
                            <p>Let Tweet Wheel reschedule every tweeted post.. or don't. It's up to you!</p>
                        </div>
                        <div class="last-feature">
                            <h4>Benefit from templating</h4>
                            <p>Set a default post tweet template or overwrite it with each post's custom one!</p>
                        </div>
                    </div>
                    <div class="feature-section col two-col">
                        <div>
                            <h4>Automatically queue new posts</h4>
                            <p>Why bother when it can be done automagically!</p>
                        </div>
                        <div class="last-feature">
                            <h4>Engage with your audience</h4>
                            <p>Comment on your blog, reply to blog-related tweets and more. Leave the rest to Tweet Wheel.</p>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <div class="return-to-dashboard">
                <a href="<?php echo TW()->twitter()->is_authed() == false ? admin_url('/admin.php?page=tw_twitter_auth') : admin_url('/admin.php?page=tw_queue') ?>">Start rocking!</a>
            </div>
            
        </div>
        
        <?php
        
    }
    
}

/**
 * Returns the main instance of TW
 *
 * @since  0.0.1
 * @return TweetWheel
 */
function TW_Dashboard() {
	return TW_Dashboard::instance();
}
TW_Dashboard();
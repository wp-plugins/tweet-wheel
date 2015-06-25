<?php
$options = array();

// General tab

$options[] = array( 'name' => __( 'General', 'tweet-wheel' ), 'type' => 'heading' );
$options[] = array( 'name' => __( 'General options', 'tweet-wheel' ), 'type' => 'title', 'desc' => __( '', 'tweet-wheel' ) );

/*
$options[] = array(
	'name' => __( 'Users access', 'tweet-wheel' ),
	'desc' => __( 'Select which user roles can manage Tweet Wheel', 'tweet-wheel' ),
	'id'   => 'user_role',
	'desc' => sprintf( '<a style="color:darkorange" href="http://nrdd.co/upgrade_to_twp" target="_blank">%s</a>', __( 'Click here to upgrade to pro and manage users access!', 'tweet-wheel' ) ),
	'type' => 'select',
	'options' => array( __( 'Not available', 'tweet-wheel' ) )
);
*/

$options[] = array(
	'name' => __( 'Allowed post types', 'tweet-wheel' ),
	'desc' => __( 'Select custom post types, which should be used by the plugin', 'tweet-wheel' ),
	'id'   => 'post_type',
	'type' => 'post_type'
);

$options[] = array(
	'name' => __( 'Exclude new posts from the queue?', 'tweet-wheel' ),
	'desc' => __( 'Check if you want new posts to be excluded from the queue by default.', 'tweet-wheel' ),
	'id'   => 'queue_new_post',
	'type' => 'checkbox',
	'options' => array(
		'exclude_by_default' => 1
	)
);

$options[] = array(
	'name' => __( 'Default tweet template', 'tweet-wheel' ),
	'desc' => __( 'Default tweet text can be overriden by custom post tweet text setting available on edit page of each post. Allowed tags: {{TITLE}} for post title and {{URL}} for post permalink.', 'tweet-wheel' ),
	'id'   => 'tweet_template',
	'type' => 'textarea',
	'placeholder' => 'What\'s happenng?',
	'std' => '{{TITLE}} - {{URL}}'
);

$options[] = array(
	'name' => __( 'Loop infinitely?', 'tweet-wheel' ),
	'desc' => __( 'Check if you want the most recent tweeted post to be re-queued automatically.', 'tweet-wheel' ),
	'id'   => 'loop',
	'type' => 'checkbox',
	'options' => array(
		'loop' => 1
	),
	'std' => 1
);

$options[] = array(
	'name' => __( 'Disconnect Twitter Account', 'tweet-wheel' ),
	'desc' => __( 'You will need to authorize another account to resume using this plugin.', 'tweet-wheel' ),
	'id'   => 'deauth',
	'type' => 'deauth'
);

// Schedule

$options[] = array( 'name' => __( 'Schedule', 'tweet-wheel' ), 'type' => 'heading', 'class' => 'lololol' );
$options[] = array( 'name' => __( 'Schedule options', 'tweet-wheel' ), 'type' => 'title', 'desc' => __( '', 'tweet-wheel' ) );

$options[] = array(
	'name' => __( 'Week days', 'tweet-wheel' ),
	'id'   => 'days',
	'type' => 'checkbox',
	'multiple' => true,
	'options' => array(
		'1' => 'Monday',
		'2' => 'Tuesday',
		'3' => 'Wednesday',
		'4' => 'Thursday',
		'5' => 'Friday',
		'6' => 'Saturday',
		'7' => 'Sunday'
	)
);
<?php

class Config {

	// the file containing the meme sentences
	const SENTENCES = './data/sentences.txt';
	// background images to pick from - at least 1 is required
	const BACKGROUND_IMAGES = array('./images/image1.jpg', './images/image2.jpg');


	// AWS bucket to push images to
	const AWS_BUCKET = 'AWS_BUCKET_NAME';
	// AWS key 
	const AWS_ACCESS_KEY = 'AWS_KEY';
	// AWS secret
	const AWS_ACCESS_SECRET = 'AWS_SECRET';


	// prefix the @slackbot post with this
	const SLACK_PREFIX = 'It\'s 9.05 guys. ';
	// Slack API key
	const SLACK_KEY = 'SLACK_API_KEY';
	// Slack channel (requires the hash at the front urlencoded as %23 i.e. %23dev for #dev)
	const SLACK_CHANNEL = 'SLACK_CHANNEL_NAME';
	// Slack domain i.e. the company name
	const SLACK_DOMAIN = 'SLACK_DOMAIN';
}
?>
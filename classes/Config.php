<?php

class Config {

	// the file containing the meme sentences
	const SENTENCES = './data/sentences.txt';
	// background images to pick from
	const BACKGROUND_IMAGES = array('./images/image1.jpg', './images/image2.jpg');


	// AWS bucket to push images to
	const AWS_BUCKET = 'AWS_BUCKET_NAME';
	// aws key 
	const AWS_ACCESS_KEY = 'AWS_KEY';
	// aws secret
	const AWS_ACCESS_SECRET = 'AWS_SECRET';


	// prefix the slackbot post with this
	const SLACK_PREFIX = 'It\'s 9.05 guys. ';
	// slack API key
	const SLACK_KEY = 'SLACK_API_KEY';
	// slak channel (requires the hash at the front)
	const SLACK_CHANNEL = 'SLACK_CHANNEL_NAME';
	// slack domain i.e. the company name
	const SLACK_DOMAIN = 'SLACK_DOMAIN';
}
?>
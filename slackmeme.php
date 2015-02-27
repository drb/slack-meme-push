<?php

/**
 * Quick and dirty script to push meme-type images to the Slack service using the @slackbot account
 */

// load some util classes we use
require_once('classes/Meme_generator.php');
require_once('classes/S3.php');
require_once('classes/Config.php');

// the s3 bucket name
$bucket = Config::AWS_BUCKET;

// init some content variables
$line   = randomLine(Config::SENTENCES);
$parts  = explode(' ', $line);
$len    = count($parts);

// seed the available images
$images = Config::BACKGROUND_IMAGES;

// split the sentence up into top and bottom portions
$firsthalf  = implode(" ", array_slice($parts, 0, $len / 2));
$secondhalf = implode(" ", array_slice($parts, $len / 2));

// choose a random image from the seeded cache
$example_image_path = $images[array_rand($images)] ;

// hash the text with the selected image filename for uniqueness
$filenameHash = md5($firsthalf . $secondhalf . $example_image_path) . '.jpg';

// generate the image
$mg = new Meme_generator();
$mg->set_top_text(strtoupper($firsthalf));
$mg->set_bottom_text(strtoupper($secondhalf));
$mg->set_output_dir('./tmp/');
$mg->set_image($example_image_path);
$mg->set_font('./fonts/Impact.ttf');
$mg->set_font_ratio( 0.07 );
$mg->set_margins_ratio( 0.03 );
$output_image = $mg->generate();
            
// instantiate the class to push to s3
$s3 = new S3(Config::AWS_ACCESS_KEY, Config::AWS_ACCESS_SECRET);

// now push the file to s3
if($s3->putObjectFile('./tmp/' . $output_image, $bucket, $filenameHash, S3::ACL_PUBLIC_READ) )
{

    // generate the path to the asset on s3 we just sent
    $s3File = 'https://s3-eu-west-1.amazonaws.com/' . $bucket . '/' . $filenameHash;

    // generate a curl instance to mke the request
    $ch = curl_init('https://' . Config::SLACK_DOMAIN . '.slack.com/services/hooks/slackbot?token=' . Config::SLACK_KEY . '&channel=' . Config::SLACK_CHANNEL);                                                                      
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, Config::SLACK_PREFIX . $s3File);                                                                  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                                                                                                                                       
     
    // make the call to the webservice... and we're done.
    $result = curl_exec($ch);
}

/**
 * randomLine
 * Get a random quote from the store
**/
function randomLine ($fileName, $maxLineLength = 4096) {

    $handle = @fopen($fileName, "r");

    if ($handle) {

        $random_line = null;
        $line = null;
        $count = 0;

        while (($line = fgets($handle, $maxLineLength)) !== false) {

            $count++;

            if(rand() % $count == 0) {
              $random_line = $line;
            }
        }

        if (!feof($handle)) {
            echo "Error: unexpected fgets() fail\n";
            fclose($handle);
            return null;
        } else {
            fclose($handle);
        }
        return $random_line;
    }
}
?>

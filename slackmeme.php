<?php

require_once('classes/Meme_generator.php');
require_once('classes/S3.php');
require_once('classes/Config.php');

// bucket Name
$bucket = Config::AWS_BUCKET;

// usage
$line   = rand_line(Config::SENTENCES);
$parts  = explode(' ', $line);
$len    = count($parts);

$images = Config::BACKGROUND_IMAGES;

$firsthalf  = implode(" ", array_slice($parts, 0, $len / 2));
$secondhalf = implode(" ", array_slice($parts, $len / 2));

$example_image_path = $images[array_rand($images)] ;
$filenameHash = md5($firsthalf . $secondhalf) . '.jpg';

// Output example image 1
$mg = new Meme_generator();
$mg->set_top_text(strtoupper($firsthalf));
$mg->set_bottom_text(strtoupper($secondhalf));
$mg->set_output_dir('./tmp/');
$mg->set_image($example_image_path);
$mg->set_font('./fonts/Impact.ttf');
$mg->set_font_ratio( 0.07 );
$mg->set_margins_ratio( 0.03 );
$output_image = $mg->generate();
            
//instantiate the class
$s3 = new S3(Config::AWS_ACCESS_KEY, Config::AWS_ACCESS_SECRET);

// push the file to s3
if($s3->putObjectFile('./tmp/' . $output_image, $bucket, $filenameHash, S3::ACL_PUBLIC_READ) )
{

    // generate the path to the asset on s3
    $s3File = 'https://s3-eu-west-1.amazonaws.com/' . $bucket . '/' . $filenameHash;

    // send the image to slack
    $ch = curl_init('https://' . Config::SLACK_DOMAIN . '.slack.com/services/hooks/slackbot?token=' . Config::SLACK_KEY . '&channel=' . Config::SLACK_CHANNEL);                                                                      
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, "It's 9.05 guys. " . $s3File);                                                                  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                                                                                                                                       
     
    $result = curl_exec($ch);
}

/**
 * Get a random bullshit quote from the store
**/
function rand_line($fileName, $maxLineLength = 4096) {

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

<?php 
/**
 * Meme generator class:
 * - Generates image file with two lines of text - top & bottom
 * - Is very customizable
 * - Works well with different frameworks (tested on CodeIgniter)
 * - Supports watermarking
 *
 * @author http://neart.eu/
 * @copyright Copyright &copy; 2013 Neart Interactive Agency, Libera Solutions Ltd.
 * @version 1.0
 * @example ./example.php Class usage examples
 */
class Meme_generator
{
	public $config;

	public function __construct()
	{
		$this->clear(); // DEFAULT BASIC SETTING 
	}

	/**
	* Bulk config load - instead of setters
	* @param Array class configuration array, possible keys: 
	* meme_font / meme_output_dir / meme_font_to_image_ratio / meme_margins_to_image_ratio / meme_image_path / meme_top_text / 
	* meme_bottom_text / meme_font
	* meme_watermark_file / meme_watermark_margins / meme_watermark_opacity
	*/
	public function load_config ($config)
	{
		$this->config = $config;
	}
  	/**
	* Sets image file path
	* @param String image file path
	*/ 
	public function set_image ( $image_path ) 
	{
		$this->config['meme_image_path'] = $image_path;
	}

  	/**
	* Sets TOP text on image
	* @param String
	*/ 
	public function set_top_text( $txt ) 
	{
		$this->config['meme_top_text'] = $txt;
	}
  	
  	/**
	* Sets BOTTOM text on image
	* @param String 
	*/ 
	public function set_bottom_text( $txt ) 
	{
		$this->config['meme_bottom_text'] = $txt;
	}

  	/**
	* Sets font for texts (TTF)
	* @param String font file path
	*/ 
	public function set_font ( $file ) 
	{
		$this->config['meme_font'] = $file ;
	}

  	/**
	* Sets output path for generated image (default to ./)
	* @param String path
	*/ 
	public function set_output_dir( $path ) 
	{
		$this->config['meme_output_dir'] = $path;
	}

	/**
	* If You know what You're doing - You can change font size to image ratio (default to 0.04)
	* Recommended values: 0.01 - 0.05
	* @param float 
	*/
	public function set_font_ratio( $ratio )
	{
		$this->config['meme_font_to_image_ratio'] = $ratio;
	}
	/**
	* Sets output image quality - default to 90
	*/
	public function set_quality( $q )
	{
		$this->config['meme_quality'] = $q;
	}

	/**
	* If You know what You're doing - You can change margins to image ratio (default to 0.02)
	* Recommended values: 0.01 - 0.05
	* @param float 
	*/
	public function set_margins_ratio( $ratio )
	{
		$this->config['meme_margins_to_image_ratio'] = $ratio;
	}


	/**
	* Set watermark PNG image file path
	* @param String PNG file path
	*/
	public function set_watermark( $path )
	{
		$this->config['meme_watermark_file'] = $path;
	}

	/**
	* Sets watermark margins - right & bottom, defaults to 10 px
	* @param integer margins in pixels
	*/
	public function set_watermark_margins( $margins = 10 )
	{
		 $this->config['meme_watermark_margins'] = $margins;
	}

	/**
	* Sets watermark opacity - default to 50 
	* @param integer opacity 0 - 100
	*/
	public function set_watemark_opacity( $opacity = 50)
	{
		 $this->config['meme_watermark_opacity'] = $opacity;
	}

	/**
	* Resets all of the values used when processing an image. You will want to call this if you are processing images in a loop.
	*/
	public function clear()
	{
		$this->config['meme_image_path'] = null;
		$this->config['meme_top_text'] = '';
		$this->config['meme_bottom_text'] = '';
		$this->config['meme_watermark_file'] = null;
		$this->config['meme_font'] = './fonts/DejaVuSansMono-Bold.ttf';
		$this->config['meme_output_dir'] = './';
		$this->config['meme_font_to_image_ratio'] = 0.04;
		$this->config['meme_margins_to_image_ratio'] = 0.02;
		$this->config['meme_quality'] = 90;
		$this->config['meme_watermark_margins'] = 10;
		$this->config['meme_watermark_opacity'] = 50;
		$this->config['meme_prefix'] = 'meme_';
		$this->config['debug'] = array();
	}

	public function debug()
	{
		return $this->config['debug'];
	}

  	/**
	 * Generate meme on image file
	 * @return String newly created image file name (stored in $this->config['meme_output_dir'] )
  	 */
  	public function generate()
  	{
		if ( ! isset( $this->config['meme_image_path'] ) || ! strlen( $this->config['meme_image_path'] ) ) {
			die ( 'Meme_generator Class ERROR : Trying to generate meme without image file specified');
		}
  		$tmp_sizes = getimagesize( $this->config['meme_image_path'] );
  		$image_width = $tmp_sizes[0];
  		$image_height= $tmp_sizes[1];
		$this->config['debug'] = array_merge ($this->config['debug'], $this->config);
		$this->config['debug'] = array_merge ($this->config['debug'], $tmp_sizes );
  		
  		$text1 = preg_replace('/\s\s+/', ' ',  $this->config['meme_top_text'] );
  		$text2 = preg_replace('/\s\s+/', ' ', $this->config['meme_bottom_text'] );

  		$font = $this->config['meme_font'];
  		$font_size = round($this->config['meme_font_to_image_ratio'] * $image_width );
  		$margin = round($this->config['meme_margins_to_image_ratio'] * $image_width );
  		
  		$text1_params = $this->get_text_params($text1,$image_width,$margin,$font,$font_size);
  		$text2_params = $this->get_text_params($text2,$image_width,$margin,$font,$font_size);
		// Some extra debug info
		$this->config['debug']['font_size'] = $font_size;
		$this->config['debug']['margin'] = $margin;
		$this->config['debug']['text1_params'] = $text1_params;
		$this->config['debug']['text2_params'] = $text2_params;
  		
  		$im = @imagecreatefromstring(file_get_contents( $this->config['meme_image_path'] ));
  		$white = imagecolorallocate($im, 255, 255, 255);
  		$black = imagecolorallocate($im, 0, 0, 0);

		if ( isset ( $this->config['meme_watermark_file'] ) && strlen( $this->config['meme_watermark_file'] ) ) { 
			// watermark file selected - watermarking...
			//$overlay_gd_image = imagecreatefrompng($this->config['meme_watermark_file']);
			$overlay_gd_image = @imagecreatefromstring(file_get_contents  ($this->config['meme_watermark_file'] ) );
			$overlay_width = imagesx($overlay_gd_image);
			$overlay_height = imagesy($overlay_gd_image);

			$merge_result = imagecopymerge(
				$im,
				$overlay_gd_image,
				$image_width - $overlay_width - $this->config['meme_watermark_margins'] ,
				$image_height - $overlay_height - $this->config['meme_watermark_margins'],
				0,
				0,
				$overlay_width,
				$overlay_height,
				$this->config['meme_watermark_opacity']
			);
			$this->config['debug']['meme_watermark_file'] = $this->config['meme_watermark_file'];
			$this->config['debug']['meme_watermark_margins'] = $this->config['meme_watermark_margins'];
			$this->config['debug']['meme_watermark_opacity'] = $this->config['meme_watermark_opacity'];
			imagedestroy($overlay_gd_image);
		} 

  		// top text
  		imagettftext($im, $font_size, 0,
	  		$text1_params['centered_start'],
	  		$font_size+$margin,
	  		$black, $font, $text1_params['text']
  		);
  		imagettftext($im, $font_size, 0,
	  		$text1_params['centered_start']-2,
	  		$font_size+$margin-2,
	  		$white, $font, $text1_params['text']
  		);
  		// bottom text
  		imagettftext($im, $font_size, 0,
	  		$text2_params['centered_start'] ,
	  		$image_height-$text2_params['height']+$font_size+$margin,
	  		$black, $font, $text2_params['text']
  		);
  		imagettftext($im, $font_size, 0,
	  		$text2_params['centered_start']-2,
	  		$image_height-$text2_params['height']+$font_size+$margin-2,
	  		$white, $font, $text2_params['text']
  		);
		$fname = $this->config['debug']['meme_recent_file_name'] 
			= $this->config['meme_prefix'] . md5(serialize( $this->config )) .time() . '.jpg'; // serialize is hack for cleared obejcts in loop
  		$cache_file_name =  $this->config['meme_output_dir'] . $fname;
		$this->config['debug']['cache_file_name'] = $cache_file_name;
  		imagejpeg($im, $cache_file_name, $this->config['meme_quality']);
  		imagedestroy($im);
  		return $fname;
  	}
  	
  	/**
	* Positioning texts
  	*/
  	private function get_text_params($text,$width,$margin,$font,$font_size)
  	{
  		$rv = array();
  		$text_a = explode(' ', $text);
  		$text_new = '';
  		foreach($text_a as $word){
  			$box = imagettfbbox($font_size, 0, $font, $text_new.' '.$word);
  			if($box[2] > $width - $margin*2){
  				$text_new .= "\n".$word;
  			} else {
  				$text_new .= " ".$word;
  			}
  		}
  	
  		$text_new = $this->align_center_img_txt($text_new);
  		$box = imagettfbbox($font_size, 0, $font, $text_new);
  		$rv['text'] = $text_new;
  		$rv['height'] = $box[1] + $font_size + $margin * 2;
  		$rv['centered_start'] = ceil(($width-$box[2]) / 2);
  		return $rv;
  	}
  	
  	/**
	* Some text centering and text padding with spaces
  	*/
  	private function align_center_img_txt ($text_new)
  	{
  		$text_new = trim($text_new);
  		$text_a_tmp = explode("\n", $text_new);
  		$max_line_length1 = 0;
	  	foreach($text_a_tmp as $line){
	  		if ( mb_strlen($line) > $max_line_length1) $max_line_length1 = mb_strlen($line);
	  	}
	  	$text_new = ''; // reset
	  	foreach($text_a_tmp as $line){
	  		$text_new .= $this->mb_str_pad($line, $max_line_length1, ' ', STR_PAD_BOTH) . "\n";
	  	}
	  	return $text_new;
  	}
  	
  	private function mb_str_pad ($input, $pad_length, $pad_string, $pad_style, $encoding="UTF-8") {
  		return str_pad (
			$input,
  			strlen($input)-mb_strlen($input,$encoding)+$pad_length, $pad_string, $pad_style
			);
  	}
  	
}

?>
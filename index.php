<?php

/**
 * EC01 Media Index.
 *
 * Allows media (images, audio and video) to be viewed in a directory through a
 * single index file.
 *
 * @package EC01 Media Index
 * @since 1.0.0
 * @author Clarence Bos <cbos@tnoep.ca>
 * @copyright Copyright (c) 2018, Clarence Bos
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 * @link http://wp.cbos.ca/plugins/ec01-media-index
 *
 * @wordpress-plugin
 * Plugin Name: EC01 Media Index
 * Plugin URI:  http://wp.cbos.ca/plugins/ec01-media-index/
 * Description: Allows media (image, audio and video) to be viewed in a directory through a single file. Shortcode [media-index].
 * Version: 1.0.0
 * Author: Clarence Bos
 * Author URI: http://ec01.earth3300.info/
 * Text Domain: ec01-media-index
 * License:  GPL-3.0+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 */

/**
 * Allows media (jpg, png, mp3, mp4) to be viewed in the given directory.
 *
 * See the bottom of this file for a more complete description
 * and the switch for determining the context in which this file
 * is found.
 */
class MediaIndex
{

	/** @var array Default options. */
	protected $opts = [
		'dim' => [ 'width' => 800, 'height' => 600 ],
		'max' => 12,
		'msg' => [ 'na' => '', ],
		'subtypes' => [
			'jpg' => [ 'type' => 'image' ],
			'png'=> [ 'type' => 'image' ],
			'mp4' => [ 'type' => 'video' ],
			'mp3' => [ 'type' => 'audio' ],
			],
		'supertype' => 'media',
	];

	/**
	 * Gets the list of images as HTML.
	 *
	 * In order to process the media file correctly, the browser has to know what
	 * MIME (Multipurpose Internet Mail Extension) it is. The MIME type has the
	 * format: `type/subtype`, thus `image/png`, `image/jpeg`, `video/mp4` or
	 * `audio/mpeg` {@link https://tools.ietf.org/html/rfc3003}. We are simply
	 * using the extensions (mp3, mp4, jpg and png) here and requiring the person
	 * responsible for the content to ensure consistency and that the extension
	 * correlates with the MIME type for that file (i.e mp3 _is_ an audio file
	 * in the mpeg format). The `jpeg` MIME type is expected to be found as `jpg`
	 * NOT `jpeg`, again for programmatic and visual consistency.
	 *
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function get( $args = null )
	{
		/** If no arguments are set, assume current directory */
		if ( $args = $this->setDirectorySwitch( $args ) )
		{
			$max = $this->getMaxImages( $args );
			$subtypes = $this->opts['subtypes'];

			/** Add the "supertype" to the class string (i.e. media). */
			$args['class'] = $this->opts['supertype'];

			$str = '<article>' . PHP_EOL;
			foreach ( $subtypes as $subtype => $type ) {
				$args['type'] = $type['type'];
				$args['subtype'] = $subtype;

				/** If the class is not already present, add it. */
				if( strpos( $args['class'], $args['type'] ) === FALSE )
				   {
					$args['class'] .= ' ' . $args['type'];
				   }
				if ( $match = $this->getMatchPattern( $subtype, $args ) )
				{
					$str .= $this->iterateFiles( $match, $max, $args );
				}
			}
			$str .= '</article>' . PHP_EOL;

			if ( isset( $args['doctype'] ) && $args['doctype'] )
			{
				$str = $this->getPageHtml( $str, $args );
			}
			return $str;
		}
		else
		{
			return "Error.";
		}
	}

	/**
	 * Iterate over files.
	 *
	 * Capability for jpg, png, mp3 and mp4
	 *
	 * @param string $match
	 * @param array $args
	 *
	 * @return string
	 */
	private function iterateFiles( $match, $max, $args )
	{
		$str = '';
		$cnt = 0;

		foreach ( glob( $match ) as $file )
		{
			$cnt++;
			if ( $cnt > $max )
			{
				break;
			}

			$args['file'] = $file;
			/** Remove the root of the file path to use it an image source. */
			$args['src'] = $this->getSrcFromFile( $args['file'] );
			$args['name-dim'] = $this->getImageNameDimArr( $args['src'] );
			$args['name'] = $args['name-dim']['name'];
			$args['dim'] = $this->getMediaDimArr( $args['name-dim']['strDim'] );
			$str .= $this->getMediaItemHtml( $args );
		}
		return $str;
	}

	/**
	 * Get the source from the file, checking for a preceding slash.
	 *
	 * @param string $str
	 * @return string
	 */
	private function getSrcFromFile( $str )
	{
		$src = str_replace( $this->getSitePath(), '', $str );
		/** May be server inconsistency, therefore remove and add again. */
		$src = ltrim( $src, '/' );
		return '/' . $src;
	}

	/**
	 * Get the SITE_PATH
	 *
	 * Get the SITE_PATH from the constant, from ABSPATH (if loading within WordPress
	 * as a plugin), else from the $_SERVER['DOCUMENT_ROOT']
	 *
	 * Both of these have been tested online to have a preceding forward slash.
	 * Therefore do not add one later.
	 *
	 * @return bool
	 */
	private function getSitePath()
	{
		if ( defined( 'SITE_PATH' ) )
		{
			return SITE_PATH;
		}
		/** Available if loading within WordPress as a plugin. */
		elseif( defined( 'ABSPATH' ) )
		{
			return ABSPATH;
		}
		else
		{
			return $_SERVER['DOCUMENT_ROOT'];
		}
	}

	/**
	 * Get the maximum number of images to process.
	 *
	 * @param array $args
	 * @return int
	 */
	private function getMaxImages( $args )
	{
		if ( isset( $args['max'] ) )
		{
			$max = $args['max'];
		}
		else
		{
			$max = $this->opts['max'];
		}
		return $max;
	}

	/**
	 * Build the match string.
	 *
	 * This is iterated through for each type added to $types, above. A basic
	 * check for a reasonable string length (currently 10) is in place. Can
	 * develop this further, if needed.
	 *
	 * @param string $type  'jpg', 'png'
	 * @param array $args
	 *
	 * @return string|false
	 */
	private function getMatchPattern( $type, $args )
	{
		$path = $this->getBasePath( $args );
		$prefix = "/*";
		$match =  $path . $prefix . $type;

		/** Very basic check. Can improve, if needed. */
		if ( strlen( $match ) > 10 )
		{
			return $match;
		}
		else {
			return false;
		}
	}

	/**
	 * Get the Base Path to the Media Directory.
	 *
	 * This does not need to include the `/media` directory.
	 *
	 * @param array $args
	 * @return string
	 */
	private function getBasePath( $args )
	{
		if ( isset( $args['self'] ) )
		{
			$path = __DIR__;
		}
		elseif ( defined( 'SITE_CDN_PATH' ) )
		{
			$path = SITE_CDN_PATH;
		}
		return $path;
	}

	/**
	 * Get the Media Directory
	 *
	 * @param array $args
	 *
	 * @return string
	 *
	 * @example $args['dir'] = '/architecture/shelter/micro-cabin/'
	 */
	private function getMediaDir( $args )
	{
		if ( isset( $args['dir'] ) )
		{
			$media = $args['dir'];
		}
		else
		{
			$media = '/media';
		}
		return $media;
	}

	/**
	 * Get the Media Item HTML.
	 *
	 * If no type is specified, return the default (.jpg). Include the dot (.)
	 * for simplicity.
	 *
	 * jpg, png, mp3, mp4 (Default: jpg)
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	private function getMediaItemHtml( $args )
	{
		$str = '';
		switch( $args['type'] )
		{
			case 'image':
				$str = $this-> getImageHtml( $args);
				break;

			case 'audio':
				$str = $this-> getAudioHtml( $args);
				break;

			case 'video':
				$str = $this-> getVideoHtml( $args);
				break;

			default:
				$str = $this-> getImageHtml( $args);
				break;
		}
		return $str;
	}

	/**
	 * Get the Image HTML.
	 *
	 * Types: jpg, png
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	private function getImageHtml( $args )
	{
		$dim['width'] = $this->getImageWidth( $args );
		$dim['height'] = $this->getImageHeight( $args );
		$str = '<div class="media image">' . PHP_EOL;
		$str .= sprintf( '<a href="%s">%s', $args['src'], PHP_EOL );
		$str .= '<img';
		$str .= sprintf( ' class="%s"', $this->getImageClass( $args ) );
		$str .= sprintf( ' src="%s"', $args['src'] );
		$str .= sprintf( ' alt="%s"', $this->getImageAlt( $args ) );
		$str .= sprintf( ' width="%s"', $dim['width'] );
		$str .= sprintf( ' height="%s"', $dim['height'] );
		$str .= ' />' . PHP_EOL;
		$str .= '</a>' . PHP_EOL;
		$str .= '<p class="text-center">';
		if ( $name = $this->getMediaName( $args['name'] ) )
		{
			$str .= sprintf( '<span class="name">%s</span>', $name );
		}
		if ( ! empty ( $dim['width'] && ! empty( $dim['height'] ) ) )
		{
			$str .= sprintf( ' <span class="dimensions">%sX%s</span>', $dim['width'], $dim['height'] );
		}
		if ( $size = $this->getMediaSize( $args ) )
		{
			$str .= sprintf( ' <span class="size">%s</span>', $size );
		}
		$str .= '</p>' . PHP_EOL;
		$str .= '</div>' . PHP_EOL;

		return $str;
	}

	/**
	 * Get the Video HTML.
	 *
	 * Types: jpg, png
	 *
	 * @param array $args
	 *
	 * @return string
	 * @example <audio controls>
	 * <source src="myAudio.mp3" type="audio/mp3">
	 * </audio>
	 */
	private function getAudioHtml( $args )
	{
		$str = '<div class="media audio">' . PHP_EOL;
		$str .= '<audio controls' . PHP_EOL;
		$str .= sprintf( '<source src="%s" type="audio/mp3">%s', $args['src'], PHP_EOL );
		$str .= '</audio>' . PHP_EOL;
		$str .= '<p class="text-center">';
		$str .= sprintf( '<span class="name">%s</span>', $this->getMediaName( $args['name'] ) );
		$str .= sprintf( ' <span class="size">%s</span>', $this->getMediaSize( $args ) );
		$str .= '</p>' . PHP_EOL;
		$str .= '</div>' . PHP_EOL;

		return $str;
	}

	/**
	 * Get the Video HTML.
	 *
	 * Types: mp4.
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	private function getVideoHtml( $args )
	{
		$str = '<div class="media video">' . PHP_EOL;
		$str .= '<video controls ' . PHP_EOL;
		$str .= sprintf( 'width="%s" ', $args['dim']['width'] );
		$str .= sprintf( 'height="%s">%s', $args['dim']['height'], PHP_EOL );
		$str .= sprintf( '<source src="%s" type="video/mp4">%s', $args['src'], PHP_EOL );
		$str .= '</video>' . PHP_EOL;
		$str .= '<p class="text-center">';
		$str .= sprintf( '<span class="name">%s</span>', $this->getMediaName( $args['name'] ) );
		$str .= sprintf( ' <span class="size">%s</span>', $this->getMediaSize( $args ) );
		$str .= '</p>' . PHP_EOL;
		$str .= '</div>' . PHP_EOL;

		return $str;
	}

	/**
	 * Wrap the string in page HTML `<!DOCTYPE html>`, etc.
	 *
	 * @param string $str
	 * @return string
	 */
	public function getPageHtml( $html, $args )
	{
		$str = '<!DOCTYPE html>' . PHP_EOL;
		$str .= sprintf( '<html class="dynamic %s" lang="en-CA">', $args['type'], PHP_EOL );
		$str .= '<head>' . PHP_EOL;
		$str .= '<meta charset="UTF-8">' . PHP_EOL;
		$str .= '<meta name="viewport" content="width=device-width, initial-scale=1"/>' . PHP_EOL;
		$str .= '<title>Media Index</title>' . PHP_EOL;
		$str .= '<meta name="robots" content="noindex,nofollow" />' . PHP_EOL;
		$str .= '<link rel=stylesheet href="/0/theme/css/style.css">' . PHP_EOL;
		$str .= '</head>' . PHP_EOL;
		$str .= '<body>' . PHP_EOL;
		$str .= '<main>' . PHP_EOL;
		$str .= $html;
		$str .= '</main>' . PHP_EOL;
		$str .= '<footer>' . PHP_EOL;
		$str .= '<div class="text-center"><small>';
		$str .= 'Note: This page has been <a href="https://github.com/earth3300/ec01-media-index.git">automatically generated</a>. No header, footer, menus or sidebars are available.';
		$str .= '</small></div>' . PHP_EOL;
		$str .= '</footer>' . PHP_EOL;
		$str .= '</html>' . PHP_EOL;

		return $str;
	}

	/**
	 * Get the Image Name and Dimensions from the String.
	 *
	 * Return the direct string values of each. Do no extra processing here.
	 *
	 * $regex = '/\/([a-z0-9\-]{3,150})-([0-9]{2,4}x[0-9]{2,5})\./'
	 *
	 * Part 1: Looks for letters, numbers and dashes from 3 to 150 characters.
	 * Part 2: Followed by a dash, then dimesions from 2 to 4 x 2 to 5 characters.
	 * Part 3: Ending. Followed by a dot \. (which will come before the extension).
	 *
	 * Not only does it divide the string given it into two parts, but it
	 * also does a basic quality check on the image name structure. If the image
	 * name does not meet the criteria given, it won't be captured.
	 *
	 * @param string $str
	 *
	 * @return array  $arr['name'] $arr['dim']
	 *
	 * @example $arr['name'] = 'image-name'
	 * @example $arr['dim'] = '800x600'
	 */
	private function getImageNameDimArr( $str )
	{
		/**
		 * Since we won't have a valid image name with fewer than 13 characaters
		 * we won't bother processing anything with less than that length.
		 */
		if ( strlen( $str ) > 12 )
		{
			/** If this isn't matched, check for a name only */
			$regex = '/\/([a-z0-9\-]{3,150})-([0-9]{2,4}x[0-9]{2,5})\./';
			preg_match( $regex, $str, $match );

			if ( empty( $match ) )
			{
				$regex = '/\/([a-z,0-9\-]{5,150})\./';
				preg_match( $regex, $str, $match );
			}

			if ( ! empty( $match[1] ) )
			{
				$arr['name'] = $match[1];
			}
			else
			{
				$arr['name'] = null;
			}

			if ( ! empty( $match[2] ) )
			{
				$arr['strDim'] = $match[2];
			}
			else
			{
				$arr['strDim'] = null;
			}
			return $arr;
		}
		else {
			return false;
		}
	}

	/**
	 * Get the image dimensions.
	 *
	 * Gets the image dimensions as the last part of the file name and only if
	 * it follows the format: ##x##, where # is an integer.
	 *
	 * @param string $str
	 *
	 * @return string
	 *
	 * @example '1280x720'  $dim['width'] = 1280, $dim['height'] = 720
	 */
	private function getMediaDimArr( $str )
	{
		if ( strlen( $str ) > 4 )
		{
			$arr = explode( 'x', $str );
			$dim['width'] = $arr[0];
			$dim['height'] = $arr[1];
			return $dim;
		}
		else
		{
			$dim['width'] = $this->opts['dim']['width'];
			$dim['height'] = $this->opts['dim']['height'];
			return $dim;
		}
	}

	/**
	 * Get the image width.
	 *
	 * @param array $args
	 * @return string
	 */
	private function getImageWidth( $args )
	{
		if ( ! empty( $args['dim']['width'] ) )
		{
			$width = $args['dim']['width'];
		}
		else if ( defined( 'SITE_IMAGE_WIDTH' ) )
		{
			$width = SITE_IMAGE_WIDTH;
		}
		return $width;
	}

	/**
	 * Get the image height.
	 *
	 * @param array $args
	 * @return string
	 */
	private function getImageHeight( $args )
	{
		if ( defined( 'SITE_IMAGE_HEIGHT' ) )
		{
			$height = SITE_IMAGE_HEIGHT;
		}
		else
		{
			$height = $args['dim']['height'];;
		}
		return $height;
	}

	/**
	 * Get the image size
	 *
	 * @param array $args
	 *
	 * @return string|null
	 */
	private function getMediaSize( $args ){

		if ( isset( $args['file'] ) )
		{
			$size = filesize( $args['file'] );
			$size = number_format( $size / 1000, 1, ".", "," );
			return $size . ' kB';
		}
		else {
			return null;
		}
	}

	/**
	 * Get the image alt tag.
	 *
	 * May be the same as the name (or not).
	 *
	 * @param array $args
	 * @return string
	 */
	private function getImageAlt( $args )
	{
		if ( "" !== $args['src'] )
		{
			$ex = explode( '/', $args['src'] );
			$alt = $ex[ count( $ex ) - 1 ];
		}
		else
		{
			$alt = $this->opts['msg']['na'];
		}
		return $alt;
	}

	/**
	 * Get the Media Name
	 *
	 * @param array $args
	 * @return string
	 */
	private function getMediaName( $str )
	{
		if ( strlen( $str ) > 2 )
		{
			$name = str_replace( '-', ' ', $str );
			$name = strtoupper( $name );
		}
		else
		{
			$name = $this->opts['msg']['na'];
		}
		return $name;
	}


	/**
	 * Get the image class.
	 *
	 * @param array $args
	 * @return string
	 */
	private function getImageClass( $args )
	{
		if ( defined( 'SITE_IMAGE_CLASS' ) )
		{
			$class = SITE_IMAGE_CLASS;
		}
		else
		{
			$class = 'generic';
		}
		return $class;
	}

	/**
	 * Set the Directory Switch (Process Containing or Given Directory).
	 *
	 * If $args['self'] or $args['dir'] are not set, it assumes we are in the
	 * directory for which images are to be processed. Therefore $args['self']
	 * is set to true and $args['dir'] is set to null. We also have to set the
	 * $args['doctype'] to true to know whether or not to wrap the output in
	 * the correct doctype and the containing html and body elements.
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	private function setDirectorySwitch( $args )
	{
		/** If $args['dir'] is not set, set it to false. */
		$args['dir'] = isset( $args['dir'] ) ? $args['dir'] : false;

		/** if $args['dir'] == false, set $args['self'] to true. */
		if ( ! $args['dir'] )
		{
			$args['self'] = true;
			$args['doctype'] = true;
			return $args;
		}
		else
		{
			return $args;
		}
	}
}

/**
 * Callback from the media-index shortcode.
 *
 * Performs a check, then instantiates the MediaIndex class
 * and returns the media list as HTML.
 *
 * @param array  $args['dir']
 * @return string  HTML as a list of images, wrapped in the article element.
 */
function media_index( $args )
{
	if ( is_array( $args ) )
	{
		$media_index = new MediaIndex();
		return $media_index -> get( $args );
	}
	else
	{
		return '<!-- Missing the image directory to process. [media-index dir=""]-->';
	}
}

/**
 * Check context (WordPress Plugin File or Directory Index File).
 *
 * The following checks to see whether or not this file (index.php) is being loaded
 * as part of the WordPress package, or not. If it is, we expect a WordPress
 * function to be available (in this case, `add_shortcode`). We then ensure there
 * is no direct access and add the shortcode hook, `media-index`. If we are not in
 * WordPress, then this file acts as an "indexing" type of file by listing all
 * of the allowed media types (currently jpg, png, mp3 and mp4) and making them
 * viewable to the end user by wrapping them in HTML and making use of a css
 * file that is expected to be found at `/0/media/theme/css/style.css`. This
 * idea was developed out of work to find a more robust method to develop out a
 * site, including that for a community. It makes use of the package found at:
 * {@link https://github.com/earth3300/ec01/wiki/}, with the entire codeset
 * available there through the same link.
 */
if( function_exists( 'add_shortcode' ) )
{
	// No direct access.
	defined('ABSPATH') || exit('No direct access.');

	//shortcode [media-index dir=""]
	add_shortcode( 'media-index', 'media_index' );
}
else
{
	/**
	 * Outside of WordPress. Instantiate directly, assuming current directory.
	 *
	 * @return string
	 */
	$media_index = new MediaIndex();
	echo $media_index -> get();
}

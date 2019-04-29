<?php 
/**
 * Quick 'n dirty script to fetch YOAST SEO (WordPress plugin) sitemaps from 
 * one or more domains.
 *
 * License: GPL 2 or higher
 *
 * Note: 
 * This script should be run from the command-line.  
 * php -f SCRIPT_FILENAME.php URLS_FILENNAME.txt
 *
 *
 * What is the purpose of this script? 
 * ===================================
 * Download Yoast SEO sitemaps from multiple websites automatically and place 
 * them in a directory structure ready for further processing. The endgoal is 
 * to use this sitemaps with Lighthouse for testing a website. 
 *
 *
 * How does it work?
 * ================
 * The script needs a text file as input. The contents of the file should consist of urls (one per line). Each url should 
 * point towards a Yoast SEO sitemap. An index of all xml sitemaps can usually be found at:
 * 
 * https://domain.ext/sitemap_index.xml
 * 
 * Choose the sitemaps of resources (posts, pages etc) you would like to test with Lighthouse and 
 * add these to the urls.txt file and run this script. 
 *
 * The script will download the sitemaps and place these in a directory for 
 * further processing. The directory structure looks like this: 
 *
 *    sitemaps/sitemap-yourwebsite.com-2019-04-29_23-59-59.xml
 *
 *  Further processing can be done with the script 'generateURLsFromSitemaps.php'  
 * 
 */

if( $argc <= 1 ) {
  die( "This scripts needs a filename with urls (one per line) pointing towards a site's sitemap(s)\n"); 
}

if( is_array( $argv ) && sizeof( $argv ) > 1 ) {
  $file = $argv[1]; 
  fetch_urls_from_file( $file );  
} else {
  echo "Not working\n";  
} 


function fetch_urls_from_file( $file ) {
  $lines = file( $file ); 
  if( is_array( $lines ) && sizeof( $lines ) > 0 ) {
    $nr = 0; 
    foreach($lines as $line) {
      $url = trim( strtolower( $line ) ); 
      if( is_valid_url( $url ) ) {
        $result = fetch_sitemap( $url, $nr );
        if( false !== $result ) {
          echo "$url sitemap downloaded here: $result\n"; 
        } else {
          echo "$url failed fetching sitemap. Sorry\n";   
        }  
      }
      $nr++;  
    } 
  } 
  return false; 
}

function fetch_sitemap( $url, $nr ) {
	$sitemap = file_get_contents( $url ); 
	if( false !== $sitemap ) {
		$hostname  = parse_url($url, PHP_URL_HOST); 
    $date 		 = date('Y-m-d_H-i-s');
    $path      = 'sitemaps' . DIRECTORY_SEPARATOR;
    if( maybe_create_directories( $path ) ) {
      $ext = '.xml'; 
      $file_name = $path . "sitemap-$hostname-$date-$nr$ext"; 
      $bytes = file_put_contents( $file_name, $sitemap ); 
      if( false !== $bytes ) {
        return $file_name;
      }
    } else {
      return false;
    }
  } else {
    // could not generate dir structure
    return false; 
  }
}

function is_valid_url( $url ) {
  $valid_url = filter_var( $url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED); 
  if( $valid_url !== false ) {
		$scheme = parse_url($url, PHP_URL_SCHEME); 
    if( 'http' === $scheme || 'https' === $scheme ) {
			return true; 
		}
  }
  return false; 
}

function maybe_create_directories( $dir ) {
  if( ! is_dir( $dir ) ) {
    return mkdir( $dir, 0777, true ); 
  }
  return true; 
}
?>  

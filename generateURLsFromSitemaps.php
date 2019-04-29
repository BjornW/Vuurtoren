<?php
/**
 * Quick 'n dirty script to process YOAST SEO (WordPress plugin) XML sitemap files  
 * and create lists of URLs in a textfile to be used with Lighthouse
 *
 * License: GPL 2 or higher
 *
 * Note: 
 * This script should be run from the command-line.  
 * php -f SCRIPT_FILENAME.php sitemaps
 *
 *
 * What is the purpose of this script? 
 * ===================================
 * Parse Yoast SEO XML sitemaps files for URLs and add these to a file for use with Lighthouse 
 * The endgoal is to use these lists of urls from sitemaps with Lighthouse for testing a website.
 *
 *
 * How does it work?
 * ================
 * The script needs a directory with XML sitemaps as input. Each file needs to have an .xml extension 
 * These can be generated with the 'fetchSitemaps.php' script. The URLs files will be placed in a directory 
 * structure like this:
 *    
 *    urls/urls-yourwebsite.com-2019-04-29_23-59-59.txt   
 * 
 * These files can be used with the 'runLighthouseOnURLS.php' script to test the supplied url with Lighthouse
 *
 *
 * 
 */
if( $argc <= 1 ) {
  die( "This scripts needs a path pointing towards a directory with xml sitemap(s) files. For example: sitemaps\n"); 
}

if( is_array( $argv ) && sizeof( $argv ) > 1 ) {
  $dir= $argv[1]; 
  fetch_sitemaps_from_directory( $dir );  
} else {
  echo "Not working\n";  
} 

function fetch_sitemaps_from_directory( $dir ) {
  $path = $dir . DIRECTORY_SEPARATOR . '*.xml'; 
  foreach ( glob( $path ) as $filename) {
    echo "Processing $filename\n"; 
    fetch_urls_from_sitemap( $filename );
  }
}

function fetch_urls_from_sitemap( $sitemap_file ) {
  $sitemap = simplexml_load_file( $sitemap_file ); 
  if( is_iterable( $sitemap->url ) ) {
    $urls = array();
    foreach( $sitemap->url as $url_node ) {
      $host = parse_url( (string) $url_node->loc, PHP_URL_HOST );
      $urls[$host][] = (string) $url_node->loc;  
    } 
    foreach( $urls as $host => $host_urls ) {
      if( is_array($host_urls) ) {  
        $unique_urls = array_unique( $host_urls );
        natsort( $unique_urls );  
        $url_lines   = implode( "\n", $unique_urls );
        $date        = date('Y-m-d_H-i-s'); 
        if( maybe_create_directories( 'urls' ) ) {
          $file_name   = "urls/urls-$host-$date.txt"; 
          $result = file_put_contents( $file_name, $url_lines, FILE_APPEND );
        }
      }      
    }
  }   
}

function maybe_create_directories( $dir ) {
  if( ! is_dir( $dir ) ) {
    return mkdir( $dir, 0777, true ); 
  }
  return true; 
} 
?>

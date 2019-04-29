<?php 
/**
 * Quick 'n dirty script to use Lighthouse on URLs in files  
 * and create reports of those URLS
 *
 * License: GPL 2 or higher
 *
 * Note: 
 * This script should be run from the command-line.  
 * php -f SCRIPT_FILENAME.php urls
 *
 *
 * What is the purpose of this script? 
 * ===================================
 * Use URLs with Lighthouse for testing those urls. Use 'fetchSitemaps.php' to fetch YOAST SEO (WordPress plugin) 
 * XML sitemaps and process these into files with urls (one url per line) using 'generateURLsFromSitemaps.php'.   
 * Or create your own files with urls, as long as you use one url per line this should work fine. 
 * The endgoal is to use these lists of urls with Lighthouse and generate reports to improve one or more sites. 
 *
 *
 * How does it work?
 * ================
 * The script needs a directory with one or more files. Each file needs to have an .txt extension 
 * These can be generated with the 'fetchSitemaps.php' and 'generateURLsFromSitemaps.php' script. 
 *
 * The URLs files will be read and each url will be tested with Lighthouse with the parameters set in this script. 
 * By default a json and html report will be created and placed in a directory similar to this: 
 *
 *    reports/report-yourwebsite.com-2019-04-29_23-59-59-_a-page-title.report.json
 *    reports/report-yourwebsite.com-2019-04-29_23-59-59-_a-page-title.report.html
 */

if( $argc <= 1 ) {
  die( "This scripts needs a path pointing towards a directory with urls files (one url per line). For example: urls\n"); 
}

if( is_array( $argv ) && sizeof( $argv ) > 1 ) {
  $dir= $argv[1]; 
  fetch_urls_from_directory( $dir );  
} else {
  echo "Not working\n";  
} 

function fetch_urls_from_directory( $dir ) {
  $path = $dir . DIRECTORY_SEPARATOR . '*.txt'; 
  foreach ( glob( $path ) as $filename) {
    echo "Run Lighthouse on $filename\n"; 
    run_lighthouse_on_urls( $filename );
  }
}


function run_lighthouse_on_urls( $filename ) {
  $lines = file( $filename ); 
  $nr = 0;
  $total_urls = sizeof($lines); 
  foreach( $lines as $url ) {
    $url  = trim($url);
    $host = parse_url( $url, PHP_URL_HOST );
    $path = parse_url( $url, PHP_URL_PATH );
    $path = str_ireplace( '/', '_', $path);
    if( strlen($path) > 100 ) {
      $path = substr( $path, 0, 100 ); // limit path to 100 chars
    }
    $date   = date('Y-m-d_H-i-s'); 
    $report = "reports/report-$host-$date-$path-$nr";
    if( maybe_create_directories( 'reports' ) ) {
      $cmd = sprintf( "lighthouse %s --output=json --output=html --output-path=%s --chrome-flags='--headless' --emulated-form-factor=desktop --quiet", $url, $report); 
      echo "Run test on $url "; 
      exec( $cmd );
      $total_urls--; 
      echo "Done! Only $total_urls URLs for $host to go\n";   
    }
    $nr++; 
  }
}

function maybe_create_directories( $dir ) {
  if( ! is_dir( $dir ) ) {
    return mkdir( $dir, 0777, true ); 
  }
  return true; 
} 
?>

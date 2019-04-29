Vuurtoren is a set of quick 'n dirty PHP scripts to assist in testing websites with [Lighthouse](https://developers.google.com/web/tools/lighthouse). 

_Keep in mind these scripts were developed for my own purpose. I only share these scripts so others (including myself) may learn from it. 
Pull requests and issues are welcomed but do not assume these scripts will be maintained._ 

## Installation and use ##
0. Install PHP CLI (at least 7.2.17) 
1. Install CLI version of Lighthouse using this [guide](https://developers.google.com/web/tools/lighthouse/#cli).   
2. Git clone this repository 
3. Read the scripts: 'fetchSitemaps.php', 'generateURLsFromSitemaps.php' and 'runLighthouseOnURLS.php'. The last one contains the Lighthouse parameters, feel free to adjust to your own liking. 
4. Edit the sitemaps_urls.txt file with URLs pointing towards XML sitemaps 
5. Open a terminal and run: `php -f fetchSitemaps.php sitemaps_urls.txt`
6. Check if the sitemaps where downloaded correctly in sitemaps
7. In the terminal run: `php -f generateURLsFromSitemaps.php sitemaps` to generate files with urls for Lighthouse 
8. Check if the files are created correctly in urls 
9. In the terminal run: `time php -f runLighthouseOnURLS.php` (time is an optional command-line utility showing how long the script took) 
10. Enjoy doing something else while Lighthouse tests your website(s) 


### Versions used ###
- Ubuntu 18.04.2 LTS
- NPM 6.4.1 (Nodejs 10.15.3 deb from Nodesource, Ubuntu 18.04.2 LTS was older if I remember correctly) 
- Lighthouse 4.1.0
- PHP 7.2.17 

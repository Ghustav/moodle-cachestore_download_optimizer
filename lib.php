<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * plugin functions are placed here.
 *
 * @package     cachestore_download_optimizer
 * @copyright   2020 Gustavo Mejía <bfmvtm@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/config.php');

function get_recommendations() {
	$url = 'https://obscure-lake-39056.herokuapp.com/api/send-recommendations-aux';
    $ch = curl_init($url);

   	curl_setopt($ch, CURLOPT_HTTPGET, 1);
   	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

   	$response = json_decode(curl_exec($ch));
    $recommendations = $response->recommendations;

   	curl_close($ch);   

    if ($response->success) {
        return $recommendations;
    } else {
        return 'There was an error retrieving recommendations';
    }
}

function send_metrics() {
	$metrics = array(
        'success' => 15,
        'fail' => 2
    );

    $payload = json_encode(array('metrics' => $metrics));

    $url = 'https://obscure-lake-39056.herokuapp.com/api/get-metrics';
    $ch = curl_init($url);

    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = json_decode(curl_exec($ch));

    curl_close($ch);
}

function clear_cache($recommendations) {
    $redis = new Redis();
    $redis->connect('127.0.0.1', '6379');

    $allkeys = $redis->keys('*');

    $unnecessarykeys = array_diff($allkeys, $recommendations);

    if ($redis->del($unnecessarykeys) == count($unnecessarykeys)) {
        echo "All keys cleaned.\xA";
        return true;
    }

    echo "There was a problem removing unnecessary files from cache.\xA";
    return false;
}

function retrieve_files($recommendations) {
    global $DB;

    $redis = new Redis();
    $redis->connect('127.0.0.1', '6379');

    $info = $redis->info("MEMORY");

    $usedmemory = $info[used_memory];
    $memorylimit = get_cache_limit();

    var_dump($memorylimit);
    var_dump($usedmemory);

    for ($i=0; $i < count($recommendations); $i++) { 
        $id = $recommendations[$i];

        // Retrieve the file from the Files API.
        $fs = get_file_storage();
        $file = $fs->get_file_by_id($id);
        $filesize = $file->get_filesize();

        var_dump($filesize);

        if (($usedmemory+$filesize) > $memorylimit) 
            continue;
    
        if (!$file) {
            echo "File with id ".$id." not found.\xA"; // The file does not exist.
        } else {
            $contents = $file->get_content();

            if (!redis_save_file($id, $contents)) {
                echo "There was a problem saving file ".$id." in Redis.\xA";
            } else {
                $usedmemory += $filesize;
                var_dump($usedmemory);
                echo "File saved in Redis successfully.\xA";
            }
        } 
    }
}

function serve_file_from_cache(){
    $redis = new Redis();
    $redis->connect('127.0.0.1', '6379');

    $str = "Some pseudo-random
    text spanning
    multiple lines";

    header('Content-Disposition: attachment; filename="sample.txt"');
    header('Content-Type: application/force-download');
    header('Content-Length: ' . strlen($str));
    header('Connection: close');


    echo $str;
}

function redis_save_file($id, $file) {
    $redis = new Redis();

    if ($redis->connect('127.0.0.1', '6379')) {

        if ($redis->exists($id)) {
            echo "File ".$id." is already cached.\xA";
            return false;
        } else {

            if (!$redis->set($id, $file)) {
                return false;
            }

            return true;
        }       
    } else {
        echo "Can't connect with Redis.\xA";

        return false;
    }
}

function get_cache_limit() {
    $fh = fopen('/proc/meminfo','r');
    $mem = 0;
    while ($line = fgets($fh)) {
        $pieces = array();
        if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
            $mem = $pieces[1];
            break;
        }
    }
    fclose($fh);

    return ($mem*0.3333)*1024;
}
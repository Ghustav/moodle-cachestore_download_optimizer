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

//require(__DIR__.'/../../../config.php');

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/config.php');

function get_recommendations() {
	$url = 'https://obscure-lake-39056.herokuapp.com/api/send-recommendations';
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

function retrieve_files($recommendations){
    global $DB;

    for ($i=0; $i < count($recommendations); $i++) { 
        $id = $recommendations[$i];
        $fileinfo = $DB->get_record('files', ['id' => $id]);

        var_dump($fileinfo);
    }
}

function redis_test($rserver, $rport) {
  $redis = new Redis();

  if ($redis->connect($rserver, $rport)) {
    $status = $redis->set('testing_file', file_get_contents("/home/yoda/Documentos/anteproyecto_Gustavo.pdf"));

	var_dump($status);
    // $value = $redis->get('test');

    // echo $value;
  } else {
    echo "Can't connect with Redis";
  }
}

function test() {
    $url = 'https://obscure-lake-39056.herokuapp.com/api/new-type';
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = json_decode(curl_exec($ch));
    curl_close($ch);

    var_dump($response);

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

    echo "\xA";
    var_dump($response);
    echo "\xA";
    echo 'Success: ' . $response->success;
    echo "\xA";
    echo 'metrics-success: ' . $response->metrics->success;
    echo "\xA";
    echo 'metrics-fail: ' . $response->metrics->fail;
    echo "\xA";

    $url = 'https://obscure-lake-39056.herokuapp.com/api/send-recommendations';
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_HTTPGET, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = json_decode(curl_exec($ch));
    $recommendations = $response->recommendations;

    curl_close($ch);   

    if ($response->success) {
        for ($i=0; $i < count($recommendations); $i++) { 
            echo $i . ' = ' . $recommendations[$i];
            echo "\xA";
        }

        var_dump($response);
        echo "\xA";
        echo 'Success: ' . $response->success;
        echo "\xA";
    } else {
        return 'There was an error retrieving recommendations';
    }
}

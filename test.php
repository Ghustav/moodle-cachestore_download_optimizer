<?php

$redis = new Redis();
$redis->connect('127.0.0.1', '6379');

var_dump($redis->exists(33));
var_dump($redis->exists('37'));

/* * *	
$redis = new Redis();

$redis->connect('127.0.0.1', '6379');

$allKeys = $redis->keys('*');

$array = [37, 33, 23, 13, 22];

$final = array_diff($allKeys, $array);

var_dump($final);
var_dump($allKeys);
var_dump($array);

// var_dump($redis->exists('1'));
// var_dump($redis->exists('2'));

function redis_test($rserver, $rport) {
	$redis = new Redis();

  	if ($redis->connect($rserver, $rport)) {
  		$status = $redis->set('testing_file', file_get_contents("/home/gustavo/Documents/propuesta_oct_2019.pdf"));

    	$content = $redis->get('testing_file');

		file_put_contents('/home/gustavo/Documents/test.pdf', $content);

		echo "Success.\xA";
  	} else {
    	echo "Can't connect with Redis.\xA";
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
* * */
?>

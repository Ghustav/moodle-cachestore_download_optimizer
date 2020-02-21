<?php

include('lib.php');

    get_recommendations();
    send_metrics();
    redis_test('127.0.0.1', '6379');
?>

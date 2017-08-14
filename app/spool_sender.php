<?php

$data = array();
$ret = array();
exec("./console swiftmailer:spool:send", $data, $ret);
print_r($data);
print_r($ret);
?>
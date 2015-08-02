<?php

$result = array();
if( empty( $_POST ) ) {
	$result['error']   = true;
	$result['message'] = 'No data found.';
} else {
	$result = $_POST;
}
echo json_encode( $result );
exit;

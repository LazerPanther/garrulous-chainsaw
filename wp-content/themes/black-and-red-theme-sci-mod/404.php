<?php
//created by AskApache.com for http://www.askapache.com/seo/404-google-wordpress-plugin.html


$ASKAPACHE_STATUS_CODES = array(
'400'=>array('Bad Request',  'Your browser sent a request that this server could not understand.'),
'401'=>array('Authorization Required', 'This server could not verify that you are authorized to access the document requested. Either you supplied the wrong credentials (e.g., bad password), or your browser doesn\'t understand how to supply the credentials required.'),
'402'=>array('Payment Required', 'INTERROR'),
'403'=>array('Forbidden',  'You don\'t have permission to access THEREQUESTURI on this server.'),
'404'=>array('Not Found',  'We couldn\'t find <acronym title="THEREQUESTURI">that uri</acronym> on our server, though it\'s most certainly not your fault.'),
'405'=>array('Method Not Allowed', 'The requested method THEREQMETH is not allowed for the URL THEREQUESTURI.'),
'406'=>array('Not Acceptable',  'An appropriate representation of the requested resource THEREQUESTURI could not be found on this server.'),
'407'=>array('Proxy Authentication Required', 'This server could not verify that you are authorized to access the document requested. Either you supplied the wrong credentials (e.g., bad password), or your browser doesn\'t understand how to supply the credentials required.'),
'408'=>array('Request Time-out', 'Server timeout waiting for the HTTP request from the client.'),
'409'=>array('Conflict',  'INTERROR'),
'410'=>array('Gone', 'The requested resourceTHEREQUESTURIis no longer available on this server and there is no forwarding address. Please remove all references to this resource.'),
'411'=>array('Length Required', 'A request of the requested method GET requires a valid Content-length.'),
'412'=>array('Precondition Failed', 'The precondition on the request for the URL THEREQUESTURI evaluated to false.'),
'413'=>array('Request Entity Too Large',  'The requested resource THEREQUESTURI does not allow request data with GET requests, or the amount of data provided in the request exceeds the capacity limit.'),
'414'=>array('Request-URI Too Large', 'The requested URL\'s length exceeds the capacity limit for this server.'),
'415'=>array('Unsupported Media Type', 'The supplied request data is not in a format acceptable for processing by this resource.'),
'416'=>array('Requested Range Not Satisfiable', ''),
'417'=>array('Expectation Failed', 'The expectation given in the Expect request-header field could not be met by this server. The client sent <code>Expect:</code>'),
'422'=>array('Unprocessable Entity', 'The server understands the media type of the request entity, but was unable to process the contained instructions.'),
'423'=>array('Locked', 'The requested resource is currently locked. The lock must be released or proper identification given before the method can be applied.'),
'424'=>array('Failed Dependency', 'The method could not be performed on the resource because the requested action depended on another action and that other action failed.'),
'425'=>array('No code',  'INTERROR'),
'426'=>array('Upgrade Required', 'The requested resource can only be retrieved using SSL. The server is willing to upgrade the current connection to SSL, but your client doesn\'t support it. Either upgrade your client, or try requesting the page using https://'),
'500'=>array('Internal Server Error', 'INTERROR'),
'501'=>array('Method Not Implemented', 'GET to THEREQUESTURI not supported.'),
'502'=>array('Bad Gateway',  'The proxy server received an invalid response from an upstream server.'),
'503'=>array('Service Temporarily Unavailable', 'The server is temporarily unable to service your request due to maintenance downtime or capacity problems. Please try again later.'),
'504'=>array('Gateway Time-out', 'The proxy server did not receive a timely response from the upstream server.'),
'505'=>array('HTTP Version Not Supported',  'INTERROR'),
'506'=>array('Variant Also Negotiates',  'A variant for the requested resource <code>THEREQUESTURI</code> is itself a negotiable resource. This indicates a configuration error.'),
'507'=>array('Insufficient Storage', 'The method could not be performed on the resource because the server is unable to store the representation needed to successfully complete the request. There is insufficient free space left in your storage allocation.'),
'510'=>array('Not Extended',  'A mandatory extension policy in the request is not accepted by the server for this resource.')
);



function aa_print_html ($AA_C){
	global $AA_REQUEST_METHOD, $AA_REASON_PHRASE, $AA_MESSAGE;
	if($AA_C == '400'||$AA_C == '403'||$AA_C == '405'||$AA_C[0] == '5'){
		@header("Connection: close",1);
		if($AA_C=='405')@header('Allow: GET,HEAD,POST,OPTIONS,TRACE');
		echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n<html><head>";
		echo "<title>$AA_C $AA_REASON_PHRASE</title>";
		echo "<h1>$AA_REASON_PHRASE</h1>\n<p>$AA_MESSAGE<br />\n</p>\n</body></html>";
		return true;
	} else	return false;
}
if(!isset($_REQUEST['error'])) $AA_STATUS_CODE = '404';
else $AA_STATUS_CODE = $_REQUEST['error'];
if(isset($_SERVER['REDIRECT_STATUS']) && $_SERVER['REDIRECT_STATUS']!='200') $AA_STATUS_CODE = $_SERVER['REDIRECT_STATUS'];
$AA_REQUEST_METHOD	= $_SERVER['REQUEST_METHOD'];
$AA_THE_REQUEST		= htmlentities(strip_tags($_SERVER['REQUEST_URI']));
$AA_REASON_PHRASE 	= $ASKAPACHE_STATUS_CODES[$AA_STATUS_CODE][0];
$AA_M_SR=array(array('INTERROR','THEREQUESTURI','THEREQMETH'),array('The server encountered an internal error or misconfiguration and was unable to complete your request.',$AA_THE_REQUEST,$AA_REQUEST_METHOD));
$AA_MESSAGE 		= str_replace($AA_M_SR[0],$AA_M_SR[1],$ASKAPACHE_STATUS_CODES[$AA_STATUS_CODE][1]);
ob_start();
@header("HTTP/1.1 $AA_STATUS_CODE $AA_REASON_PHRASE",1);
@header("Status: $AA_STATUS_CODE $AA_REASON_PHRASE",1);

if(!aa_print_html($AA_STATUS_CODE)){
?>
<?php get_header();?>

<div id="content">
	<div class="top"><div class="bottom">
	<div class="left"><div class="lpadding">
		<?php get_sidebar(); ?>
	</div></div>
	<div class="right">
		
		<div class="post_block1"><div class="post_block2"><div class="post_block3"><div class="post_block4"><div class="post">
			<h1><?php _e("$AA_STATUS_CODE $AA_REASON_PHRASE"); ?></h1>
			<?php // if(function_exists('aa_google_404')) aa_google_404(); ?>
			<p>Click on the [ home ] link, above, to return to our site.</p>
			</div>
			<div class="clear"></div>
		</div></div></div></div>

		<div class="div1"></div>	

	</div>
	<div class="clear"></div>
	</div></div>
</div>

<?php get_footer(); ?>
<?php }
exit;
exit();
?>
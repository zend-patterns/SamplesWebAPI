<?php

/*
 * Important note about applicationDeploy
 * The applicationDeploy Web API action restarts your webserver as part of the deployment process.
 * This means that this script will be terminated "out of the blue" and for no apparent reason.
 * 
 * This script does still give you a basic polling example, it just does not live out its intended use-case.
 * A better implementation would incorporate Javascript and fault tolerant Ajax calls to an API which may or may not be answering.
 */

use WebAPI\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(dirname(__DIR__)));

// Setup autoloading
require 'init_autoloader.php';
require 'vendor/json_lint.php';
require 'vendor/get_in.php';
require 'module/Application/src/WebAPI/SignatureGenerator.php';
require 'module/Application/src/WebAPI/Http/Client.php';

set_time_limit(240);

$name = isset($_GET['name']) ? $_GET['name'] : '';
$key = isset($_GET['key']) ? $_GET['key'] : '';

$output = isset($_GET['output']) ? $_GET['output'] : 'xml';
$version = isset($_GET['version']) ? $_GET['version'] : '1.7';

$client = new Client(
		'http://localhost:10081/ZendServer/Api/applicationDeploy',
		array(
			'key' => $key,
			'keyName' => $name,
			'output' => $output,
			'version' => $version,
		));

$client->setFileUpload('data/sanity.zpk', 'appPackage');
$client->setMethod('POST');

$client->getRequest()->getPost()->fromArray(array(
	'baseUrl' => 'http://localhost/test',
	'defaultServer' => 'TRUE',
));

$client->send();
$response = $client->getResponse()->getBody();

$config = array(
		'indent'         => true,
		'input-xml'   => true,
		'output-xml'   => true,
		'escape-cdata' => true,
		'wrap'           => 400);


$tidy = new tidy;
$tidy->parseString($response, $config, 'utf8');
$tidy->cleanRepair();

$request = Request::fromString($client->getLastRawRequest());

?>
<h2>Example output</h2>
<h3>Synopsis:</h3>
<p>http://localhost/samples/examples/applicationDeploy.php?key=&lt;key&gt;&amp;name=&lt;key-name&gt;[&amp;version=&lt;version-number&gt;][&amp;output=&lt;json|xml&gt;]</p>
<h3>WebAPI call parameters:</h3>
<pre><code><?php echo htmlentities(print_r($_GET,true)) ?></code></pre>
<h3>URI called:</h3>
<pre><code><?php echo htmlentities($request->getUriString()) ?></code></pre>
<h3>Headers sent:</h3>
<pre><code><?php echo htmlentities(print_r($request->getHeaders()->toArray(),true)) ?></code></pre>
<h3>Response:</h3>
<pre><code><?php echo htmlentities($tidy) ?></code></pre>

<hr />
<?php 

if ($client->getResponse()->isServerError() || $client->getResponse()->isClientError()) {
	die('No polling needed');
}

$response = $client->getResponse()->getBody();
if ($output == 'json') {
	$responseDecoded = Json::decode($response, Json::TYPE_ARRAY);
	$appId = \igorw\get_in($responseDecoded, ['responseData', 'applicationInfo', 'id']);
} else {
	preg_match('#\<applicationInfo\>\s+\<id\>([0-9]+)\</id\>#m', $response, $matches);
	$appId = $matches[1];
}

if (!$appId) {
	die('No appId, no polling needed');
}

?>
<h2>Start Polling on /ZendServer/Api/applicationGetStatus</h2>
<?php 
$pollingClient = new Client(
		'http://localhost:10081/ZendServer/Api/applicationGetStatus',
		array(
				'key' => $key,
				'keyName' => $name,
				'output' => 'json',
				'version' => $version,
		));
$pollingClient->getRequest()->getQuery()->fromArray(array(
	'applications' => array($appId)
));


$continue = true;
$i = 1;

while ($continue) {
	echo "<h3>Response for request $i</h3>";

	$pollingClient->send();
	$response = $pollingClient->getResponse()->getBody();
	/// accept 200 or 202
	if ($pollingClient->getResponse()->isOk() || $pollingClient->getResponse()->isInformational()) {
		$responseDecoded = Json::decode($response, Json::TYPE_ARRAY); /* @var $responseDecoded stdClass */

		$appInfo = current(\igorw\get_in($responseDecoded, ['responseData', 'applicationsList']));
		$status = \igorw\get_in($appInfo, ['status']);

		echo "<h4>Status is $status</h4>".PHP_EOL;
		
		if (! in_array($status, array('staging', 'activating'))) {
			$continue = false;
		}
	
	} else {
		$continue = false;
	}

	$displayResponse = htmlentities(indent($response));
	echo "<pre><code>$displayResponse</code></pre>";

	$i++; flush(); sleep(1);
}

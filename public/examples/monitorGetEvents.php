<?php
use WebAPI\Http\Client;
use Zend\Http\Request;
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(dirname(__DIR__)));

// Setup autoloading
require 'init_autoloader.php';
require 'vendor/json_lint.php';
require 'module/Application/src/WebAPI/SignatureGenerator.php';
require 'module/Application/src/WebAPI/Http/Client.php';

$requestEnvironment = new \Zend\Http\PhpEnvironment\Request();

$name = isset($_GET['name']) ? $_GET['name'] : '';
$key = isset($_GET['key']) ? $_GET['key'] : '';

$output = isset($_GET['output']) ? $_GET['output'] : 'xml';
$version = isset($_GET['version']) ? $_GET['version'] : '1.7';

$issueId = isset($_GET['issueId']) ? $_GET['issueId'] : '';

if ((! $issueId) && ($key)) {
	/// if no issueId was provided, attempt to find one on our own
	$client = new Client(
			'http://localhost:10081/ZendServer/Api/monitorGetIssuesByPredefinedFilter?filterId=All%20Issues&limit=1',
			array(
					'key' => $key,
					'keyName' => $name,
					'output' => 'json'
			));
	$response = json_decode($client->send()->getBody(), true);
	
	if (is_null($response)) {
		echo 'Error: Output for monitorGetIssuesByPredefinedFilter is not a json';
	}
	
	if (isset($response['errorData'])) {
		echo "Error: {$response['errorData']['errorCode']} {$response['errorData']['errorMessage']}";
		exit; 
	} else {
		$issues = $response['responseData']['issues'];
		if (0 == count($issues)) {
			echo "Error: No events found on the system.";
			exit;
		}
		
		$issue = current($response['responseData']['issues']);
		$issueId = $issue['id'];
	}
}

/// proceed to retrieve the actual IssueId
$client = new Client(
		'http://localhost:10081/ZendServer/Api/monitorGetIssueDetails',
		array(
			'key' => $key,
			'keyName' => $name,
			'output' => $output,
			'version' => $version,
		));
$client->getRequest()->getQuery()->fromArray(array('issueId' => $issueId, 'limit' => 3));

$response = $client->send()->getBody();

$config = array(
		'indent'         => true,
		'input-xml'   => true,
		'output-xml'   => true,
		'escape-cdata' => true,
		'wrap'           => 400);

if ($output == 'xml') {
	$tidy = new tidy;
	$tidy->parseString($response, $config, 'utf8');
	$tidy->cleanRepair();
} else {
	$tidy = indent($response);
}

$request = Request::fromString($client->getLastRawRequest());
?>
<h2>Example output</h2>
<h3>Synopsis:</h3>
<p><?php echo preg_replace('#(\?.+)#', '', $requestEnvironment->getUriString()) ?>?key=&lt;key&gt;&amp;name=&lt;key-name&gt;[&amp;version=&lt;version-number&gt;][&amp;output=&lt;json|xml&gt;][&amp;issueId=&lt;issue Id to view&gt;]</p>
<h3>WebAPI call parameters:</h3>
<pre><code><?php echo htmlentities(print_r($_GET,true)) ?></code></pre>
<h3>URI called:</h3>
<pre><code><?php echo htmlentities($request->getUriString()) ?></code></pre>
<h3>Headers sent:</h3>
<pre><code><?php echo htmlentities(print_r($request->getHeaders()->toArray(),true)) ?></code></pre>
<h3>Response:</h3>
<pre><code><?php echo htmlentities($tidy) ?></code></pre>
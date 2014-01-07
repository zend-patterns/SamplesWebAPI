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
require 'module/Application/src/WebAPI/SignatureGenerator.php';
require 'module/Application/src/WebAPI/Http/Client.php';

$name = isset($_GET['name']) ? $_GET['name'] : '';
$key = isset($_GET['key']) ? $_GET['key'] : '';

$output = isset($_GET['output']) ? $_GET['output'] : 'xml';
$version = isset($_GET['version']) ? $_GET['version'] : '1.7';

$client = new Client(
		'http://localhost:10081/ZendServer/Api/tasksComplete',
		array(
			'key' => $key,
			'keyName' => $name,
			'output' => $output,
			'version' => $version,
		));

$response = $client->send()->getBody();

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
<p>http://localhost/samples/examples/applicationGetStatus.php?key=&lt;key&gt;&amp;name=&lt;key-name&gt;[&amp;version=&lt;version-number&gt;][&amp;output=&lt;json|xml&gt;]</p>
<h3>WebAPI call parameters:</h3>
<pre><code><?php echo htmlentities(print_r($_GET,true)) ?></code></pre>
<h3>URI called:</h3>
<pre><code><?php echo htmlentities($request->getUriString()) ?></code></pre>
<h3>Headers sent:</h3>
<pre><code><?php echo htmlentities(print_r($request->getHeaders()->toArray(),true)) ?></code></pre>
<h3>Response:</h3>
<pre><code><?php echo htmlentities($tidy) ?></code></pre>
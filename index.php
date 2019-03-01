<?php
// set up frontend
$frontend = <<<EOT
<!DOCTYPE html>
<html>
<body>
<form action="/" method="POST">
  Choose search engine:<br>
	<select name="search_engine">
		<option value="Yandex">Yandex</option>
		<option value="Google">Google</option>
	</select>
  <br>
  Enter request:<br>
  <input type="text" name="request">
  <br><br>
  <input type="submit" value="Submit">
</form> 
</body>
</html>
EOT;

// parsers
function parse_yandex($html) {
	$first = strpos($html, '<li');
	$second = strpos($html, '<li', $first + 1);
	$third = strpos($html, '<li', $second + 1);
	$fourth = strpos($html, '<li', $third + 1);
	$fifth = strpos($html, '<li', $fourth + 1);
	$sixth = strpos($html, '<li', $fifth + 1);
  // $html = substr($html, $first, $sixth - $first);
	return $html;
}
function parse_google($html) {
  $first = strpos($html, '<div class="g">');
	$second = strpos($html, '<div class="g">', $first + 1);
	$third = strpos($html, '<div class="g">', $second + 1);
	$fourth = strpos($html, '<div class="g">', $third + 1);
	$fifth = strpos($html, '<div class="g">', $fourth + 1);
	$sixth = strpos($html, '<div class="g">', $fifth + 1);
  $html = substr($html, $first, $sixth - $first);
	// fix links
	$html = str_replace('href="/url?q=', 'href=', $html);
  while ($hash_start = strpos($html, '&amp;sa=')) {
  	$hash_end = strpos($html, '">', $hash_start);
    $hash = substr($html, $hash_start, $hash_end - $hash_start + 1);
	  $html = str_replace($hash, '', $html);
  }
  
	return $html;
}

// request to search engine
function request($engine, $request) {
  $request = urlencode($request);
	if ($engine == 'Yandex') return parse_yandex(file_get_contents('https://yandex.ru/search/?text=' . $request));
  if ($engine == 'Google') return parse_google(file_get_contents('https://www.google.com/search?&q=' . $request));
}

// server route handling
switch($_SERVER["REQUEST_URI"]) {
  case "/":
	  if($_SERVER['REQUEST_METHOD'] == 'GET') echo $frontend;
    if($_SERVER['REQUEST_METHOD'] == 'POST') echo request($_POST['search_engine'], $_POST['request']);
		break;
}
?>

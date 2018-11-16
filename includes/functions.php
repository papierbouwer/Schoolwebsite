<?php
include_once 'psl-config.php';
 
function sec_session_start() {
    $session_name = 'sec_session_id';   // Set a custom session name
    $secure = SECURE;
    // This stops JavaScript being able to access the session id.
    $httponly = true;
    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }
    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly);
    // Sets the session name to the one set above.
    session_name($session_name);
    session_start();            // Start the PHP session 
    session_regenerate_id();    // regenerated the session, delete the old one. 
}

function login($email, $password, $mysqli) {
    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $mysqli->prepare("SELECT id, username, password, salt 
        FROM members
       WHERE email = ?
        LIMIT 1")) {
        $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();
 
        // get variables from result.
        $stmt->bind_result($user_id, $username, $db_password, $salt);
        $stmt->fetch();
 
        // hash the password with the unique salt.
        $password = hash('sha512', $password . $salt);
        if ($stmt->num_rows == 1) {
            // If the user exists we check if the account is locked
            // from too many login attempts 
 
            if (checkbrute($user_id, $mysqli) == true) {
                // Account is locked 
                // Send an email to user saying their account is locked
                return false;
            } else {
                // Check if the password in the database matches
                // the password the user submitted.
                if ($db_password == $password) {
                    // Password is correct!
                    // Get the user-agent string of the user.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS protection as we might print this value
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;
                    // XSS protection as we might print this value
                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/", 
                                                                "", 
                                                                $username);
                    $_SESSION['username'] = $username;
                    $_SESSION['login_string'] = hash('sha512', 
                              $password . $user_browser);
                    // Login successful.
                    return true;
                } else {
                    // Password is not correct
                    // We record this attempt in the database
                    $now = time();
                    $mysqli->query("INSERT INTO login_attempts(user_id, time)
                                    VALUES ('$user_id', '$now')");
                    return false;
                }
            }
        } else {
            // No user exists.
            return false;
        }
    }
}

function checkbrute($user_id, $mysqli) {
    // Get timestamp of current time 
    $now = time();
 
    // All login attempts are counted from the past 2 hours. 
    $valid_attempts = $now - (2 * 60 * 60);
 
    if ($stmt = $mysqli->prepare("SELECT time 
                             FROM login_attempts 
                             WHERE user_id = ? 
                            AND time > '$valid_attempts'")) {
        $stmt->bind_param('i', $user_id);
 
        // Execute the prepared query. 
        $stmt->execute();
        $stmt->store_result();
 
        // If there have been more than 5 failed logins 
        if ($stmt->num_rows > 5) {
            return true;
        } else {
            return false;
        }
    }
}

function login_check($mysqli) {
    // Check if all session variables are set 
    if (isset($_SESSION['user_id'], 
                        $_SESSION['username'], 
                        $_SESSION['login_string'])) {
 
        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];
 
        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
 
        if ($stmt = $mysqli->prepare("SELECT password 
                                      FROM members 
                                      WHERE id = ? LIMIT 1")) {
            // Bind "$user_id" to parameter. 
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   // Execute the prepared query.
            $stmt->store_result();
 
            if ($stmt->num_rows == 1) {
                // If the user exists get variables from result.
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);
 
                if ($login_check == $login_string) {
                    // Logged In!!!! 
                    return true;
                } else {
                    // Not logged in 
                    return false;
                }
            } else {
                // Not logged in 
                return false;
            }
        } else {
            // Not logged in 
            return false;
        }
    } else {
        // Not logged in 
        return false;
    }
}

function esc_url($url) {
 
    if ('' == $url) {
        return $url;
    }
 
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
 
    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;
 
    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }
 
    $url = str_replace(';//', '://', $url);
 
    $url = htmlentities($url);
 
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);
 
    if ($url[0] !== '/') {
        // We're only interested in relative links from $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}

function bbcode($text){
	$text = nl2br($text);
	$text = preg_replace('#\[!--(.+)--\]#isU', '', $text);
	$text = preg_replace('#\[abbr=(.+)\](.+)\[/abbr\]#isU', '<abbr title="$1">$2</abbr>', $text);
	$text = preg_replace('#\[acronym=(.+)\](.+)\[/acronym\]#isU', '<abbr title="$1">$2</abbr>', $text);
	$text = preg_replace('#\[b\](.+)\[/b\]#isU', '<b>$1</b>', $text);
	$text = preg_replace('#\[bdo=rtl\](.+)\[/bdo\]#isU', '<span class="rtl">$1</span>', $text);
	$text = preg_replace('#\[bdo=ltr\](.+)\[/bdo\]#isU', '<span class="ltr">$1</span>', $text);
	$text = preg_replace('#\[br\]#isU', '<br>', $text);
	$text = preg_replace('#\[center\](.+)\[/center\]#isU', '<span class="center">$1</span>', $text);
	$text = preg_replace('#\[code\](.+)\[/code\]#isU', '</p><pre><code>$1</code></pre><p>', $text);
	$text = preg_replace('#\[email\](.+)\[/email\]#isU', '<a href="mailto:$1">mailto:$1</a>', $text);
	$text = preg_replace('#\[email=(.+)\](.+)\[/email\]#isU', '<a href="mailto:$1">$2</a>', $text);
	$text = preg_replace('#\[ftp\](.+)\[/ftp\]#isU', '<a href="ftp://$1">ftp://$1</a>', $text);
	$text = preg_replace('#\[ftp=(.+)\](.+)\[/ftp\]#isU', '<a href="ftp://$1">$2</a>', $text);
	$text = preg_replace('#\[height=(.+)\](.+)\[/height\]#isU', '<span height=$1>$2</span>', $text);
	$text = preg_replace('#\[hr\]#isU', '<hr></hr>', $text);
	$text = preg_replace('#\[i\](.+)\[/i\]#isU', '<i>$1</i>', $text);
	$text = preg_replace('#\[iframe src=(.+) width=(.+) height=(.+) seamless\]#isU', '<iframe src="$1" width="$2" height="$3" class="noborder"></iframe>', $text);
	$text = preg_replace('#\[iframe src=(.+) width=(.+) height=(.+)\]#isU', '<iframe src="$1" width="$2" height="$3"></iframe>', $text);
	$text = preg_replace('#\[iframe src=(.+)\]#isU', '<iframe src="$1"></iframe>', $text);
	$text = preg_replace('#\[img\](.+)\[/img\]#isU', '<img src="$1" alt="Dit is een plaatje">', $text);
	$text = preg_replace('#\[left\](.+)\[/left\]#isU', '<span class="left">$1</span>', $text);
	$text = preg_replace('#\[li\](.+)\[/li\]#isU', '<li>$1</li>', $text);
	$text = preg_replace('#\[list\](.+)\[/list\]#isU', '<ul>$1</ul>', $text);
	$text = preg_replace('#\[list type=decimal\](.+)\[/list\]#isU', '<ol>$1</ol>', $text);
	$text = preg_replace('#\[ltr\](.+)\[/ltr\]#isU', '<span class="ltr">$1</span>', $text);
	$text = preg_replace('#\[pre\](.+)\[/pre\]#isU', '<pre>$1</pre>', $text);
	$text = preg_replace('#\[left\](.+)\[/left\]#isU', '<span class="right">$1</span>', $text);
	$text = preg_replace('#\[rtl\](.+)\[/rtl\]#isU', '<span class="rtl">$1</span>', $text);	
	$text = preg_replace('#\[s\](.+)\[/s\]#isU', '<s>$1</s>', $text);
	$text = preg_replace('#\[style size=(.+)\](.+)\[/s\]#isU', '<span class="grote$1">$2</span>', $text);
	$text = preg_replace('#\[size=(.+)\](.+)\[/size\]#isU', '<span class=grote$1>$2</span>', $text);
	$text = preg_replace('#\[source=(.+)\](.+)\[/source\]#isU', '<source scr="$1" type="video/$2">', $text);
	$text = preg_replace('#\[sub\](.+)\[/sub\]#isU', '<sub>$1</sub>', $text);
	$text = preg_replace('#\[sup\](.+)\[/sup\]#isU', '<sup>$1</sup>', $text);
	$text = preg_replace('#\[table\](.+)\[/table\]#isU', '<table class="tableline"></tbody>$1</tbody></table>', $text);
	$text = preg_replace('#\[tr\](.+)\[/tr\]#isU', '<tr>$1</tr>', $text);
	$text = preg_replace('#\[td\](.+)\[/td\]#isU', '<td>$1</td>', $text);
	$text = preg_replace('#\[u\](.+)\[/u\]#isU', '<u>$1</u>', $text);
	$text = preg_replace('#\[url\](.+)\[/url\]#isU', '<a href="$1">$1</a>', $text);
	$text = preg_replace('#\[url=(.+)\](.+)\[/url\]#isU', '<a href="$1">$2</a>', $text);
	$text = preg_replace('#\[video\](.+)\[/video\]#isU', '<video controls>$1</video>', $text);
	$text = preg_replace('#\[width=(.+)\](.+)\[/width\]#isU', '<span width=$1>$2</span>', $text);
	$text = preg_replace('#\[youtube\](.+)\[/youtube\]#isU', '<iframe width="560" height="315" src="//www.youtube.com/embed/$1" class="noborder" allowfullscreen></iframe>', $text);
	$text = preg_replace('#\[form\](.+)\[/form\]#isU', '</p><form>$1</form><p>', $text);
	$text = preg_replace('#\[textbox\]#isU', '<input type="text" name="text">', $text);
	$text = preg_replace('#\[button\]#isU', '<input onclick="alert(\'Je hebt op de knop gedrukt\');" type="button" name="button" value="Dit is een knop">', $text);
	$text = preg_replace('#\[range\]#isU', '<input type="range" name="button">', $text);
	$text = preg_replace('#\[file\]#isU', '<input type="file" name="file">', $text);
	$text = preg_replace('#\[checkbox\]#isU', '<input type="checkbox" name="check">', $text);
	$text = preg_replace('#\[radio\]#isU', '<input type="radio" name="radio">', $text);
	$text = preg_replace('#\[textarea\]#isU', '<textarea></textarea>', $text);
	$text = preg_replace('#\[option\]#isU', '<select><option value="volvo">Volvo</option><option value="saab">Saab</option><option value="mercedes">Mercedes</option><option value="audi">Audi</option></select>', $text);
	
	
	//Nog implementeren:
	//$text = preg_replace('#\[quote\](.+)\[/quote\]#isU', '', $text);
	//$text = preg_replace('#\[style color=(.+)\](.+)\[/s\]#isU', '<span class="$1">$2</span>', $text);
	//$text = preg_replace('#\[glow\])';
	//
	return $text;
}

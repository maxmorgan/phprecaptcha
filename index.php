<?
/*
 * This is a basic example of integrating against
 * Google's reCAPTCHA v2 API using PHP
 *
 * Demo: https://playground.maxmorgandesign.com/recaptcha/
 */

// Make sure you put your keys here
define('RECAPTCHA_PUB_KEY', 'YOUR-KEY');
define('RECAPTCHA_PRIV_KEY', 'YOUR-KEY');

function recaptcha_create(){
    ?>
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_PUB_KEY; ?>"></div>
    <?
}

function recaptcha_verify(){
    $url = 'https://www.google.com/recaptcha/api/siteverify';

    $data = array(
        'secret' => RECAPTCHA_PRIV_KEY,
        'response' => $_POST["g-recaptcha-response"]
    );

    $options = array(
        'http' => array(
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );

    $context = stream_context_create($options);
    $verify = file_get_contents($url, false, $context);
    $captcha_success=json_decode($verify);

    return $captcha_success->success;
}

/*
 * PHP will generally add stripslashes to all posted data. 
 * This removes it and helps to sanitize data for security reasons.
 * trim - Removes whitespace from the ends of a string. 
 *      " hello! " => "hello!
 * stripslashes - Reverses addslashes()
 * htmlentities - Converts special characters to their counterpart
 *      "<script>" => "&lt;script&gt;"
 */
function sanitize($val){
    return htmlentities(stripslashes(trim($val)), ENT_QUOTES);
}

$submitted = false;
$errors = [];
if ( isset($_POST['firstName']) ) {
    foreach ( $_POST as $k => $v ) $_POST[$k] = sanitize($v);

    if ( empty($_POST['firstName']) ) $errors[] = 'Please enter your first name!';

    if ( !recaptcha_verify() ) $errors[] = 'The captcha you filled out did not match the image. Please try again.';

    if ( count($errors) == 0 ) $submitted = true; 
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>PHP Google reCaptcha v2.0 Demo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css" />
</head>
<body>

    <div class="container">
        <header class="text-center">
            <h1>Google PHP ReCaptcha v2.0 Demo</h1>
        </header>

        <hr/>

        <? if ( $submitted == true ) { ?>

            <div class="alert alert-success">
                Captcha validated. Thank you, <?= $_POST['firstName']; ?>!
            </div>
        
        <? } else {

            if ( count($errors) > 0 ) { ?>
                <? foreach ( $errors as $error ) { ?>
                    <div class="alert alert-danger">
                        <?= $error; ?>
                    </div>
                <? }; ?>
            <? }; ?>

            <form method="post" action="" id="contact_form">
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" class="form-control" id="firstName" name="firstName" placeholder="John Doe" value="<?= $_POST['firstName']; ?>" />
                </div>
                <div class="form-group">
                    <label for="captcha">Captcha</label>
                    <? recaptcha_create(); ?>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>

        <? } ?>

        <hr/>

        <footer>
            <ul class="nav justify-content-center">
                <li class="nav-item">
                    <a class="nav-link" href="https://github.com/maxmorgan/phprecaptcha">Source</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://developers.google.com/recaptcha/docs/display">reCAPTCHA v2.0 Docs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://www.maxmorgandesign.com/contact/">Support</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://www.maxmorgandesign.com/">Credit</a>
                </li>
            </ul>
        </footer>
    </div>
</body>
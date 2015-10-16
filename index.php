<style>
    body {text-align: center; padding: 100px;}
    a {color: #6495ed; text-decoration: none;}
    a:hover {text-decoration: underline;}
    input {line-height: 1.5; font-size: larger; padding: 10px; border-radius: 15px; box-shadow: none; border: 1px solid #ddd;}
    input[type=text] {width: 500px;}
    input[type=submit] {width: auto;}
    h2 {font-size: larger; line-height: 2;}

    .rainbow {
        border-radius: 15px;
        display: inline-block;
        padding: 20px;
        border-width: 3px;

        border-image: -webkit-linear-gradient(left, #E18728, #BE4C39 33%, #9351A6 66%, #4472B9,#4CA454,#D49B00) 2%;
        border-image: -ms-linear-gradient(left, #E18728, #BE4C39 33%, #9351A6 66%, #4472B9,#4CA454,#D49B00) 2%;
        border-image: -moz-linear-gradient(left, #E18728, #BE4C39 33%, #9351A6 66%, #4472B9,#4CA454,#D49B00) 2%;
    }
</style>
<?php

$host = 'http://'. $_SERVER['HTTP_HOST'] .'/';

$dir = '';

if (!empty($_POST['dir'])) {
    $dir = $_POST['dir'];
}

?>
<form method="post">
    <input type="text" name="dir" value="<?php echo $dir;?>" placeholder="/home/support/dev/... or /mnt/data/home/...">
    <input type="submit" value="Go!">
</form>

<?php

if (!empty($dir)) {
    $pathTree = explode('/' , trim($dir, '/'));
    $pathTree = array_filter($pathTree, 'strlen');
    $c = count($pathTree) - 1;
    $prefix = $pathTree[$c - 3];
    $user = $pathTree[$c - 2];
    $devDir = $pathTree[$c - 1];
    $project = $pathTree[$c];

    $urlParts = array(
        'devDir' => $devDir,
        'user' => $user,
        'project' => $project
    );

    $scandir = scandir($dir);

    $urlPrefix = $host . implode('/', $urlParts) . '/';

    echo '<h2>';
    foreach ($scandir as $file) {
        if (preg_match('/\.gz$/', $file)) {
            $url = $urlPrefix . $file;
            echo '<a href="' . $url . '">' . $url . '</a><br>';
        }
    }
    echo '</h2>';
}
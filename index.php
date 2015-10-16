<!doctype html>
<html>
<head>
    <title>Links2Dumps</title>
    <style>
        html, body, input {font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;}
        html, body {width: 100%; height: 100%;}
        body {background: #fff; color: #232635;}
        a {color: #6495ed; text-decoration: none;}
        a:hover {text-decoration: underline;}
        input {line-height: 1.5; font-size: larger; padding: 10px; border-radius: 15px; box-shadow: none; border: 1px solid #ddd;}
        input[type=text] {width: 600px;}
        input[type=submit] {width: auto; background: none; cursor: pointer;}
        h2 {font-size: larger; line-height: 2;}
        .error {color: #BE4C39;}

        .rainbow {
            border-radius: 15px;
            display: inline-block;
            padding: 20px;
            border-width: 3px;

            border-image: -webkit-linear-gradient(left, #E18728, #BE4C39 33%, #9351A6 66%, #4472B9,#4CA454,#D49B00) 2%;
            border-image: -ms-linear-gradient(left, #E18728, #BE4C39 33%, #9351A6 66%, #4472B9,#4CA454,#D49B00) 2%;
            border-image: -moz-linear-gradient(left, #E18728, #BE4C39 33%, #9351A6 66%, #4472B9,#4CA454,#D49B00) 2%;
        }
        table.centerize, table.centerize tr, table.centerize td {
            text-align: center;
            vertical-align: middle;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
<table class="centerize">
    <tr><td>
<?php
define("DS", DIRECTORY_SEPARATOR);
$data = '';
$dir = '';
$url = '';
if (!empty($_POST['data'])) {
    $data = $_POST['data'];
}
?>
<form method="post">
    <input type="text" name="data" value="<?php echo $data;?>" placeholder="/home/support/dev/... or http://sparta....">
    <input type="submit" value="Go!">
</form>

<?php
function getDumpFiles($dir)
{
    $result = array();
    if (!is_dir($dir)) {
        return $result;
    }
    $scandir = scandir($dir);
    foreach ($scandir as $file) {
        if (preg_match('/\.gz$/', $file)) {
            $result[] = $file;
        }
    }
    return $result;
}

function dataError($error = 'Files are not exist! Please try again...')
{
    echo '<h2 class="error">' . $error . '</h2>';
    exit();
}

if (!empty($data)) {
    if (is_dir($data)) {
        $dir = $data;
        $host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        $pathTree = explode('/', trim($dir, '/'));
        $pathTree = array_filter($pathTree, 'strlen');
        $c = count($pathTree) - 1;
        if ($c < 3) {
            dataError();
        }
        $prefix = $pathTree[$c - 3];
        $user = $pathTree[$c - 2];
        $devDir = $pathTree[$c - 1];
        $project = $pathTree[$c];

        $urlParts = array(
            'devDir' => $devDir,
            'user' => $user,
            'project' => $project
        );

        $urlPrefix = $host . implode('/', $urlParts) . '/';

        echo '<h2>';
        $files = getDumpFiles($dir);
        foreach ($files as $file) {
            $url = $urlPrefix . $file;
            echo '<div><a href="' . $url . '">' . $url . '</a></div>';
        }
        echo '</h2>';
    } else if (preg_match('/^http(s){0,1}\:\/\//', $data)) {
        $url = $data;
        $url = preg_replace('/^http(s){0,1}\:\/\//', '', $url);
        $urlParts = explode('/', trim($url, '/'));
        $c = count($urlParts);
        if ($c > 2) {
            $devDir = $urlParts[1];
            $user = $urlParts[2];

            unset($urlParts[0], $urlParts[1], $urlParts[2]);

            $dirParts = array(
                'home',
                $user,
                $devDir
            );

            foreach ($urlParts as $urlPart) {
                $dirParts[] = $urlPart;
            }

            $dir = DS . implode(DS, $dirParts) . DS;
            echo '<h2>';
            $files = getDumpFiles($dir);
            if ($files) {
                foreach ($files as $file) {
                    echo '<div><a>' . $dir . $file . '</a></div>';
                }
            } else {
                dataError('There is no any dumps here: ' . $dir);
            }
            echo '</h2>';
        }
    } else {
        dataError();
    }
}
?>
    </td></tr>
</table>
</body>
</html>

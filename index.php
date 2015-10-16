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

        table.centerize, table.centerize tr, table.centerize td {
            text-align: center;
            vertical-align: middle;
            width: 100%;
            height: 100%;
        }

        .color0 {background-color: #BE4C39;}
        .color1 {background-color: #E18728;}
        .color2 {background-color: #d4be2d;}
        .color3 {background-color: #4CA454;}
        .color4 {background-color: #4472B9;}
        .color5 {background-color: #1337cb;}
        .color6 {background-color: #9351A6;}
        .rainbow {
            min-width: 100px;
            min-height: 10px;
            position: relative;
            border-radius: 15px;
            display: inline-block;
            box-sizing: border-box;
            overflow: hidden;
        }
        .rainbow .rainbow-parts {position: absolute; top:0; width: 100%; height: 100%; z-index: -1;}
        .rainbow-part {width: 14.3%; float: left; height: 100%;}
        .rainbow .rainbow-part:last-child {width: auto; float: none}
        .rainbow-content {margin: 5px; background-color: #fff; border-radius: 10px; padding: 20px;}
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
            <div class="rainbow0">
                <div class="rainbow-parts">
                    <div class="rainbow-part color0"></div>
                    <div class="rainbow-part color1"></div>
                    <div class="rainbow-part color2"></div>
                    <div class="rainbow-part color3"></div>
                    <div class="rainbow-part color4"></div>
                    <div class="rainbow-part color5"></div>
                    <div class="rainbow-part color6"></div>
                </div>
                <div class="rainbow-content">

<form method="post">
    <input type="text" name="data" value="<?php echo $data;?>" placeholder="/home/support/dev/... or http://sparta....">
    <input type="submit" value="Go!">
</form>

                </div>
            </div>

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

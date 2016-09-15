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
        input:focus {outline: none;}
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

        .color0 {background-color: #ff0000;}
        .color1 {background-color: #ff7f00;}
        .color2 {background-color: #ffff00;}
        .color3 {background-color: #05ff00;}
        .color4 {background-color: #5ac8ff;}
        .color5 {background-color: #4472B9;}
        .color6 {background-color: #ff9eb1;}
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
if (!empty($_REQUEST['data'])) {
    $data = $_REQUEST['data'];
}

$colors = array(0, 1, 2, 3, 4, 5, 6);
shuffle($colors);
?>
            <div class="rainbow">
                <div class="rainbow-parts">
                    <?php foreach ($colors as $color) { echo '<div class="rainbow-part color' . $color . '"></div>'; }?>
                </div>
                <div class="rainbow-content">

<form method="post">
    <input type="text" name="data" autofocus="autofocus" value="<?php echo $data;?>" placeholder="/home/support/dev/... or http://sparta....">
    <input type="submit" value="Go!">
</form>

                </div>
            </div>

<?php

function getAllFiles($dir)
{
    $scandir = array();
    if (!is_dir($dir)) {
        return $scandir;
    }
    $scandir = scandir($dir);
    if (!empty($scandir)) {
        $scandir = preg_replace('/^\.+([a-z]{0,})/i', '', $scandir);
        $scandir = array_filter($scandir);
    }
    return $scandir;
}

function getDumpFiles($dir)
{
    $result = array();
    $scandir = getAllFiles($dir);
    if (!empty($scandir)) {
        foreach ($scandir as $file) {
            if (preg_match('/\.(tgz|gz|zip|sql)$/', $file)) {
                $result[] = $file;
            }
        }
    }
    return $result;
}

function dataError($error = 'Files are not exist! Please try again...', $withExit = true)
{
    echo '<h2 class="error">' . $error . '</h2>';
    if ($withExit) {
        exit();
    }
}

$data = trim($data);
if (!empty($data)) {
    if (is_dir($data)) {
        $dir = $data;
        $dir = DS . trim($dir, DS) . DS;
        $host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        $pathTree = explode('/', trim($dir, '/'));
        $pathTree = array_filter($pathTree, 'strlen');
        $c = count($pathTree) - 1;
        if ($c < 3) {
            dataError();
        }

        $prefix = '';
        $devDir = '';
        $user = '';
        foreach ($pathTree as $i => $node) {
            if (!$prefix && preg_match('/^(home|users)$/ui', $node)) {
                $prefix = $node;
            } elseif ($prefix && !$user && !$devDir) {
                $user = $node;
            } elseif ($prefix && $user && !$devDir && preg_match('/^(dev|sites)$/ui', $node)) {
                $devDir = $node;
            }
            unset($pathTree[$i]);

            if ($prefix && $user && $devDir) {
                break;
            }
        }

        $urlParts = array(
            'devDir' => $devDir,
            'user' => $user,
        );

        $urlParts = array_merge($urlParts, $pathTree);

        $urlPrefix = $host . implode('/', $urlParts) . '/';

        echo '<h2>';
        $files = getDumpFiles($dir);
        if ($files) {
            foreach ($files as $file) {
                $url = $urlPrefix . $file;
                echo '<div><a href="' . $url . '">' . $url . '</a></div>';
            }
        } elseif ($files = getAllFiles($dir)) {
            dataError('There is no any dumps here: ' . $dir . ', but we found some files:', false);
            foreach ($files as $file) {
                $url = $urlPrefix . $file;
                echo '<div><a href="' . $url . '">' . $dir . $file . '</a></div>';
            }
        } else {
            dataError('There is no any dumps here: <a href="' . $urlPrefix . '">' . $urlPrefix . '</a>');
        }
        echo '</h2>';
    } else if (preg_match('/^http(s){0,1}\:\/\//', $data)) {
        $url = $urlPrefix = $data;
        $urlPrefix = trim($urlPrefix, '/') . '/';
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
                    $url = $urlPrefix . $file;
                    echo '<div><a href="' . $url . '">' . $dir . $file . '</a></div>';
                }

            } elseif ($files = getAllFiles($dir)) {
                dataError('There is no any dumps here: ' . $dir . ', but we found some files:', false);
                foreach ($files as $file) {
                    $url = $urlPrefix . $file;
                    echo '<div><a href="' . $url . '">' . $dir . $file . '</a></div>';
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

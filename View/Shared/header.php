<?php
use \ASPHP\Core\Routing\Router;
use \ASPHP\Core\Configuration\Config;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_VIEWBAG["Title"]?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo Config::Get()["environment"]["directory"]["publicDir"].'/Styles/Css/min/font-awesome.min.css'; ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo Config::Get()["environment"]["directory"]["publicDir"].'/Styles/Css/min/bootstrap.min.css'; ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo Config::Get()["environment"]["directory"]["publicDir"].'/Styles/Css/style.css'; ?>" />
    <?php
    $this->Bundle()->Header()->Render();
	?>
</head>
<body>
    <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo Router::BuildRouteString("Home","Index", []);?>">
                    <?php echo $_VIEWBAG["Title"];?>
                </a>
            </div>
            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li><?php $this->htmlHelper->ActionLink("Link1","Home","Index");?></li>
                    <li><?php $this->htmlHelper->ActionLink("Link2","Home","Index", ["a" => 33, "b"=>44]);?></li>
                    <li><?php $this->htmlHelper->ActionLink("Link3","Home","Index", [1,2,3], ["id" => 33]);?></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container body-content">
    <div class="row">
		<div class="space-50"></div>
	</div>
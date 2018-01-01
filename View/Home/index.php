<?php
use \ASPHP\Core\Routing\Router;
?>
<div class="jumbotron">
    <h1>ASPHP</h1>
    <p class="lead">
        This is a free MVC framework for developing fast, well structured, extensible web applications
    </p>
</div>

<div class="row">
    <?php
    $this->htmlHelper->Partial('Home/_partial',
    [
        "h1" => "Account",
        "t1" => "View your account info",
        "a1" => "Goto Account &raquo;",
        "h2" => "Access files",
        "t2" => "View other users documents that you have access",
        "a2" => "Access List &raquo;",
        "h3" => "Search",
        "t3" => "Search for additional documents available at the system",
        "a3" => "Goto Search &raquo;"
    ]);
    ?>
</div>
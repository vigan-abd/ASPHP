<?php use \ASPHP\Core\Configuration\Config; ?>
        <hr />
        <footer>
            <p><?php echo $_VIEWBAG["Copy"]; ?></p>
        </footer>
    </div>
    <script src="<?php echo Config::Get()["environment"]["directory"]["publicDir"].'/Scripts/min/jquery.1.12.3.min.js'; ?>"></script>
    <script src="<?php echo Config::Get()["environment"]["directory"]["publicDir"].'/Scripts/min/bootstrap.min.js'; ?>"></script>
    <?php
    $this->Bundle()->Footer()->Render();
    ?>
</body>
</html>
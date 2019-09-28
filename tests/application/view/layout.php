<!DOCTYPE html>
<html lang="en">
<head>
<?php
$headerTplPath = APP_PATH . DS . "view"  . DS . "common" . DS . "header.php";
$this->display($headerTplPath, ["headerTitle" => isset($headerTitle) ? $headerTitle : ""]);
?>
</head>
<body>
<div class="main-content">
	<?php echo isset($mainContent) ? $mainContent : ""; ?>
</div>
<?php
$headerTplPath = APP_PATH . DS . "view"  . DS . "common" . DS . "footer.php";
$this->display($headerTplPath);
?>
</body>
</html>

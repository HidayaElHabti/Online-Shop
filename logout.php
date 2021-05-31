<?php

include "db.php";

session_start();

unset($_SESSION['userid']);
unset($_SESSION['login']);

header("Location:index.php");


?>
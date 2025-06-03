<?php

require('../BD.php');

session_start();

$Id = $_SESSION['usuario']['Id'] ?? null;

?>
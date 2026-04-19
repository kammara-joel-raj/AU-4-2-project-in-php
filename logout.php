<?php
require_once 'includes/bootstrap.php';

session_unset();
session_destroy();

header('Location: index.php');
exit;

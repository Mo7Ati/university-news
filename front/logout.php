<?php
require_once '../back/user_auth.php';
logout_user();
header('Location: index.php');
exit; 
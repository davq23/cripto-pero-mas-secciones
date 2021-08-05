<?php

require_once 'comments.php';

if (!isset($_GET) || !isset($_GET['action'])) {
    exit(400);
}

switch ($_GET['action']) {
    case 'post_comment':

        break;
    
    default:
        exit(400);
        break;
}
<?php
require_once dirname(__DIR__) . '/config/index.php';
if ($member->is_logged_in()) {
    header('Location: profile/');
} else {
    header('Location: login.php');
}

<?php
header("Content-type: application/json");

if (!empty($_GET)) {
    echo json_encode($_GET);
} else {
    echo json_encode($_POST);
}


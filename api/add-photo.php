<?php

require '../helpers/headers.php';
require '../classes/database.php';
require '../helpers/functions.php';

$database = new Database();
$db_connection = $database->db_connection();

$response = [];

$upload_photo_stmt = $db_connection->prepare("INSERT INTO images (image) VALUES (?)");

$filename = $_FILES['file']['name'];

$target_file = './uploads/' . $filename;

$file_extension = pathinfo($target_file, PATHINFO_EXTENSION);

$file_extension = strtolower($file_extension);

$valid_extension = array("png", "jpg", "jpeg");

if (in_array($file_extension, $valid_extension)) {

    if (move_uploaded_file(
        $_FILES['file']['tmp_name'],
        $target_file
    )) {

        $upload_photo_stmt->execute([$target_file]);

        $response = msg(1, 201, 'Erfolgreich hochgeladen');
    }

}

echo json_encode($response);
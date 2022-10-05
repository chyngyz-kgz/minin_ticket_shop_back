<?php

require '../helpers/headers.php';
require '../classes/database.php';
require '../middlewares/Auth.php';
require '../helpers/functions.php';

$database = new Database();
$db_connection = $database->db_connection();

$request_headers = getallheaders();
$auth = new Auth($db_connection, $request_headers);

$response = [];

if ($auth->isAuth()):

    $user_id = $auth->isAuth()['user']['user_id'];
    // print_r($user_id);
    // die;

    $ticket_data = json_decode(file_get_contents("php://input"));

    if ($_SERVER["REQUEST_METHOD"] != "POST"):
        $response = msg(0, 404, "Seite wurde nicht gefunden");
    elseif (!isset($ticket_data->flight_id)):
        $response = msg(0, 422, "Oops, du hast was vergessen");
    else:
        try {
            $insert_stmt = $db_connection->prepare('INSERT INTO ticket (user_id,flight_id, status) VALUES (?, ?, "Processing")');
            $insert_stmt->execute([$user_id, $ticket_data->flight_id]);

            $response = msg(1, 201, 'Erfolgreich hinzugefügt!');
        } catch (PDOException $e) {
            $response = msg(0, 500, $e->getMessage());
        }
    endif;
else:
    $response = msg(0, 404, 'Sorry, ich habe dich nicht erkannt :(');

endif;

echo json_encode($response);
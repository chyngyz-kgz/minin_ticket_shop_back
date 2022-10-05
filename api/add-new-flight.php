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

if ($auth->isAuth()['user']['role'] === 'admin'):

    $new_flight_data = json_decode(file_get_contents("php://input"));

    if ($_SERVER["REQUEST_METHOD"] != "POST"):
        $response = msg(0, 4, "Seite wurde nicht gefunden");
    elseif (!isset($new_flight_data->from_location) || !isset($new_flight_data->departure_time) || !isset($new_flight_data->arrival_time) || !isset($new_flight_data->duration) || !isset($new_flight_data->total_seats) || empty(trim($new_flight_data->from_location)) || empty(trim($new_flight_data->departure_time)) || empty(trim($new_flight_data->arrival_time)) || empty(trim($new_flight_data->duration)) || empty(trim($new_flight_data->total_seats))):
        $response = msg(0, 422, "Oops, du hast was vergessen");
    else:
        try {
            $insert_stmt = $db_connection->prepare('INSERT INTO flight (from_location,to_location, departure_time, arrival_time, duration,total_seats) VALUES (?, ?, ?, ?, ?, ?)');
            $insert_stmt->execute([$new_flight_data->from_location, 'Mars International Station', $new_flight_data->departure_time, $new_flight_data->arrival_time, $new_flight_data->duration, $new_flight_data->total_seats]);

            $response = msg(1, 201, 'Erfolgreich hinzugefügt!');
        } catch (PDOException $e) {
            $response = msg(0, 500, $e->getMessage());
        }
    endif;
else:
    $response = msg(0, 404, 'Sorry, ich habe dich nicht erkannt :(');

endif;

echo json_encode($response);
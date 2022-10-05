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
$user_tickets = [];

if ($auth->isAuth()) {
    $user_data = $auth->isAuth()['user'];

    $user_profile_stmt = $db_connection->prepare('SELECT * FROM ticket LEFT JOIN flight using(flight_id) WHERE user_id = :user_id');
    $user_profile_stmt->execute(['user_id' => $user_data['user_id']]);

    if ($user_profile_stmt->rowCount()) {
        while ($row = $user_profile_stmt->fetch()) {
            array_push($user_tickets, [
                'ticket_id' => $row['ticket_id'],
                'flight_id' => $row['flight_id'],
                'status' => $row['status'],
                'from_location' => $row['from_location'],
                'to_location' => $row['to_location'],
                'departure_time' => $row['departure_time'],
                'arrival_time' => $row['arrival_time'],
                'duration' => $row['duration'],
                'total_seats' => $row['total_seats'],
            ]);
        }
    }

    $user_data['tickets'] = $user_tickets;
    $response = msg(1, 200, 'Alles gut :)', $user_data);
} else {
    $response = msg(0, 404, 'Sorry, es ist etwas falsch gelaufen :(');
}

echo json_encode($response);
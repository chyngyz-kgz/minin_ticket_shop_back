<?php
require '../helpers/headers.php';
require '../classes/database.php';

$database = new Database();
$db_connection = $database->db_connection();

$query = 'SELECT * FROM flight';

$stmt = $db_connection->query($query);

$response = [];

$flights_count = $stmt->rowCount();

if ($flights_count > 0) {
    while ($row = $stmt->fetch()) {
        $departure_time = strtotime($row['departure_time']);
        $arrival_time = strtotime($row['arrival_time']);

        array_push($response, [
            'flight_id' => $row['flight_id'],
            'from_location' => $row['from_location'],
            'to_location' => $row['to_location'],
            'departure_time' => date('D d/M/Y H:i', $departure_time),
            'arrival_time' => date('D d/M/Y H:i', $arrival_time),
            'duration' => $row['duration'],
            'total_seats' => $row['total_seats'],
        ]);
    }
    echo json_encode($response);
} else {
    http_response_code(404);
    echo json_encode([
        'message' => 'No records found',
    ]);
}
<?php
require '../../helpers/headers.php';
require '../../classes/database.php';
require '../../helpers/functions.php';

$database = new Database();
$db_connection = $database->db_connection();

$response = [];

$new_user_data = json_decode(file_get_contents("php://input"));

if ($_SERVER["REQUEST_METHOD"] != "POST"):
    $response = msg(0, 4, "Page not found");
elseif (!isset($new_user_data->firstName) || !isset($new_user_data->lastName) || !isset($new_user_data->email) || !isset($new_user_data->password) || !isset($new_user_data->phone) || empty(trim($new_user_data->firstName)) || empty(trim($new_user_data->lastName)) || empty(trim($new_user_data->email)) || empty(trim($new_user_data->password)) || empty(trim($new_user_data->phone))):
    $response = msg(0, 422, "Oops, du hast was vergessen");
else:
    try {
        $check_email_stmt = $db_connection->prepare('SELECT email FROM users WHERE email = ?');
        $check_email_stmt->execute([$new_user_data->email]);

        if ($check_email_stmt->rowCount()):
            $response = msg(0, 422, 'Du bist zu spaet! Es ist schon jemand mit dieser Email registriert');
        else:
            $insert_stmt = $db_connection->prepare('INSERT INTO users (first_name,last_name, email, password, phone,role) VALUES (:first_name, :last_name, :email, :password, :phone, "user")');
            $insert_stmt->execute([
                'first_name' => $new_user_data->firstName,
                'last_name' => $new_user_data->lastName,
                'email' => $new_user_data->email,
                'password' => password_hash($new_user_data->password, PASSWORD_DEFAULT),
                'phone' => $new_user_data->phone,
            ]);

            $response = msg(1, 201, 'Erfolgreich registriert!');
        endif;
    } catch (PDOException $e) {
        $response = msg(0, 500, $e->getMessage());
    }
endif;

echo json_encode($response);
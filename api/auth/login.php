<?php

require '../../helpers/headers.php';
require '../../helpers/functions.php';
require '../../classes/database.php';
require '../../classes/JwtHandler.php';

$database = new Database();
$db_connection = $database->db_connection();

$user_data = json_decode(file_get_contents('php://input'));
$response = [];

if ($_SERVER['REQUEST_METHOD'] != "POST"):
    $response = msg(0, 404, 'Page not found');

elseif (!isset($user_data->email) || !isset($user_data->password) || empty(trim($user_data->email)) || empty(trim($user_data->password))):
    $response = msg(0, 422, "Oops, du hast was vergessen");
else:
    try {
        $login_stmt = $db_connection->prepare('SELECT * FROM users WHERE email = :email');
        $login_stmt->execute(['email' => $user_data->email]);

        if ($login_stmt->rowCount()):
            $row = $login_stmt->fetch(PDO::FETCH_ASSOC);
            $password_checked = password_verify($user_data->password, $row['password']);

            if ($password_checked):
                $jwt = new JwtHandler();
                $token = $jwt->_jwt_encode_data(
                    'http://localhost/php_auth_api/',
                    array('user_id' => $row['user_id'])
                );
                $response = msg(1, 200, 'Erfolgreich angemeldet', [
                    'token' => $token,
                    'role' => $row['role'],
                ]);
            else:
                $response = msg(0, 422, 'Kennwort wurde falsch eingegeben');
            endif;
        else:
            $response = msg(0, 422, 'Email ist falsch');
        endif;

    } catch (PDOEcxeption $e) {
        $response = msg(0, 500, $e->getMessage());
    }
endif;

echo json_encode($response);
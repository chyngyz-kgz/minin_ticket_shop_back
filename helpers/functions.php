<?php
function msg($success, $status, $message, $extra = [])
{
    return [
        'success' => $success,
        'status' => $status,
        'message' => $message,
        'data' => $extra,
    ];
};
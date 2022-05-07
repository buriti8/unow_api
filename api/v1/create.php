<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../class/Appointment.php';

$database = new Database();
$db = $database->getConnection();

$item = new Appointment($db);

/* Getting the data from the request body. */
$data = json_decode(file_get_contents("php://input"));

/* This is the data that is being sent to the database. */
$item->name = $data->name;
$item->document = $data->document;
$item->appointment_date = $data->appointment_date;
$item->created_at = date('Y-m-d H:i:s');
$item->updated_at = date('Y-m-d H:i:s');

/* Checking if the appointment was created and if it was it will return a success message and if it
wasn't it will return an error message. */
if ($item->createAppointment()) {
    http_response_code(201);

    $response = [
        'message' => "Success"
    ];

    echo json_encode($response);
} else {
    http_response_code(400);

    $response = [
        'message' => "Error, the appointment can't be created"
    ];

    echo json_encode($response);
}

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

$item->id = isset($_GET['id']) ? $_GET['id'] : die();

$item->showAppointment();

/* This is a conditional statement that checks if the name is not null. If it is not null, it will
return the appointment details. If it is null, it will return a message that no record was found. */
if ($item->name != null) {
    $appointment = [
        "id" => $item->id,
        "name" => $item->name,
        "document" => $item->document,
        "appointment_date" => $item->appointment_date,
        "status" => $item->status,
        "created_at" => $item->created_at,
        "updated_at" => $item->updated_at,
    ];

    http_response_code(200);
    echo json_encode($appointment);
} else {
    http_response_code(404);

    echo json_encode(
        [
            "message" => "No record found."
        ]
    );
}

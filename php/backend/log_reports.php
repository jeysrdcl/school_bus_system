<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once 'db_connect.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];


$basePath = "/school_bus_system/php/backend/log_reports.php";
$endpoint = str_replace($basePath, "", strtok($uri, '?'));
$endpoint = trim($endpoint, "/");

switch ($method) {
    case "GET":
        switch ($endpoint) {
            case "students/logs":
                $result = getStudentLogs($conn, $_GET);
                if ($result) {
                    response(200, "Student Logs fetched successfully.", $result);
                }
                break;
            case "bus/logs":
                $result = getAllBusLogs($conn, $_GET);
                if ($result) {
                    response(200, "Bus Logs fetched successfully.", $result);
                }
                break;
        }
        break;
    case "POST":

        break;
}

function getStudentLogs($conn, $filters = [])
{
    $sql =
        "SELECT 
            sl.id AS log_id,
            s.student_id,
            s.name AS student_name,
            sl.pick_up_time,
            sl.drop_off_time,
            sl.status AS student_status,
            br.id AS session_id,
            br.time_start AS session_start,
            br.time_end AS session_end,
            u.full_name AS conductor_name,
            b.bus_name,
            b.plate_number
        FROM student_logs sl
        JOIN students s ON sl.student_id = s.student_id
        JOIN bus_records br ON sl.session_id = br.id
        JOIN bus_table b ON br.bus_id = b.id
        JOIN users u ON br.conductor_id = u.id
        WHERE u.role = 'Teacher'";

    //dynamic filters
    if (!empty($filters['student_id'])) {
        $sql .= " AND s.student_id = :student_id";
    }
    if (!empty($filters['conductor_name'])) {
        $sql .= " AND u.full_name LIKE :conductor_name";
    }
    if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
        $sql .= " AND sl.pick_up_time BETWEEN :date_from AND :date_to";
    }
    $sql .= " ORDER BY sl.pick_up_time DESC";

    $stmt = $conn->prepare($sql);

    // bind parameters 
    if (!empty($filters['student_id'])) {
        $stmt->bindParam(":student_id", $filters['student_id']);
    }
    if (!empty($filters['conductor_name'])) {
        $conductor_name = "%" . $filters['conductor_name'] . "%";
        $stmt->bindParam(":conductor_name", $conductor_name);
    }
    if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
        $stmt->bindParam(":date_from", $filters['date_from']);
        $stmt->bindParam(":date_to", $filters['date_to']);
    }
    try {
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($rows) {
            return $rows;
        } else {
            response(200, "No Available Logs.");
        }

    } catch (PDOException $e) {
        response(500, $e->getMessage());
    }
}

function getAllBusLogs($conn, $filters = [])
{
    $sql = "SELECT 
                br.id AS session_id,
                br.bus_id,
                b.bus_name,
                b.plate_number,
                b.bus_type,
                b.capacity,
                b.max_capacity,
                b.status AS bus_status,
                br.conductor_id,
                u.full_name AS conductor_name,
                br.direction,
                br.status AS session_status,
                br.time_start,
                br.time_end,
                br.current_load
            FROM bus_records br
            JOIN bus_table b ON br.bus_id = b.id
            JOIN users u ON br.conductor_id = u.id
            WHERE u.role = 'Teacher'";

    //dynamic filters
    if (!empty($filters['bus_id'])) {
        $sql .= " AND br.bus_id = :bus_id";
    }
    if (!empty($filters['conductor_name'])) {
        $sql .= " AND u.full_name LIKE :conductor_name";
    }
    if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
        $sql .= " AND br.time_start BETWEEN :date_from AND :date_to";
    }
    $sql .= " ORDER BY br.time_start DESC";

    $stmt = $conn->prepare($sql);

    // bind parameters
    if (!empty($filters['bus_id'])) {
        $stmt->bindParam(":bus_id", $filters['bus_id']);
    }
    if (!empty($filters['conductor_name'])) {
        $conductor_name = "%" . $filters['conductor_name'] . "%";
        $stmt->bindParam(":conductor_name", $conductor_name);
    }
    if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
        $stmt->bindParam(":date_from", $filters['date_from']);
        $stmt->bindParam(":date_to", $filters['date_to']);
    }

    try {
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($rows) {
            return $rows;
        } else {
            response(200, "No Available Logs.");
        }

    } catch (PDOException $e) {
        response(500, $e->getMessage());
    }

}

function response($status, $message, $data = [])
{
    http_response_code($status);
    echo json_encode(["status" => $status, "message" => $message, "data" => $data]);
}

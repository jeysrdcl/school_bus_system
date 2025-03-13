<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once 'db_connect.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];


$basePath = "/school_bus_system/php/backend/bus_crud.php";
$endpoint = str_replace($basePath, "", strtok($uri, '?'));
$endpoint = trim($endpoint, "/");

switch ($method) {
    case 'POST':
        if ($endpoint == 'buses/create') {
            $required_fields = ['bus_name', 'plate_number', 'bus_type', 'capacity', 'max_capacity'];
            // $sample = ['bus_name' => 'BUS C', 'plate_number' => 'XTVY-4567', 'bus_type' => 'Mini Bus', 'capacity' => 20, 'max_capacity' => 30];
            if (!array_diff_key(array_flip($required_fields), $_POST)) {
                if (!checkPlateNumber($conn, $_POST['plate_number'])) {
                    $stmt = $conn->prepare("INSERT INTO bus_table (bus_name, plate_number, bus_type, capacity, max_capacity, status) VALUES (:bus_name, :plate_number, :bus_type, :capacity, :max_capacity, 'ACTIVE')");
                    try {
                        if ($stmt->execute($_POST)) {
                            $result = fetchBusses($conn, ['plate_number' => $_POST['plate_number']]);
                            response(200, "Bus Added successfully", $result);
                        } else {
                            response(500, "Bus Registration failed");
                        }
                    } catch (PDOException $e) {
                        response(500, $e->getMessage());
                    }
                } else {
                    response( 400,'Plate Number already Exists');
                }
            } else {
                response(400, 'Missing Required Fields');
            }
        } elseif ($endpoint == 'buses/update') {
            $required_fields = ['bus_name', 'plate_number', 'bus_type', 'capacity', 'max_capacity', 'status'];
            // $sample = ['id' => 4, 'bus_name' => 'BUS D', 'plate_number' => 'XTVY-4567', 'bus_type' => 'Mini Bus', 'capacity' => 20, 'max_capacity' => 30];


            $updateData = array_intersect_key($_POST, array_flip($required_fields));
            if (!isset($_POST['id'])) {
                response(400, 'Missing plate_number for update');
                exit;
            }
            if (empty($updateData)) {
                response(400, 'No valid fields provided');
                exit;
            }

            $setClause = implode(", ", array_map(fn($field) => "$field = :$field", array_keys($updateData)));
            $updateData['id'] = $_POST['id'];
            $sql = "UPDATE bus_table SET $setClause WHERE id = :id";
            $stmt = $conn->prepare($sql);

            try {
                $stmt->execute($updateData);
                $result = fetchBusses($conn, ['id' => $_POST['id']]);

                response(200, "Bus details updated successfully", $result);
            } catch (PDOException $e) {
                response(500, $e->getMessage());
            }
        } else {
            response(404, "Invalid endpoint");
        }

        break;
    case 'GET':
        if ($endpoint == "buses") {
            response(200, 'Bus Fetched Successfully', fetchBusses($conn, empty($_GET) ? null : $_GET));
        } else {
            response(404, "Invalid endpoint");
        }
        break;

}

$all_busses = fetchBusses($conn, null);

function checkPlateNumber($conn, $plateNumber)
{
    $stmt = $conn->prepare("SELECT COUNT(*) FROM bus_table WHERE plate_number = :plateNumber");
    $stmt->execute(['plateNumber' => $plateNumber]);
    return $stmt->fetchColumn() > 0;
}

function fetchBusses($conn, $params = null)
{
    if ($params == null) {
        try {
            $stmt = $conn->prepare("SELECT * FROM bus_table WHERE status = :status");
            $stmt->execute(['status' => 'ACTIVE']);
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $response;
        } catch (PDOException $e) {
            response(500, $e->getMessage());
            return null;
        }
    } else {
        $sql = "SELECT * FROM bus_table";

        $conditions = [];
        foreach ($params as $key => $value) {
            $conditions[] = "$key = :$key";
            $filters[$key] = $value;
        }
        $sql .= " WHERE " . implode(" AND ", $conditions);
        $stmt = $conn->prepare($sql);
        try {
            $stmt->execute($filters);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            response(500, $e->getMessage());
            return null;
        }
    }
}

function response($status, $message, $data = [])
{
    http_response_code($status);
    echo json_encode(["status" => $status, "message" => $message, "data" => $data]);
}













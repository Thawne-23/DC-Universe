<?php

$conn = include 'dbConnect.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['name'])) {
    $name = $_GET['name'];

    $stmt = $conn->prepare("SELECT * FROM tbl_dc_info WHERE title = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        echo json_encode([
            "status" => "success",
            "data" => $row
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Character not found"
        ]);
    }

    $stmt->close();
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No name provided"
    ]);
}
?>

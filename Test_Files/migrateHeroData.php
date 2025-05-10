<?php
// Database connection (adjust with your credentials)

set_time_limit(1000); // Set time limit to 300 seconds (5 minutes)

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dc_blog";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// List of hero IDs to migrate
$heroIDs = [
     "124", "136", 
    "165", "172", "173", "194", "195", "204", "212", "216", "224", "230", 
    "298", "306", "309", "316", "315", "328", "370", "376", "386", "405", 
    "427", "432", "546", "561", "625", "643", "644", "263", "613", "678", 
    "720", "730"
];

foreach ($heroIDs as $heroID) {
    $heroData = getHeroData($heroID);
    
    if ($heroData) {
        $title = $heroData['name'];
        $full_name = $heroData['full_name'];
        $description = $heroData['description'];
        $powers = $heroData['powers'];
        $base_of_operations = $heroData['base_of_operations'];
        $occupation = $heroData['occupation'];
        $real_name = $heroData['real_name'];
        $imageUrl = $heroData['imageUrl'];

        // Prepare SQL query to insert hero data into the database
        $stmt = $conn->prepare("
            INSERT INTO tbl_dc_info (title, full_name, description, powers, base_of_operations, occupation, real_name, image_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        // Bind the parameters for the prepared statement
        $stmt->bind_param("ssssssss", $title, $full_name, $description, $powers, $base_of_operations, $occupation, $real_name, $imageUrl);
        
        // Execute the query
        if ($stmt->execute()) {
            echo "Hero $title data inserted successfully.<br>";
        } else {
            echo "Error inserting hero data: " . $stmt->error . "<br>";
        }
        $stmt->close();
    }
}

$conn->close();

function getHeroData($heroID) {
    $apiUrl = "https://superheroapi.com/api/204d8eaf58514069223a19c1fbe49a42/$heroID";

    // Fetch data from the superhero API
    $response = @file_get_contents($apiUrl); // Use @ to suppress potential warnings

    if ($response === false) {
        echo "Error fetching data for hero ID $heroID from Superhero API.<br>";
        return null;
    }

    $data = json_decode($response, true);

    if ($data && isset($data['response']) && $data['response'] === "success") {
        $hero = $data;

        // Call the AI API to generate the description
        $aiDescription = generateAIDescription($hero['name']);

        return [
            'name' => $hero['name'],
            'full_name' => $hero['biography']['full-name'] ?? "Unknown",
            'description' => $aiDescription,
            'powers' => "Intelligence: " . $hero['powerstats']['intelligence'] . ", " . 
                        "Strength: " . $hero['powerstats']['strength'] . ", " . 
                        "Combat: " . $hero['powerstats']['combat'],
            'base_of_operations' => $hero['work']['base'] ?? "Unknown",
            'occupation' => $hero['work']['occupation'] ?? "Unknown",
            'real_name' => $hero['biography']['full-name'] ?? "Unknown",
            'imageUrl' => $hero['image']['url']
        ];
    } else {
        echo "Could not find data for hero ID $heroID or API error: " . (isset($data['error']) ? $data['error'] : 'Unknown error') . "<br>";
        return null; // Return null if hero data is not found or API returns an error
    }
}

function generateAIDescription($heroName) {
    $apiKey = "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";
    $url = "https://openrouter.ai/api/v1/chat/completions";
    
    // Prepare the request payload
    $payload = json_encode([
        "model" => "nvidia/llama-3.1-nemotron-ultra-253b-v1:free",
        "messages" => [
            ["role" => "user", "content" => "Create a brief description for this character (Note: make it 150-200 words only, and just give me the text only and no need to add title or \"Hero:\"): $heroName"]
        ]
    ]);
    
    // Prepare the headers
    $headers = [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ];

    // Use cURL to send the request to the AI API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Get the response
    $response = curl_exec($ch);
    curl_close($ch);

    // Parse the response
    $data = json_decode($response, true);
    return $data['choices'][0]['message']['content'] ?? "Description not available."; // Return the AI-generated description
}
?>

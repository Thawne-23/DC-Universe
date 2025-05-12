<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header("Location: home.php");
    exit;
}


$conn = include '../script/dbConnect.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM tbl_dc_info ORDER BY title ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $allCharacters = array();
    while($row = $result->fetch_assoc()) {
        $allCharacters[] = $row;
    }
    $result->data_seek(0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>All DC Characters</title>
    <base href="/Dc_Characters/" />
    <link rel="icon" type="image/png" href="images/dc_logo.png">
    <link rel="stylesheet" href="css/base.css" />
    <style>
        h2 {
            text-align: center;
            margin: 60px 0 30px;
            font-size: 2.6em;
        }
        .search-container {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }
        .search-input {
            padding: 12px 20px;
            width: 340px;
            max-width: 90vw;
            border-radius: 50px;
            border: 1px solid #ccc;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            font-size: 1rem;
            transition: 0.3s ease;
        }
        .search-input:focus {
            border-color: #007bff;
            outline: none;
        }
        .character-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .character-item {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            text-align: center;
            padding: 15px;
            cursor: pointer;
            width: 200px;
        }
        .character-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
        .character-item img {
            width: 100%;
            max-height: 200px;
            object-fit: contain;
            border-radius: 8px;
        }
        .character-name {
            margin-top: 12px;
            font-weight: 600;
            color: #007bff;
            font-size: 1.05em;
        }
        #searchResults {
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            border: 1px solid #ccc;
            border-top: none;
            width: 340px;
            max-width: 90vw;
            max-height: 250px;
            overflow-y: auto;
            box-shadow: 0 8px 16px rgba(0,0,0,0.12);
            border-radius: 0 0 10px 10px;
            display: none;
            z-index: 999;
        }
        #searchResults.visible {
            display: block;
        }
        #searchResults div {
            padding: 10px;
            cursor: pointer;
        }
        #searchResults div:hover {
            background-color: #007bff;
            color: white;
        }
        @media (max-width: 480px) {
            .search-input {
                width: 80%;
            }
            #searchResults {
                width: 80%;
            }
        }
        .container {
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'navbar.php'; ?>
        <main class="content" role="main">
            <section class="section" aria-label="List of all DC characters">
                <h2>All DC Characters</h2>
                <div class="search-container">
                    <input 
                        type="text" 
                        id="characterSearch" 
                        placeholder="Search characters..." 
                        class="search-input" 
                        aria-label="Search DC characters"
                        autocomplete="off" 
                    />
                    <div id="searchResults" role="listbox" aria-live="polite"></div>
                </div>
                <div class="character-list" id="charactersContainer">
                    <?php
                        while($row = $result->fetch_assoc()) {
                            $title = htmlspecialchars($row["title"]);
                            $characterData = strtolower($row["title"]);
                            echo "<div class='character-item' data-character='" . htmlspecialchars($characterData, ENT_QUOTES) . "' tabindex='0' role='option' aria-label='{$title}'>";
                            echo "<a href='webpages/character.php?heroId={$row["dc_info_id"]}&userId={$_SESSION["user_id"]}' style='text-decoration: none; color: inherit;'>";
                            if(isset($row["image_url"]) && !empty($row["image_url"])){
                                echo "<img src='" . htmlspecialchars($row["image_url"]) . "' alt='" . $title . "' loading='lazy' />";
                            }
                            echo "<div class='character-name'>{$title}</div>";
                            echo "</a></div>";
                        }
                    ?>
                </div>
            </section>
        </main> 
    </div>
    <?php include 'footer.php'; ?>
    <script src="script/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const allCharacters = <?php echo json_encode($allCharacters); ?>;
            const characterSearch = document.getElementById('characterSearch');
            const searchResults = document.getElementById('searchResults');
            const charactersContainer = document.getElementById('charactersContainer');

            characterSearch.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase().trim();
                if (!searchTerm) {
                    searchResults.classList.remove('visible');
                    searchResults.innerHTML = '';
                    filterCharacters('');
                    return;
                }

                const matched = allCharacters.filter(c => c.title.toLowerCase().includes(searchTerm));
                searchResults.innerHTML = matched.length === 0
                    ? '<div>No matches found</div>'
                    : matched.map(c => `<div tabindex="0" role="option">${c.title}</div>`).join('');
                searchResults.classList.add('visible');
            });

            searchResults.addEventListener('click', function (e) {
                if (e.target.matches('div[role="option"]')) {
                    const selected = e.target.textContent;
                    characterSearch.value = selected;
                    filterCharacters(selected.toLowerCase());
                    searchResults.classList.remove('visible');
                    searchResults.innerHTML = '';
                }
            });

            searchResults.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && e.target.matches('div[role="option"]')) {
                    e.preventDefault();
                    e.target.click();
                }
            });

            characterSearch.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    searchResults.classList.remove('visible');
                    searchResults.innerHTML = '';
                }
            });

            function filterCharacters(searchTerm) {
                const items = charactersContainer.querySelectorAll('.character-item');
                items.forEach(item => {
                    const name = item.getAttribute('data-character')?.toLowerCase().trim();
                    item.style.display = !searchTerm || name.includes(searchTerm) ? 'flex' : 'none';
                });
            }
        });
    </script>
</body>
</html>

<?php
} else {
    echo "<div class='container'><p style='text-align:center; margin-top:50px;'>No characters found in the database.</p></div>";
}
$conn->close();
?>

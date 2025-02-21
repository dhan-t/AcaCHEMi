<?php
// Database connection
$host = 'localhost';
$db = 'php_activity';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
try {
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode to exception
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Initialize variables
$id = $fn = $ln = $mn = '';

// Handle insert or update
if (isset($_POST['submit'])) {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $ln = isset($_POST['lastname']) ? $_POST['lastname'] : '';
    $fn = isset($_POST['firstname']) ? $_POST['firstname'] : '';
    $mn = isset($_POST['middlename']) ? $_POST['middlename'] : '';

    if (!empty($id)) {
        // Update the existing record
        $sql = "UPDATE php_activity_db SET firstname = :firstname, middlename = :middlename, lastname = :lastname WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':id' => $id, ':firstname' => $fn, ':middlename' => $mn, ':lastname' => $ln));
        echo "Record updated successfully!<br>";
    } else {
        // Insert a new record
        $sql = "INSERT INTO php_activity_db (lastname, firstname, middlename) VALUES (:ln, :fn, :mn)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':ln' => $ln, ':fn' => $fn, ':mn' => $mn));
        echo "Data inserted successfully!<br>";
    }
    header("Location: " . $_SERVER['SCRIPT_NAME']); // Redirect to refresh the table
    exit;
}

// Handle edit action
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT id, firstname, middlename, lastname FROM php_activity_db WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':id' => $id));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $fn = $row['firstname'];
        $mn = $row['middlename'];
        $ln = $row['lastname'];
    }
}

// Handle delete action
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $sql = "DELETE FROM php_activity_db WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':id' => $id));
    echo "Data deleted successfully!<br>";
    header("Location: " . $_SERVER['SCRIPT_NAME']); // Redirect to the same page
    exit;
}

// Fetch all records
$sql = "SELECT id, lastname, firstname, middlename FROM php_activity_db";
$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StackRead</title>

    <style>
    /*Main*/
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }

    body {
        margin: 0;
        font-family: Arial, sans-serif;
        overflow: hidden;
    }

    nav {
        background-color: #87A375;
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: fixed;
        width: 100%;
        z-index: 2;
    }

    /* Navigation */
    nav h1 {
        color: white;
        font-size: 30px;
        font-weight: bold;
    }

    nav ul {
        list-style: none;
        display: flex;
    }

    nav ul li {
        margin-left: 2rem;
    }

    nav ul li a {
        text-decoration: none;
        color: white;
        font-size: 20px;
    }

    nav a:hover {
        text-decoration: none;
        cursor: pointer;
        color: #f0f0f0;
        transition: transform 0.6s ease-in-out;
    }

    .outerContainer {
        display: flex;
        transition: transform 0.6s ease-in-out;
        width: 500%;
        position: relative;
        height: 200%;
        z-index: 1;
    }

    .section {
        width: 100vw;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
    }

    /*Big AF Container*/
    #home {
        z-index: 1;
        background-color: white;
    }

    .auth-container {
        display: flex;
        flex-direction: row;
        width: 100%;
        height: 70%;
        border-radius: 10px;
        transition: transform 0.3s ease, opacity 0.3s ease;
        left: 15%;
        align-items: center;
        background-color: gray;
    }

    .panel {
        flex: 1;
        height: 100%;
        width: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: opacity 0.5s ease;
    }

    .panel.left {
        background-color: #ffffff;
    }

    .panel.right {
        background-color: #ffffff;
    }

    .form-container {
        text-align: center;
        height: 100%;
        width: 50%;
        align-content: center;

    }

    .form-container h2 {
        margin-bottom: 20px;
        font-size: 22px;
        color: #333;
    }

    .form-container input {
        display: block;
        margin: 10px auto;
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        outline: none;
    }

    .form-container input:disabled {
        background-color: #e0e0e0;
        cursor: not-allowed;
    }

    .form-container button {
        margin-top: 10px;
        width: 100%;
        padding: 10px;
        background-color: #87A375;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .form-container button:hover {
        background-color: #6F8A5E;
    }

    .form-container button:disabled {
        background-color: #a0a0a0;
        cursor: not-allowed;
    }

    .dimmed {
        opacity: 0.3;
    }

    .fullscreen-section {
        max-width: 1200px;
        margin: 0 auto;
    }

    .login-panels {
        display: flex;
        flex-direction: row;
        width: 100%;
        height: 100%;
        z-index: 3;
    }

    .landing {
        transition: transform 0.6s ease-in-out;
        position: absolute;
        width: 100%;
        height: 100%;
        z-index: 3;
        background-color: white;
    }

    .landing,
    .outerContainer {
        transition: z-index 0.3s ease, transform 0.5s ease, opacity 0.5s ease;
    }

    /*Tabs*/
    #home {
        background-color: white;
        color: white;
    }

    #marketplace {
        background-color: white;
        color: white;
        display: flex;
        flex-direction: column;
        padding-top: 5vh;
    }

    #literaryhits {
        background-color: white;
        color: white;
        display: flex;
        flex-direction: column;
        padding-top: 5vh;
    }

    #library {
        background-color: white;
        color: white;
        display: flex;
        flex-direction: column;
        padding-top: 5vh;
    }

    #profile {
        background-color: white;
        color: white;
    }

    .full-width-container {
        width: 90%;
        max-width: 900px;
        height: 80%;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        background-color: transparent;
    }

    /*Home */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 75px;
        margin-bottom: 25px;
    }

    #home-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 75px;
        margin-bottom: 0px;
    }

    .header h1 {
        display: flex;
        font-size: 32px;
        color: #333;
        width: 15rem;
    }

    .search-bar-container {
        display: grid;
        grid-template-columns: 1fr auto;
    }

    .search-bar {
        display: flex;
        align-items: center;
        background-color: #F2F2F2;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        flex: 1;
        width: 40.5vw;
        max-width: 1000px;
    }

    .search-bar input {
        border: none;
        outline: none;
        font-size: 16px;
        flex: 1;
        margin: 10px;
        width: 80%;
        background-color: #F2F2F2;
    }

    .search-bar button {
        background: none;
        border: none;
        cursor: pointer;
        padding-right: 1rem;
    }

    .search-bar img {
        width: 20px;
        height: 20px;
    }

    .cart-icon {
        background-color: #9FC880;
        border-radius: 20%;
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        margin-left: 10px;
    }

    .cart-icon img {
        width: 20px;
        height: 20px;
    }

    .content {
        display: flex;
        gap: 20px;
    }

    .home-section {
        flex: 1;
        background-color: #A98467;
        padding: 16px;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        height: 70vh;
        width: 50vw;
        max-height: 900px;
        max-width: 1400px;
        margin-top: 35px;
    }

    .home-section h2 {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: white;
    }

    .favorite-books-list,
    .last-read-list {
        flex: 1;
        overflow-y: auto;
        max-height: 800px;
    }

    .favorite-book-item,
    .last-read-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #6C584C;
        padding: 12px;
        margin-bottom: 12px;
        border-radius: 8px;
    }

    .favorite-book-item:last-child,
    .last-read-item:last-child {
        margin-bottom: 0;
    }

    .book-info {
        display: flex;
        align-items: center;
    }

    .book-icon {
        width: 40px;
        height: 40px;
        background-color: white;
        border-radius: 4px;
        margin-right: 12px;
    }

    .book-details {
        color: white;
    }

    .book-title {
        font-size: 14px;
        font-weight: bold;
    }

    .book-author {
        font-size: 12px;
        margin-top: 4px;
    }

    .favorite-action-icon {
        width: 24px;
        height: 24px;
        cursor: pointer;
    }

    .last-read-action-icon {
        width: 24px;
        height: 24px;
        cursor: pointer;
    }

    /* Scrollbar styling */
    .favorite-books-list::-webkit-scrollbar,
    .last-read-list::-webkit-scrollbar {
        width: 6px;
    }

    .favorite-books-list::-webkit-scrollbar-thumb,
    .last-read-list::-webkit-scrollbar-thumb {
        background-color: #A98467;
        border-radius: 3px;
    }

    .favorite-books-list::-webkit-scrollbar-track,
    .last-read-list::-webkit-scrollbar-track {
        background-color: #A98467;
    }

    /*Marketplace */
    .marketplace-title {
        text-align: center;
        font-size: 2rem;
        margin: 1rem 0;
        color: #333;
        font-weight: bold;
        padding: 10px;
        position: relative;
        top: 3.5%;
    }

    .add-book-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        background-color: #A98467;
        border-radius: 12px;
        margin: 10px auto;
        z-index: 1;
        width: 100%;
        box-sizing: border-box;
    }

    .add-book-section input {
        flex: 1;
        padding: 8px;
        margin-right: 10px;
        border-radius: 6px;
        border: 1px solid #ddd;
        font-size: 1rem;
        box-sizing: border-box;
    }

    .add-book-section button {
        background-color: #8bc34a;
        color: #fff;
        padding: 8px 16px;
        font-size: 1rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        white-space: nowrap;
    }

    .add-book-section button:hover {
        background-color: #7cb342;
    }


    .add-book-section button:hover {
        background-color: #7cb342;
    }

    /* Scrollable Div */
    .scrollable-div {
        flex-grow: 1;
        max-height: 100%;
        overflow-y: auto;
        padding: 20px;
        background-color: #A98467;
        justify-content: space-between;
        margin-bottom: 12px;
        border-radius: 8px;
        flex: 1;
    }

    .scrollable-div::-webkit-scrollbar {
        width: 6px;
    }

    .scrollable-div::-webkit-scrollbar-thumb {
        background-color: #A98467;
        border-radius: 3px;
    }

    .scrollable-divt::-webkit-scrollbar-track {
        background-color: #A98467;
    }


    /* Book Item Styling */
    .book-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #5e4b3b;
        color: #fff;
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .book-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
    }

    /* Book Info */
    .book-info {
        display: flex;
        align-items: center;
    }

    .book-icon {
        width: 50px;
        height: 70px;
        background-color: #ddd;
        border-radius: 6px;
        margin-right: 10px;
    }

    .book-details .book-title {
        font-size: 1rem;
        font-weight: bold;
        line-height: 1.2;
    }

    .book-details .book-author {
        font-size: 0.9rem;
        color: #ccc;
    }

    /* Buy Button */
    .buy-button {
        background-color: #9FC880;
        border: none;
        color: #fff;
        font-size: 1.2rem;
        border-radius: 8px;
        padding: 5px 10px;
        cursor: pointer;
        transition: background-color 0.2s ease-in-out;
    }

    .buy-button:hover {
        background-color: #7cb342;
        transform: scale(1.1);
    }

    .buy-button span {
        display: inline-block;
        margin-right: 5px;
    }

    /*Hits*/
    /*Library*/
    .library-title {
        text-align: center;
        font-size: 2rem;
        margin: 1rem 0;
        color: #333;
        font-weight: bold;
        padding: 10px;
        position: relative;
        top: 3.5%;
    }

    /* Search Bar Section */
    .library-search-bar {
        display: flex;
        justify-content: center;
        padding: 10px;
        background-color: #A98467;
        border-radius: 12px;
        margin: 10px;
    }

    .library-search-bar input {
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ddd;
        font-size: 1rem;
    }

    /* Scrollable Div */
    .book-list-container {
        flex-grow: 1;
        max-height: 100%;
        overflow-y: auto;
        padding: 10px;
        background-color: #f5f0e6;
        border-radius: 12px;
    }

    /* Book Item Styling */
    .book-item-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #5e4b3b;
        color: #fff;
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        cursor: pointer;
    }

    .book-item-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
    }

    /* Book Info */
    .book-item-info {
        display: flex;
        align-items: center;
    }

    .book-item-icon {
        width: 50px;
        height: 70px;
        background-color: #ddd;
        border-radius: 6px;
        margin-right: 10px;
    }

    .book-item-details .book-title {
        font-size: 1rem;
        font-weight: bold;
        line-height: 1.2;
    }

    .book-item-details .book-author {
        font-size: 0.9rem;
        color: #ccc;
    }

    /* Buy Button */
    .book-buy-button {
        background-color: #9FC880;
        border: none;
        color: #fff;
        font-size: 1.2rem;
        border-radius: 8px;
        padding: 5px 10px;
        cursor: pointer;
        transition: background-color 0.2s ease-in-out;
    }

    .book-buy-button:hover {
        background-color: #7cb342;
        transform: scale(1.1);
    }

    .book-buy-button span {
        display: inline-block;
        margin-right: 5px;
    }

    /* Book Details Modal */
    .book-details-modal {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        z-index: 100;
        display: none;
        width: 80%;
        max-width: 500px;
    }

    .book-details-modal.active {
        display: block;
    }

    .book-details-modal h3 {
        margin-bottom: 10px;
        font-size: 1.5rem;
    }

    .book-details-modal p {
        margin-bottom: 10px;
        font-size: 1rem;
    }

    .book-details-modal button {
        background-color: #8bc34a;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 1rem;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .book-details-modal button:hover {
        background-color: #7cb342;
    }

    .book-details-modal .edit-book-button {
        background-color: #ffc107;
    }

    .book-details-modal .delete-book-button {
        background-color: #f44336;
    }

    /* Close Button (X) */
    .close-modal-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #333;
        cursor: pointer;
        transition: color 0.2s;
    }

    .close-modal-btn:hover {
        color: #f44336;
    }


    /*Profile*/
    .profile-container {
        background-color: #fff;
        width: 100%;
        max-width: 600px;
        padding: 30px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .profile-title {
        text-align: center;
        font-size: 2rem;
        margin: 1rem 0;
        color: #333;
        font-weight: bold;
        padding: 10px;
        position: relative;
        top: 3.5%;
    }

    .profile-form {
        display: flex;
        flex-direction: column;
    }

    .input-group {
        margin-bottom: 20px;
    }

    .input-group label {
        display: block;
        margin-bottom: 5px;
        font-size: 0.9rem;
        color: #666;
    }

    .input-wrapper {
        display: flex;
        align-items: center;
        position: relative;
    }

    .input-wrapper input {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        color: #333;
    }

    .error {
        color: red;
        font-size: 0.9rem;
        margin-top: 5px;
    }

    .button-group button {
        background-color: #9FC880;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
    }

    .success-message {
        position: absolute;
        top: 85vh;
        left: 45vw;
        background-color: rgb(255, 255, 255);
        /* Soft red background */
        color: rgb(2, 139, 2);
        /* Dark red text */
        border: 1px solidrgb(255, 254, 254);
        /* Light red border */
        border-radius: 8px;
        /* Rounded corners */
        padding: 10px 15px;
        /* Inner padding */
        max-width: 400px;
        /* Limit width */
        margin: 10px auto;
        /* Center the message */
        font-family: 'Arial', sans-serif;
        /* Modern font */
        font-size: 14px;
        /* Subtle text size */
        text-align: center;
        /* Center the text */
        opacity: 0;
        /* Start as invisible */
        visibility: hidden;
        /* Hide it initially */
        animation: fadeInOut 5s ease-in-out forwards;
        /* Animation for fade-in and fade-out */
        z-index: 4;
    }

    @keyframes fadeInOut {
        0% {
            opacity: 0;
            visibility: hidden;
        }

        10% {
            opacity: 1;
            visibility: visible;
        }

        90% {
            opacity: 1;
        }

        100% {
            opacity: 0;
            visibility: hidden;
        }
    }

    .error-message {
        position: absolute;
        top: 85vh;
        left: 40vw;
        background-color: #f8d7da;
        /* Soft red background */
        color: #721c24;
        /* Dark red text */
        border: 1px solid #f5c6cb;
        /* Light red border */
        border-radius: 8px;
        /* Rounded corners */
        padding: 10px 15px;
        /* Inner padding */
        max-width: 400px;
        /* Limit width */
        margin: 10px auto;
        /* Center the message */
        font-family: 'Arial', sans-serif;
        /* Modern font */
        font-size: 14px;
        /* Subtle text size */
        text-align: center;
        /* Center the text */
        opacity: 0;
        /* Start as invisible */
        visibility: hidden;
        /* Hide it initially */
        animation: fadeInOut 5s ease-in-out forwards;
        /* Animation for fade-in and fade-out */
        z-index: 4;
    }

    @keyframes fadeInOut {
        0% {
            opacity: 0;
            visibility: hidden;
        }

        10% {
            opacity: 1;
            visibility: visible;
        }

        90% {
            opacity: 1;
        }

        100% {
            opacity: 0;
            visibility: hidden;
        }
    }
    </style>
</head>

<body>
    <div class="landing">
        <?php if (!empty($success_message)): ?>
        <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <p style="text-align:center; color: black;font-size: 62px;padding:25px;font-weight:bold">StackRead</p>

        <!-- container -> auth-container sa css -->
        <div class="auth-container" id="container">

            <div class="panel left" id="sign-in-panel">
                <div class="form-container">
                    <h2>Sign in</h2>
                    <form action="" method="POST" onsubmit="handleSubmit(event)">
                        <input type="hidden" name="action" value="login">
                        <div class="form-group">
                            <input type="text" name="username" placeholder="Username" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                        <button type="submit" onclick="toggleZindex()">Log in</button>
                    </form>
                </div>
            </div>

            <div class="panel right" id="log-in-panel">
                <div class="form-container">
                    <h2>Create Account</h2>
                    <form action="" method="POST">
                        <input type="hidden" name="action" value="signup">
                        <div class="form-group">
                            <input type="text" name="username" placeholder="Username" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                        <button type="submit">Sign up</button>
                    </form>
                </div>
            </div>

        </div>


    </div>
    </div>
    </div>

    <nav>
        <h1>StackRead</h1>
        <ul>
            <li><a onclick="navigateTo(0)">Home</a>
            <li><a onclick="navigateTo(1)">Marketplace</a>
            <li><a onclick="navigateTo(2)">Literary Hits</a>
            <li><a onclick="navigateTo(3)">Library</a>
            <li><a onclick="navigateTo(4)">Profile</a>
        </ul>
    </nav>

    <!--HOME-->
    <div class="outerContainer">
        <div id="home" class="section">
            <div class="fullscreen-section" id="fullscreen">

                <header class="header" id="home-header">
                    <h1>Welcome, User!</h1>
                    <div class="search-bar-container">
                        <div class="search-bar">
                            <input type="text" placeholder="Search">
                            <button>
                                <img src="https://img.icons8.com/material-outlined/24/search.png" alt="Search">
                            </button>
                        </div>
                        <div class="cart-icon">
                            <img src="https://img.icons8.com/material-outlined/24/shopping-cart.png" alt="Cart">
                        </div>
                    </div>
                </header>

                <div class="content">
                    <!-- Favorite Books Section -->
                    <div class="home-section">
                        <h2 class="shelf-label">Favorite books</h2>

                        <div class="favorite-books-list">
                            <div class="favorite-book-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">The Great Gatsby</p>
                                        <p class="book-author">F. Scott Fitzgerald</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/color/24/filled-like.png" class="favorite-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="favorite-book-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">The Great Gatsby</p>
                                        <p class="book-author">F. Scott Fitzgerald</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/color/24/filled-like.png" class="favorite-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="favorite-book-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">The Great Gatsby</p>
                                        <p class="book-author">F. Scott Fitzgerald</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/color/24/filled-like.png" class="favorite-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="favorite-book-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">The Great Gatsby</p>
                                        <p class="book-author">F. Scott Fitzgerald</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/color/24/filled-like.png" class="favorite-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="favorite-book-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">The Great Gatsby</p>
                                        <p class="book-author">F. Scott Fitzgerald</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/color/24/filled-like.png" class="favorite-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="favorite-book-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">The Great Gatsby</p>
                                        <p class="book-author">F. Scott Fitzgerald</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/color/24/filled-like.png" class="favorite-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="favorite-book-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">The Great Gatsby</p>
                                        <p class="book-author">F. Scott Fitzgerald</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/color/24/filled-like.png" class="favorite-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="favorite-book-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">The Great Gatsby</p>
                                        <p class="book-author">F. Scott Fitzgerald</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/color/24/filled-like.png" class="favorite-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="favorite-book-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">The Great Gatsby</p>
                                        <p class="book-author">F. Scott Fitzgerald</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/color/24/filled-like.png" class="favorite-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="favorite-book-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">The Great Gatsby</p>
                                        <p class="book-author">F. Scott Fitzgerald</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/color/24/filled-like.png" class="favorite-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="favorite-book-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">The Great Gatsby</p>
                                        <p class="book-author">F. Scott Fitzgerald</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/color/24/filled-like.png" class="favorite-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="favorite-book-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">The Great Gatsby</p>
                                        <p class="book-author">F. Scott Fitzgerald</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/color/24/filled-like.png" class="favorite-action-icon"
                                    alt="Heart">
                            </div>
                        </div>
                    </div>

                    <!-- Last Read Section -->
                    <div class="home-section">
                        <h2>Last read</h2>
                        <div class="last-read-list">
                            <div class="last-read-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">To Kill a Mockingbird</p>
                                        <p class="book-author">Harper Lee</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="last-read-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">To Kill a Mockingbird</p>
                                        <p class="book-author">Harper Lee</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="last-read-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">To Kill a Mockingbird</p>
                                        <p class="book-author">Harper Lee</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="last-read-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">To Kill a Mockingbird</p>
                                        <p class="book-author">Harper Lee</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="last-read-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">To Kill a Mockingbird</p>
                                        <p class="book-author">Harper Lee</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="last-read-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">To Kill a Mockingbird</p>
                                        <p class="book-author">Harper Lee</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="last-read-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">To Kill a Mockingbird</p>
                                        <p class="book-author">Harper Lee</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="last-read-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">To Kill a Mockingbird</p>
                                        <p class="book-author">Harper Lee</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="last-read-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">To Kill a Mockingbird</p>
                                        <p class="book-author">Harper Lee</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="last-read-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">To Kill a Mockingbird</p>
                                        <p class="book-author">Harper Lee</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                                    alt="Heart">
                            </div>
                            <div class="last-read-item">
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">To Kill a Mockingbird</p>
                                        <p class="book-author">Harper Lee</p>
                                    </div>
                                </div>
                                <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                                    alt="Heart">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--MARKETPLACE-->
        <div id="marketplace" class="section">

            <header class="header">
                <h1>Marketplace</h1>
                <div class="search-bar-container">
                    <div class="search-bar">
                        <input type="text" placeholder="Search">
                        <button>
                            <img src="https://img.icons8.com/material-outlined/24/search.png" alt="Search">
                        </button>
                    </div>
                    <div class="cart-icon">
                        <img src="https://img.icons8.com/material-outlined/24/shopping-cart.png" alt="Cart">
                    </div>
                </div>
            </header>

            <div class="full-width-container" id="marketplace-container">

                <div class="scrollable-div" id="bookList">
                    <div class="last-read-item">
                        <div class="book-info">
                            <div class="book-icon"></div>
                            <div class="book-details">
                                <p class="book-title">To Kill a Mockingbird</p>
                                <p class="book-author">Harper Lee</p>
                            </div>
                        </div>
                        <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                            alt="Heart">
                    </div>
                    <div class="last-read-item">
                        <div class="book-info">
                            <div class="book-icon"></div>
                            <div class="book-details">
                                <p class="book-title">To Kill a Mockingbird</p>
                                <p class="book-author">Harper Lee</p>
                            </div>
                        </div>
                        <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                            alt="Heart">
                    </div>
                    <div class="last-read-item">
                        <div class="book-info">
                            <div class="book-icon"></div>
                            <div class="book-details">
                                <p class="book-title">To Kill a Mockingbird</p>
                                <p class="book-author">Harper Lee</p>
                            </div>
                        </div>
                        <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                            alt="Heart">
                    </div>
                    <div class="last-read-item">
                        <div class="book-info">
                            <div class="book-icon"></div>
                            <div class="book-details">
                                <p class="book-title">To Kill a Mockingbird</p>
                                <p class="book-author">Harper Lee</p>
                            </div>
                        </div>
                        <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                            alt="Heart">
                    </div>
                    <div class="last-read-item">
                        <div class="book-info">
                            <div class="book-icon"></div>
                            <div class="book-details">
                                <p class="book-title">To Kill a Mockingbird</p>
                                <p class="book-author">Harper Lee</p>
                            </div>
                        </div>
                        <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                            alt="Heart">
                    </div>
                    <div class="last-read-item">
                        <div class="book-info">
                            <div class="book-icon"></div>
                            <div class="book-details">
                                <p class="book-title">To Kill a Mockingbird</p>
                                <p class="book-author">Harper Lee</p>
                            </div>
                        </div>
                        <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                            alt="Heart">
                    </div>
                    <div class="last-read-item">
                        <div class="book-info">
                            <div class="book-icon"></div>
                            <div class="book-details">
                                <p class="book-title">To Kill a Mockingbird</p>
                                <p class="book-author">Harper Lee</p>
                            </div>
                        </div>
                        <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                            alt="Heart">
                    </div>
                </div>
            </div>


        </div>
        <!--LITERARYHITS-->
        <div id="literaryhits" class="section">

            <header class="header">
                <h1>Literary Hits</h1>
                <div class="search-bar-container">
                    <div class="search-bar">
                        <input type="text" placeholder="Search">
                        <button>
                            <img src="https://img.icons8.com/material-outlined/24/search.png" alt="Search">
                        </button>
                    </div>
                    <div class="cart-icon">
                        <img src="https://img.icons8.com/material-outlined/24/shopping-cart.png" alt="Cart">
                    </div>
                </div>
            </header>

            <div class="full-width-container" id="literary-container">
                < class="scrollable-div" id="anotherbookList">
                    <div class="last-read-item">
                        <div class="book-info">
                            <div class="book-icon"></div>
                            <div class="book-details">
                                <p class="book-title">To Kill a Mockingbird</p>
                                <p class="book-author">Harper Lee</p>
                            </div>
                        </div>
                        <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                            alt="Heart">
                    </div>
                    <div class="last-read-item">
                        <div class="book-info">
                            <div class="book-icon"></div>
                            <div class="book-details">
                                <p class="book-title">To Kill a Mockingbird</p>
                                <p class="book-author">Harper Lee</p>
                            </div>
                        </div>
                        <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                            alt="Heart">
                    </div>
                    <div class="last-read-item">
                        <div class="book-info">
                            <div class="book-icon"></div>
                            <div class="book-details">
                                <p class="book-title">To Kill a Mockingbird</p>
                                <p class="book-author">Harper Lee</p>
                            </div>
                        </div>
                        <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                            alt="Heart">
                    </div>
                    <div class="last-read-item">
                        <div class="book-info">
                            <div class="book-icon"></div>
                            <div class="book-details">
                                <p class="book-title">To Kill a Mockingbird</p>
                                <p class="book-author">Harper Lee</p>
                            </div>
                        </div>
                        <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                            alt="Heart">
                    </div>
                    <div class="last-read-item">
                        <div class="book-info">
                            <div class="book-icon"></div>
                            <div class="book-details">
                                <p class="book-title">To Kill a Mockingbird</p>
                                <p class="book-author">Harper Lee</p>
                            </div>
                        </div>
                        <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                            alt="Heart">
                    </div>
                    <div class="last-read-item">
                        <div class="book-info">
                            <div class="book-icon"></div>
                            <div class="book-details">
                                <p class="book-title">To Kill a Mockingbird</p>
                                <p class="book-author">Harper Lee</p>
                            </div>
                        </div>
                        <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                            alt="Heart">
                    </div>
                    <div class="last-read-item">
                        <div class="book-info">
                            <div class="book-icon"></div>
                            <div class="book-details">
                                <p class="book-title">To Kill a Mockingbird</p>
                                <p class="book-author">Harper Lee</p>
                            </div>
                        </div>
                        <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon"
                            alt="Heart">
                    </div>
            </div>
        </div>
    </div>




    <!--LIBRARY-->
    <div id="library" class="section">

        <header class="header">
            <h1>Library</h1>
            <div class="search-bar-container">
                <div class="search-bar">
                    <input type="text" placeholder="Search">
                    <button>
                        <img src="https://img.icons8.com/material-outlined/24/search.png" alt="Search">
                    </button>
                </div>
                <div class="cart-icon">
                    <img src="https://img.icons8.com/material-outlined/24/shopping-cart.png" alt="Cart">
                </div>
            </div>
        </header>

        <div class="book-details-modal" id="bookDetailsModal">
            <button class="close-modal-btn" onclick="closeBookDetailsModal()">×</button>
            <h3 id="bookDetailsTitle"></h3>
            <p id="bookDetailsAuthor"></p>
            <button class="edit-book-button" onclick="initiateEditBook()">Edit</button>
            <button class="delete-book-button" onclick="initiateDeleteBook()">Delete</button>
        </div>

        <div class="full-width-container" id="library-container">
            <div class="add-book-section">
                <input type="text" id="bookTitle" placeholder="Book title" />
                <input type="text" id="bookAuthor" placeholder="Author" />
                <button onclick="addBook()">Add Book</button>
            </div>
            <div class="scrollable-div" id="anotherbookList">
                <div class="last-read-item">
                    <div class="book-info">
                        <div class="book-icon"></div>
                        <div class="book-details">
                            <p class="book-title">To Kill a Mockingbird</p>
                            <p class="book-author">Harper Lee</p>
                        </div>
                    </div>
                    <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon" alt="Heart">
                </div>
                <div class="last-read-item">
                    <div class="book-info">
                        <div class="book-icon"></div>
                        <div class="book-details">
                            <p class="book-title">To Kill a Mockingbird</p>
                            <p class="book-author">Harper Lee</p>
                        </div>
                    </div>
                    <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon" alt="Heart">
                </div>
                <div class="last-read-item">
                    <div class="book-info">
                        <div class="book-icon"></div>
                        <div class="book-details">
                            <p class="book-title">To Kill a Mockingbird</p>
                            <p class="book-author">Harper Lee</p>
                        </div>
                    </div>
                    <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon" alt="Heart">
                </div>
                <div class="last-read-item">
                    <div class="book-info">
                        <div class="book-icon"></div>
                        <div class="book-details">
                            <p class="book-title">To Kill a Mockingbird</p>
                            <p class="book-author">Harper Lee</p>
                        </div>
                    </div>
                    <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon" alt="Heart">
                </div>
                <div class="last-read-item">
                    <div class="book-info">
                        <div class="book-icon"></div>
                        <div class="book-details">
                            <p class="book-title">To Kill a Mockingbird</p>
                            <p class="book-author">Harper Lee</p>
                        </div>
                    </div>
                    <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon" alt="Heart">
                </div>
                <div class="last-read-item">
                    <div class="book-info">
                        <div class="book-icon"></div>
                        <div class="book-details">
                            <p class="book-title">To Kill a Mockingbird</p>
                            <p class="book-author">Harper Lee</p>
                        </div>
                    </div>
                    <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon" alt="Heart">
                </div>
                <div class="last-read-item">
                    <div class="book-info">
                        <div class="book-icon"></div>
                        <div class="book-details">
                            <p class="book-title">To Kill a Mockingbird</p>
                            <p class="book-author">Harper Lee</p>
                        </div>
                    </div>
                    <img src="https://img.icons8.com/ios-filled/24/like.png" class="last-read-action-icon" alt="Heart">
                </div>

            </div>


        </div>
    </div>

    <!--PROFILE-->
    <div id="profile" class="section">

        <div class="profile-container">
            <h1 class="profile-title">Edit Profile</h1>
            <?php
        if (!empty($success_message)) {
            echo "<p class='success'>$success_message</p>";
        }
        if (!empty($error_message)) {
            echo "<p class='error'>$error_message</p>";
        }
        ?>

            <!--PROFILE-->
            <form id="profileForm" method="POST">
                <input type="hidden" name="action" value="edit_profile">
                <div class="input-group">
                    <label for="username">Username</label>
                    <div class="input-wrapper">
                        <input type="text" id="username" name="username" placeholder="Enter your username" required
                            value="<?php echo htmlspecialchars($_SESSION['user']); ?>" />
                    </div>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter new password"
                            required />
                    </div>
                </div>

                <div class="input-group">
                    <label for="confirm-password">Confirm Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="confirm-password" name="confirm-password"
                            placeholder="Confirm new password" required />
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit">Save Changes</button>
                </div>
            </form>
            <button onclick="toggleZIndex()"> Log Out</button>
        </div>
    </div>

    <script>
    const outerContainer = document.querySelector('.outerContainer');
    const signInPanel = document.getElementById("sign-in-panel");
    const logInPanel = document.getElementById("log-in-panel");
    const container = document.getElementById("container");
    const fullscreen = document.getElementById("fullscreen");

    let scrollPosition = 0;

    function navigateTo(index) {
        outerContainer.style.transform = `translateX(-${index * 100}vw)`;
    }

    function dimPanel(panelToDim, panelToUndim) {
        panelToDim.classList.add("dimmed");
        panelToUndim.classList.remove("dimmed");

        const inputs = panelToDim.querySelectorAll("input, button");
        inputs.forEach(input => input.disabled = true);

        const activeInputs = panelToUndim.querySelectorAll("input, button");
        activeInputs.forEach(input => input.disabled = false);
    }


    signInPanel.addEventListener("mouseenter", () => {
        dimPanel(logInPanel, signInPanel);
    });

    logInPanel.addEventListener("mouseenter", () => {
        dimPanel(signInPanel, logInPanel);
    });

    let isDiv1OnTop = true;

    function toggleZIndex() {
        const div1 = document.querySelector('.landing');
        const div2 = document.querySelector('.outerContainer');

        if (isDiv1OnTop) {
            div1.style.opacity = 0;
            div2.style.opacity = 1;
            setTimeout(() => {
                div1.style.zIndex = 0;
                div2.style.zIndex = 1;
                div1.classList.remove('on-top');
                div2.classList.add('on-top');
            }, 1000);
        } else {
            navigateTo(0)
            div1.style.opacity = 1;
            div2.style.opacity = 0;
            div1.style.zIndex = 3;
            div2.style.zIndex = 0;
            div1.classList.add('on-top');
            div2.classList.remove('on-top');
        }

        isDiv1OnTop = !isDiv1OnTop;
    }

    function addBook() {
        const title = document.getElementById("bookTitle").value;
        const author = document.getElementById("bookAuthor").value;

        if (title && author) {
            fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'create',
                        title: title,
                        author: author,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        document.getElementById("bookTitle").value = "";
                        document.getElementById("bookAuthor").value = "";
                        fetchBooks();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while adding the book');
                });
        } else {
            alert("Please fill in both fields.");
        }
    }

    function fetchBooks() {
        fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'fetch'
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && Array.isArray(data.books)) {
                    const bookList = document.getElementById("bookList");
                    bookList.innerHTML = "";

                    data.books.forEach(book => {
                        const bookItem = document.createElement("div");
                        bookItem.classList.add("book-item");

                        bookItem.innerHTML = `
                                <div class="book-info">
                                    <div class="book-icon"></div>
                                    <div class="book-details">
                                        <p class="book-title">${book.title}</p>
                                        <p class="book-author">${book.author}</p>
                                    </div>
                                </div>
                                <button class="buy-button">
                                    <span>&#128722;</span>
                                </button>
                            `;
                        bookList.appendChild(bookItem);
                    });
                } else {
                    alert(data.message || 'Failed to fetch books');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while fetching books');
            });
    }

    function openBookDetailsModal(title, author) {
        selectedBook = {
            title,
            author
        };
        document.getElementById("bookDetailsTitle").textContent = title;
        document.getElementById("bookDetailsAuthor").textContent = author;
        document.getElementById("bookDetailsModal").classList.add("active");
    }
    window.onload = () => {
        fetchBooks();
    };
    </script>
</body>

</html>

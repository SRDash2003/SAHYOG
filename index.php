INDEX


<?php
//include 'includes/header.php';
include 'includes/database.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Welcome to SAHYOG</title>
  <link rel="stylesheet" href="assets/style.css" />
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      
      
      padding: 0;
      height: 100%;
      margin: 0;
      font-family: Arial, sans-serif;
      background: url('assets/blu3.jpeg') no-repeat center center fixed;
      background-size: cover;
      background-color: bisque;
    }
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: rgb(218, 231, 255);
      padding: 10px 20px;
      color: white;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
    }

    .navbar img {
      height: 50px;
      width: auto;
      object-fit: contain;

     
    }

    .navbar img:hover{
      transform: scale(1.5);
    }
    .tree{
       height: 50px;
       
    }
    .nvt{
      height: 50px;
      width: auto;
      object-fit: contain;
      height: 23px;
      font-size: larger;

    }
    .logo-container {
      display: flex;
      align-items: center;
    }
    

    .navbar a {
      color: rgb(17, 14, 91);
      margin-left: 22px;
      text-decoration: none;
      font-weight: bold;
    }

    .navbar a:hover {
      text-decoration: underline;
    }


    .container {
  max-width: 780px;
  margin: 5% auto;
  padding: 5px 40px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  background-color: rgb(239, 247, 255);

  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  min-height: 40vh;
  text-align: center;
}

    h1 {
      color: #0056b3;
      margin-bottom: 10px;
      font-size: 2.5rem;
      
    }

    p {
      font-size: 1.1rem;
      color: #333;
    }

    .button {
      display: inline-block;
      margin: 12px 8px;
      padding: 10px 24px;
      background-color: #007BFF;
      color: #fff;
      border: none;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .button:hover {
      background-color: #0056b3;
      transition: transform 0.3s ease;
      transform: scale(1.1);
    }
    .blog {
      margin-top: 40px;
      text-align: left;
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
      padding: 20px;
      border-radius: 12px;
      background-color: #dae7ff;
      
      
    }

    .blog h2 {
      color: #0056b3;
    }

    .blog p {
      color: black;
      line-height: 1.6;
    }
    .image-row {
      display: flex;
      justify-content: center; /* center images */
      gap: 15px; /* space between images */
      flex-wrap: wrap; /* wrap on small screens */
      margin-top: 20px;
    }
    
    .image-row img {
      width: 323px; /* adjust as needed */
      height: auto;
      border-radius: 8px;
      transition: transform 0.3s ease;
    }
    
    .image-row img:hover {
      transform: scale(1.05); /* slightly grow on hover */
    }

  
    .scrolling-wrapper {
      width: 80%;
      overflow: hidden;
      white-space: nowrap;
      box-sizing: border-box;
      padding: 10px 0;
    }
    
    .scrolling-track {
      display: inline-block;
      white-space: nowrap;
      animation: ticker 30s linear infinite;
    }
    
    .scrolling-track span {
      display: inline-block;
      margin-right: 83.4px; /* big space between messages */
      font-size: 2.5rem;
      color:  #0056b3;;
      font-weight: bold;
    }
    
    @keyframes ticker {
      0% {
        transform: translateX(0%);
      }
      100% {
        transform: translateX(-50%);
      }
    }
  </style>


</head>
<body>
   <!-- Navigation Bar -->
   <div class="navbar">
    <div class="tree">
    <img src="assets/logo.png" alt="Sahyog Logo" >
    </div>
    
  
   
  
    <div class="scrolling-wrapper">
      <div class="scrolling-track">
        <span>Welcome to SAHYOG</span>
        <span>Welcome to SAHYOG</span>
        <span>Welcome to SAHYOG</span>
        <span>Welcome to SAHYOG</span>


        <span>Welcome to SAHYOG</span>
        <span>Welcome to SAHYOG</span>
        <span>Welcome to SAHYOG</span>
        <span>Welcome to SAHYOG</span>
      </div>
    </div>
    <div class="nvt">
      <a href="index.php">Home</a>
     
      <a href="#blog">Blog</a>
    </div>
  </div>
  <div class="container">
    
    <h1>Welcome to <span style="color:#007BFF;">SAHYOG</span></h1>
    <p>Your gateway to giving and receiving help!</p>

    <?php if (isset($_SESSION['user_id'])): ?>
      <p>You are logged in as <strong><?= htmlspecialchars($_SESSION['name']); ?></strong>.</p>

      <?php
      $role = $_SESSION['role'];
      $dashboardLink = "dashboard/{$role}/{$role}.php";
      ?>
      <a href="<?= $dashboardLink ?>" class="button">Go to Dashboard</a>
    <?php else: ?>
      <a href="login.php" class="button">Login</a>
      <a href="register.php" class="button">Register</a>
    <?php endif; ?>

  
  </div>
  <!-- Blog Section -->
  <div class="blog" id="blog">
    <h2>Why Donations Matter ?</h2>
    <p>
      Every donation, big or small, has the power to transform lives. At SAHYOG, we believe in connecting those in need with those willing to help.
      Whether it's providing meals, supporting education, or aiding during disasters—your contribution makes a real difference.
    </p>
    <p>
      Read our stories of hope, resilience, and the incredible power of community giving. Together, we can build a better, more compassionate world.
    </p>
  </div>
  <div class="image-row">
    <img src="assets/boys.avif" alt="Description 1">
    <img src="assets/food.jpeg" alt="Description 2">
    <img src="assets/kamal.jpeg" alt="Description 3">
    <img src="assets/foren.jpeg" alt="Description 4">
  </div>
  <br><br><br><br><br>
</body>
</html>

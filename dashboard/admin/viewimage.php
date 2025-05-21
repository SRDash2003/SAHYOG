<?php
include '../../includes/database.php';
$id = $_GET['id'];

$sql = "SELECT * FROM users WHERE id = $id";
$result = mysqli_query($conn, $sql);


    $row =  mysqli_fetch_assoc($result);
?>

<?php if ($row['role'] === 'receiver' && !empty($row['org_doc'])) { ?>
    <img src="../../<?php    echo $row['org_doc']; ?>">
<?php } 
     else {?>
        <img src="../../<?php    echo $row['gov_id_file']; ?>">
   <?php  } ?>





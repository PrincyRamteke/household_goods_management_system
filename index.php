<?php
    session_start();
    if(!$_SESSION['loggedin'] || $_SESSION['loggedin'] != true) {
        header("location: login.php");
        exit;
    }
    require 'db_connect.php';
    $insert = false;
    $delete = false;
    $items = array();
    $userName = $_SESSION['userName'];
    if(isset($_POST['name'])) {
        $name = $_POST['name'];
        $location = $_POST['location'];
        $shopName = $_POST['shopName'];
        $price = $_POST['price'];
        $billNumber = isset($_POST['billNumber']) ? $_POST['billNumber'] : "No bill received";
        $dateOfPurchase = $_POST['dateOfPurchase'];
        $shopNumber = isset($_POST['shopNumber']) ? $_POST['shopNumber'] : "No number found";
        $productQuantity = $_POST['quantity'];
        $validity = isset($_POST['validity']) ? $_POST['validity'] : "Validity not found";
        $somethingElse = isset($_POST['somethingElse']) ? $_POST['somethingElse'] : "No extra information";
        $destinationfile = "https://media.istockphoto.com/photos/empty-white-studio-room-abstract-background-picture-id1147521090?b=1&k=20&m=1147521090&s=170667a&w=0&h=z2Syz9Pwcb55xKIIbp08AFPJyVjZM28t5IZLyyhqV3k=";
        
        if(isset($_FILES['image'])) {
            $files = $_FILES['image'];
            $filename = $files['name'];
            $fileerror = $files['error'];
            $filetmp = $files['tmp_name'];
            $fileext = explode('.', $filename);
            $filecheck = strtolower(end($fileext));
            $fileextstored = array('png', 'jpg', 'jpeg');
            if(in_array($filecheck, $fileextstored) && !$fileerror) {
                $destinationfile = 'imagesUploaded/'.$filename;
                move_uploaded_file($filetmp, $destinationfile);
            }
        }

        $insert_sql = "INSERT INTO `items` (`userName`, `productName`, `productLocation`, `productShop`, `productPrice`, `productBillNo`, `productDate`, `productShopNumber`, `productDetails`, `productQuantity`, `validity`, `image` ) VALUES ('$userName', '$name', '$location', '$shopName', '$price', '$billNumber', '$dateOfPurchase', '$shopNumber', '$somethingElse', '$productQuantity', '$validity', '$destinationfile');";

        if($connection->query($insert_sql) == TRUE) {
            $insert = true;
        }
    } 
    if(isset($_POST['delete'])) {
        $serialNo = $_POST['sno'];
        $serialNo = str_replace('/','',$serialNo);
        $insert_sql = "DELETE FROM `items` WHERE `serialNo` = '$serialNo'";

        if($connection->query($insert_sql) == TRUE) {
            $delete = true;
        } else {
            echo $connection->error;
        }
    }
    $get_items_sql = "SELECT * FROM `items` WHERE `userName` = '$userName' ORDER BY `productDate` DESC;";
    $result = $connection->query($get_items_sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            array_push($items, $row);
        }
    }
    if(isset($_POST['search'])) {
        $search = $_POST['search'];
        $search_sql = "SELECT * FROM `items` WHERE MATCH (`productName`,`productLocation`,`productShop`,`productDetails`) AGAINST ('$search') AND `userName` = '$userName' ORDER BY `productDate` DESC;";
        $result = $connection->query($search_sql);
        unset($items);
        $items = array();
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                array_push($items, $row);
            }
        }
    }
    if(isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header("location: login.php");
    }
    $connection->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Remember Things</title>
    <link rel="stylesheet" href="index.css" />
</head>

<body>
    <div class="container">
        <form class='logout' method="post">
            <input type='submit' name='logout' value='Logout' />
        </form>
        <form class='search' method="post">
            <input type="text" name="search" id="search" placeholder="Search">
            <button type="submit" class="btn">Search</button>
        </form>
        <h3>Welcome to your online house</h3>
        <p>Enter a new item you bought</p>
        <?php
                if($insert) {
                    echo "<p id='successMessage'>Item added successfully</p>";
                }
                if($delete) {
                    echo "<p id='dangerMessage'>Item delete successfully</p>";
                }
            ?>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="name" id="name" placeholder="What's the product name?" />
            <input type="text" name="location" id="location"
                placeholder="Where can you find the product in your house?" />
            <input type="text" name="shopName" id="shopName" placeholder="Where did you buy this product from?" />
            <input type="number" name="price" id="price" placeholder="What's the price?" />
            <input type="text" name="billNumber" id="billNumber" placeholder="What's the bill number" />
            <input type="date" name="dateOfPurchase" id="dateOfPurchase" placeholder="When did you buy the product?" />
            <input type="number" id="shopNumber" name="shopNumber" placeholder="What's shopkeepers number?" />
            <input type="number" id="quantity" name="quantity" placeholder="How many products did you buy?" />
            <input type="text" id="validity" name="validity" placeholder="Validity or warranty of the product?" />
            <input name="somethingElse" id="somethingElse" rows="10" placeholder="Something else about the product" />
            <input type="file" id="image" name="image">
            <div class="form_buttons">
                <button type="submit" class="btn">Add product</button>
                <button type="reset" class="btn">Reset details</button>
            </div>
        </form>
        <div class="items_container">
            <?php 
                    foreach($items as $row) {
                        echo "<div class='item'>
                            <img src=".$row['image'].">
                            <h4>".$row['productName']."</h4>
                            <p class='location'>".$row['productLocation']."</p>
                            <p class='shop'>Shop Details - <span>".$row['productShop']."</span> <span>(".$row['productShopNumber'].")</span></p>
                            <p class='shop'>Bill no. <span>".$row['productBillNo']."</span></p>
                            <p class='price'>₹".$row['productPrice']." - <span>".$row['productQuantity']."pieces</span></p>
                            <p class='date'>".$row['productDate']."</p>
                            <p class='details'>".$row['productDetails']."</p>
                            <form method='post'>
                                <input type='submit' name='delete' value='Delete'/>
                                <input id='serial_number' name='sno' value=".$row['serialNo']."/>
                            </form>
                        </div>";
                    }
                ?>
        </div>
    </div>
</body>

</html>
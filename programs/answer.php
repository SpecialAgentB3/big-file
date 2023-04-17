<?php
/***
// The choice_form on index.php is posted to this page. 
*/

/////////////////////////////////////////////////////////
// ENABLE Showing Errors to the user (helpful when debugging code) 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/////////////////////////////////////////////////////////
// COOKIE CHECKS
// Check to see if the unique_visitor_id cookie is set (it should have been set on index.php)
$unique_visitor_id = '';
if(isset($_COOKIE['unique_visitor_id']) ) {	
	$unique_visitor_id = $_COOKIE['unique_visitor_id'];
} 

$total_correct = 0; 
if(isset($_COOKIE['total_correct']) ) {	
	$total_correct = intval($_COOKIE['total_correct']);	
}

$total_incorrect = 0; 
if(isset($_COOKIE['total_incorrect']) ) {	
	$total_incorrect = intval($_COOKIE['total_incorrect']);	
} 

$money_saved = 0;
if (isset($_COOKIE['money_saved'])) {
    $money_saved = floatval($_COOKIE['money_saved']);
}

/////////////////////////////////////////////////////////
// DATABASE CONNECTION
$mysqli = new mysqli("localhost","chewilco_perceivedcost","QLmZUk?hldA0","chewilco_perceivedcost");
if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}


/////////////////////////////////////////////////////////
// SAVE POSTED ANSWER TO THE DATABASE
// if the page was submitted:
if (isset($_POST) && !empty($_POST['selected_item'])) {       

	// print_r($_POST);	

	$start_time =  (isset($_POST['start_time'])) ? intval($_POST['start_time']) : 0;	
	$response_time = time() - $start_time;		
	
	
	// info about prepared sql statements: (https://websitebeaver.com/prepared-statements-in-php-mysqli-to-prevent-sql-injection)
	$stmt = $mysqli->prepare("INSERT INTO responses (item1_id, item_1_actual_price, item_1_fake_price, item2_id, item_2_actual_price, item_2_fake_price, selected_item, response_time, ip_address, unique_visitor_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)" );
	$stmt->bind_param("iddiddiiss", 
		$_POST['item1_id'], 
		$_POST['item1_actual_price'], 
		$_POST['item1_fake_price'], 
		$_POST['item2_id'], 
		$_POST['item2_actual_price'], 
		$_POST['item2_fake_price'], 
		$_POST['selected_item'],
		$response_time,
		$_SERVER['REMOTE_ADDR'],
		$unique_visitor_id);
	// comment out when debugging:
	$stmt->execute();	
	$stmt->close();	  
	  
		
 
	
/////////////////////////////////////////////////////////
// DATABASE QUERY
// look up the same items from the products table:   
$stmt = $mysqli->prepare("SELECT * from products WHERE id IN (?,?) ");
$stmt->bind_param("ii", 
    $_POST['item1_id'],      
    $_POST['item2_id']);

$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows !== 2) exit('Database error (not returning 2 rows)');
while($row = $result->fetch_assoc()) {

  if ( $row['id'] == $_POST['item1_id'] ) {
    $item1_id           = $row['id'];
    $item1_name         = $row['name'];
    $item1_actual_price = $row['actual_price'];
    $item1_fake_price   = $_POST['item1_fake_price'];
    $item1_img_url      = $row['img_url'];
  } else {
    $item2_id           = $row['id'];
    $item2_name         = $row['name'];
    $item2_actual_price = $row['actual_price'];
    $item2_fake_price   = $_POST['item2_fake_price'];
    $item2_img_url      = $row['img_url'];        
  }     
}

if ( $item1_id == $_POST['selected_item'] ) { 
    // user selected item 1
    $selected_item_id = $item1_id;
    $selected_item_name = $item1_name;
    $selected_item_actual_price = $item1_actual_price;
    $selected_item_fake_price = $item1_fake_price;
    $selected_item_img_url = $item1_img_url;         
} else {
    $selected_item_id = $item2_id;
    $selected_item_name = $item2_name;
    $selected_item_actual_price = $item2_actual_price;
    $selected_item_fake_price = $item2_fake_price;
    $selected_item_img_url = $item2_img_url;         
}   

$stmt->close();
$result -> free_result();
	
	/////////////////////////////////////////////////////////
	// CALCULATE THE BETTER DEAL
	// determine the percent difference between actual and fake:
	// what percent lower is the offered price to the actual price?  
	// what if fake price is higher?  that will result in a NEGATIVE percent!
	// https://www.oracle.com/webfolder/technetwork/data-quality/edqhelp/Content/processor_library/matching/comparisons/percent_difference.htm
	
	$percent_difference1 = ( ($item1_actual_price - $item1_fake_price) / $item1_actual_price ) * 100;
	$percent_difference2 = ( ($item2_actual_price - $item2_fake_price) / $item2_actual_price ) * 100;	
	$selected_item_percent_diff = ( ($selected_item_actual_price - $selected_item_fake_price) / $selected_item_actual_price ) * 100;
	
		
	// if percent is < 0, BAD that means the fake price was MORE than the actual.  (bad deal)
	// if percent is > 0, GOOD: this means the fake price was less (clearly a better deal)... but by how much?
	
	$correct_choice = false;
	
	if ($percent_difference1 > $percent_difference2) {
		// item1 is the better deal!
		if ($selected_item_id == $item1_id) {
			// yay, you chose correctly: item1
			$correct_choice = true;
		} else {
			// sorry, you chose the worse deal: item2
			$correct_choice = false;
		}
	} else {
		// item2 is the better deal
		if ($selected_item_id == $item2_id) {
			// yay, you chose correctly: item2
			$correct_choice = true;
		} else {
			// sorry, you chose the worse deal: item1
			$correct_choice = false;
		}
	}

	if ($correct_choice){
		$answer_title = "<span style='color:green'>Correct!</span>";
		$total_correct ++;
		setcookie('total_correct', $total_correct, 2147483647, "/");  
	} else {
		$answer_title = "<span style='color:red'>Incorrect</span>";
		$total_incorrect ++;
		setcookie('total_incorrect', $total_incorrect, 2147483647, "/"); 
	}

    $percent_difference_difference = abs($percent_difference1 - $percent_difference2);
    $money_saved_current = $percent_difference_difference * $selected_item_actual_price / 100;
    if (!$correct_choice) {
        $money_saved_current = -$money_saved_current;
    }
    $money_saved += $money_saved_current;
    setcookie('money_saved', $money_saved, 2147483647, "/");

	$answer_subtext = "You Selected " . $selected_item_name . " for <span style='font-weight:bold'>$" . number_format($selected_item_fake_price,2) . "</span> <br>which is <span style='font-weight:bold'>" . number_format(abs($selected_item_percent_diff),0) . "%</span>" ;
	$answer_subtext .= ($selected_item_percent_diff > 0) ?  " LESS " : " MORE ";	
	$answer_subtext .= " than the actual price <span style='font-weight:bold'>$" . number_format($selected_item_actual_price,2) . "</span>";
	
  
	
}
else {	
	
	// nothing got posted to this page?  some kind of error.
	print("<div style='text-align:center; margin: 30px;width:100%; border: solid 5px #CCC'>Please make a selection on the <a href='index.php'>Choice Page</a> </div>");
	exit();
	
	
}

 
 

// close the db connection
$mysqli -> close();



?><!DOCTYPE html>
<html lang="en">
<head>
  <title>Perceived Cost</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  
  <style>
  .choice-block {
	  text-align:center;
	  border:  dotted 2px #CCC;	    
  }
  .choice-img {	  
	border:  dotted 2px #CCC;	  
	min-width:150px;  
	border-radius: 20%;
	max-width:100%;
	max-height:100%;
	height: auto; 
  }
  
  .big-price {
	font-weight:bold;	 
	font-family:Georgia;
	text-align:center;	  
	font-size: 30px;
	font-size: 7vw;	  
  }  
    
  .big-price {
  	font-weight:bold;	 
	font-family:Georgia;
	text-align:center;	  	
	font-size: 6vw;	  
  }
  
.outer_img {
	position:relative;
}
 
.overlay_img {	
    position: absolute;
    top: 50%;
    left: 50%;
    transform:translate(-50%, -50%);
}
.img_check {
	width:60%;
	max-width:140px;
}

  </style>
  
</head>
<body>

<div class="jumbotron text-center">
  <h1>
    <?php echo $answer_title; ?>
  </h1>

  <h3><?php echo $answer_subtext; ?></h3>

  <br><br>

  <script>
    document.addEventListener("click", function() {
      window.location.href = "index.php";
    });
  </script>
</div>
  
<div class="container-fluid">
  <div class="row">    
    <div class="col-xs-6 " >   
      <div class=" choice-block" id="choice-block1" >	   	  
        <h3><?php echo $item1_name; ?></h3>	 
        <h3>Actual Price: <span style='font-weight:bold'>$<?php echo number_format($item1_actual_price,2); ?></span></h3>	
	  
        <?php if ($percent_difference1 > 0) { ?>
          <h4><span style='color:green'>(<?php echo number_format($percent_difference1,0); ?>% discount)</span></h4>
        <?php } else { ?>
          <h4><span style='color:red'>(<?php echo number_format(abs($percent_difference1),0); ?>% increase in price)</span></h4>
        <?php } ?>
		
        <div class="outer_img">
          <img src="<?php echo $item1_img_url; ?>" class="choice-img rounded">                    
          <?php if ($percent_difference1 > $percent_difference2) { ?>
            <div class="overlay_img">		
              <img src="img/green-check.png" class="img_check">    
            </div>
          <?php } ?>										  
        </div>	  
		
        <div class="big-price">
          $<?php echo number_format($item1_fake_price,2); ?>
        </div>			
      </div>
    </div>	
	  
    <div class="col-xs-6 " > 
      <div class=" choice-block" id="choice-block2" >		 
        <h3><?php echo $item2_name; ?></h3>  
        <h3>Actual Price: <span style='font-weight:bold'>$<?php echo number_format($item2_actual_price,2); ?></span></h3>	
		
        <?php if ($percent_difference2 > 0) { ?>
			<h4><span style='color:green'>(<?php echo number_format($percent_difference2,0); ?>% discount)</span></h4>
        <?php } else { ?>
			<h4><span style='color:red'>(<?php echo number_format(abs($percent_difference2),0); ?>% increase in price)</span></h4>
        <?php } ?>
				 
        <div class="outer_img">
          <img src="<?php echo $item2_img_url; ?>" class="choice-img rounded">                    
          <?php if ($percent_difference1 < $percent_difference2) { ?>
            <div class="overlay_img">							 
              <img src="img/green-check.png" class="img_check">    
            </div>
          <?php } ?>										  
        </div>	  

        <div class="big-price">	  
          $<?php echo number_format($item2_fake_price,2); ?>					
        </div>		 
		  
      </div>
    </div>
  </div>
</div>


   
  
  

  





<!-- Footer -->

<footer class="text-center text-lg-start bg-light text-muted" style='margin-top:20px;'>
    
	<hr>
  
    <div class="container text-center text-md-start mt-5">	
	  <!-- show stats IF they are available in a cookie: -->
		<div style="text-align:left; margin:10px; padding: 10px; border: solid 1px #CCC">
		 <h3>STATS</h3>
		 Total Correct: <?php echo $total_correct; ?><br>
		 Total Incorrect: <?php echo $total_incorrect; ?><br>
         Money Saved: $<?php echo number_format($money_saved, 2); ?><br>
		</div>
	<br/><br/>
  
    </div>
	
  <!-- Copyright -->
  <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
    © <?php echo date("Y"); ?> Copyright:
    <a class="text-reset fw-bold" href="">perceivedcost.com</a>
  </div>
  <!-- Copyright -->
</footer>
<!-- Footer -->


</body>
</html>
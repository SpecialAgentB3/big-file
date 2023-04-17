<?php
/***
// This PHP code is executed on the server first, before the actual html mark up text below is sent to the browser.
*/

/////////////////////////////////////////////////////////
// ENABLE Showing Errors to the user (helpful when debugging code) 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/////////////////////////////////////////////////////////
// COOKIE CHECK
// Check to see if the unique_visitor_id cookie is set.  

if (isset($_GET['action']) && ($_GET['action'] == 'clear_stats')) {	
	setcookie('unique_visitor_id', false,  time() - 3600, "/"); // erase value, set expiration in the past.
	setcookie('total_correct', false,  time() - 3600, "/"); // erase value, set expiration in the past.
	setcookie('total_incorrect', false,  time() - 3600, "/"); // erase value, set expiration in the past.
    setcookie('money_saved', false,  time() - 3600, "/"); // erase value, set expiration in the past.
	header("Location: index.php");
}

if(isset($_COOKIE['unique_visitor_id']) ) {
	// set it in this variable:
	$unique_visitor_id = $_COOKIE['unique_visitor_id'];	
} else {
	// otherwise, create it now and set the cookie
    $unique_visitor_id = guidv4();
    setcookie('unique_visitor_id', $unique_visitor_id, 2147483647, "/");
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
// DATABASE QUERY
// Get 2 random items from the products table:
$choices = [];


$stmt = $mysqli->prepare("SELECT * from products order by rand() limit 2");
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows !== 2) exit('Database error (not returning 2 rows)');
while($row = $result->fetch_assoc()) {
  $choices[] = $row;
}

$stmt->close();

	
	/*
	// Check the data that this query returned:
	print "<pre>";
	print "choices: \n-----------------\n";
	print_r($choices); 	
    
	$items = [];
    foreach($choices as $key => $val) {		
		print "\nValue of choices [ $key ] \n-----------------\n";
		print_r($val); 		
		$items[] = $val; // append it to the items array
    }    
	print "\n\nitems: \n-----------------\n";
	print_r($items); 		
	print "</pre>";
	//*/
    
 

// Set local variables with the choice data from the database:

$item1_id           = $choices[0]['id'];
$item1_name         = $choices[0]['name'];
$item1_actual_price = $choices[0]['actual_price'];
$item1_fake_price   = $choices[0]['actual_price'] * (rand(5000, 15000) / 10000);
$item1_img_url      = $choices[0]['img_url'];

$item2_id           = $choices[1]['id'];
$item2_name         = $choices[1]['name'];
$item2_actual_price = $choices[1]['actual_price'];
$item2_fake_price   = $choices[1]['actual_price'] * (rand(5000, 15000) / 10000);
$item2_img_url      = $choices[1]['img_url'];



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
	  cursor:pointer;	  
  }
  .choice-img {	  
	border:  dotted 2px #CCC;	  
	min-width:100px;  
	border-radius: 20%;
	max-width:100%;
	max-height:300px;
	height: auto; 
  }
  
  .big-price {
	font-weight:bold;	 
	font-family:Georgia;
	text-align:center;	  
	font-size: 30px;
	font-size: 7vw;	  
  }
  
  
  </style>
  
</head>
<body>

<div class="jumbotron text-center">
  <h1>Which price is a better deal?</h1>
  <p>Select the item below that you think represents the best value</p> 
</div>
  

<form action="answer.php" method="post" name="choice_form" id="choice_form" >  
  
<!-- These hidden form fields store the choice details from the database that will be submitted by the form -->
  
<input type="hidden" name="item1_id" value="<?php echo ($item1_id); ?>"/>
<input type="hidden" name="item1_actual_price" value="<?php echo ($item1_actual_price); ?>"/>
<input type="hidden" name="item1_fake_price" value="<?php echo ($item1_fake_price); ?>"/>

<input type="hidden" name="item2_id" value="<?php echo ($item2_id); ?>"/>
<input type="hidden" name="item2_actual_price" value="<?php echo ($item2_actual_price); ?>"/>
<input type="hidden" name="item2_fake_price" value="<?php echo ($item2_fake_price); ?>"/>

<input type="hidden" name="selected_item" id="selected_item" value=""/>   

<input type="hidden" name="start_time" value="<?php echo (time()); ?>"/>



<div class="container-fluid">
  <div class="row">    
    <div class="col-xs-6 " >   
    <div class=" choice-block" id="choice-block1" onclick="submitForm('<?php echo ($item1_id); ?>', 'choice-block1')" >	   	  
      <h3> <?php echo $item1_name; ?> </h3>	         
		<img src="<?php echo $item1_img_url; ?>" class="choice-img rounded" alt="<?php echo $item1_name; ?>">
			<div class="figure-caption big-price">
			  $<?php echo number_format($item1_fake_price,2);  ?>
			</div>			
    </div>
    </div>	
	<div class="col-xs-6 " > 
		<div class=" choice-block" id="choice-block2" onclick="submitForm('<?php echo ($item2_id); ?>', 'choice-block2')" >		 
		<h3>  <?php echo $item2_name; ?>  </h3>  
		  <img src="<?php echo $item2_img_url; ?>" class="choice-img rounded" alt="<?php echo $item2_name; ?>">
		  <div class="big-price">	  
				$<?php echo number_format($item2_fake_price,2);  ?>		
		  </div>		 
		</div>
	</div>
  </div>
</div>

</form>


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
		
       <a href='index.php?action=clear_stats'>
		<input type='button' value='Clear Stats'/>
	  </a>
	  <br/><br/>

		<!--
			<?php echo $unique_visitor_id; ?>
		-->
		
    </div>
  
  <!-- Copyright -->
  <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
    © <?php echo date("Y"); ?> Copyright:
    <a class="text-reset fw-bold" href="">perceivedcost.com</a>
  </div>
  <!-- Copyright -->
</footer>
<!-- Footer -->

<!-- Client side code that will make the form submit as soon as the item is clicked  -->
<script>
    function submitForm(selected_id, highlight_id) {
		
		var el = document.getElementById(highlight_id);
		el.style.border = 'solid 5px blue';
		
        document.getElementById("selected_item").value = selected_id;
        let form = document.getElementById("choice_form");
        form.submit();
    }
</script>



</body>
</html>
<?php

// helper functions

function guidv4($data = null) {
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}



?>
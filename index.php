<?php



session_start();
$product_ids= array();
//session_destroy();
//check if add to cart btn s submitted
if (filter_input(INPUT_POST, 'add_to_cart')) {
	if (isset($_SESSION['Shopping_cart'])) {
		//no of products in array
		$count= count($_SESSION['Shopping_cart']);
		//matching arrays to id
		$product_ids=array_column($_SESSION['Shopping_cart'],'id' );
		if (!in_array(filter_input(INPUT_GET, 'id'),$product_ids)) {
			$_SESSION['Shopping_cart'][$count]=array(
				'id' => filter_input(INPUT_GET, 'id'),
				'name' => filter_input(INPUT_POST, 'name'),
				'price' => filter_input(INPUT_POST, 'price'),
				'quantity' => filter_input(INPUT_POST, 'quantity')
 			);
		}
		else{
			for ($i=0 ; $i < count($product_ids) ; $i++ ) { 
				if ($product_ids[$i]== filter_input(INPUT_GET, 'id')) {
					//add item quantity to existing product
					$_SESSION['Shopping_cart'][$i]['quantity']+=filter_input(INPUT_POST, 'quantity');
				}
			}
		}
	}
	else{
		//if cart doesnt exist,
		$_SESSION['Shopping_cart'][0]=array(
				'id' => filter_input(INPUT_GET, 'id'),
				'name' => filter_input(INPUT_POST, 'name'),
				'price' => filter_input(INPUT_POST, 'price'),
				'quantity' => filter_input(INPUT_POST, 'quantity')
 			);
	}
}

if (filter_input(INPUT_GET, 'action')=='delete') {
	//loop through shopping cart until matches with id variable
	foreach ($_SESSION['Shopping_cart'] as $key => $product) {
		if ($product['id']==filter_input(INPUT_GET, 'id')) {
			//remove from cart if match with id
			unset($_SESSION['Shopping_cart'][$key]);
		}
	}
	//reset session array key so they match with product_ids numeric array
	$_SESSION['Shopping_cart']= array_values($_SESSION['Shopping_cart']);
}

//pre_r($_SESSION);

function pre_r($array){
	echo "<pre>";
	print_r($array);
	echo "</pre>";
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>SM-Tech Shopping cart</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="cart.css">
</head>
<body>

	<div class="container">
		<div class="row">
		<?php

		$con= mysqli_connect("localhost","root","","testt");
		$query= "SELECT * FROM product ORDER by id ASC";
		$result= mysqli_query($con,$query);
		if ($result): 
			if (mysqli_num_rows($result)>0): 
				while ($product= mysqli_fetch_assoc($result)): 
					//print_r($product);
					?>
					<div class="col-sm-4 col-md-3">
						<form method="post" action="index.php?action=add&id=<?php echo $product["id"];?>">
							<div class="products">
								<img class="img-fluid" src="img/<?php echo $product['image'];?>">
								<h4 class="text-info"><?php echo $product['name'];?></h4>
								<h4>$<?php echo $product['price'];?></h4>
								<input type="text" name="quantity" class="form-control" value="1">
								<input type="hidden" name="name" value="<?php echo $product['name'];?>">
								<input type="hidden" name="price" value="<?php echo $product['price'];?>">
								<input type="submit" name="add_to_cart" style="margin-top:5px;" class="btn btn-info" value="Add To Cart">
							</div>
						</form>
					</div>
					<?php
				endwhile;
			endif;
		endif;

		?>
			<div style="clear:both"></div>
			<br>
			<div class="table-responsive">
				<table class="table">
					<tr><th colspan="5"><h3>Order Details</h3></th></tr>
					<tr>
						<th width="40%">Product Name</th>
						<th width="10%">Quantity</th>
						<th width="20%">Price</th>
						<th width="15%">Total</th>
						<th width="5%">Action</th>
					</tr>
					<?php
					if (!empty($_SESSION['Shopping_cart'])):
						$total=0;
						foreach ($_SESSION['Shopping_cart'] as $key => $product) :
					?>
					<tr>
						<td><?php echo $product['name'];?></td>
						<td><?php echo $product['quantity'];?></td>
						<td>$<?php echo $product['price'];?></td>
						<td>$<?php echo number_format($product['quantity']*$product['price'],2);?></td>
						<td>
							<a href="index.php?action=delete&id=<?php echo $product['id'];?>">
								<div class="btn btn-danger">Remove</div>
							</a>
						</td>
					</tr>
					<?php
						$total = $total + ($product['quantity']*$product['price']);
						endforeach;
					?>
					<tr>
						<td colspan="3" align="right">Total</td>
						<td align="right">$<?php echo number_format($total,2);?></td>
						<td></td>
					</tr>
					<tr>
						<td colspan="5">
							<?php
							if (isset($_SESSION['Shopping_cart'])):
							if (count($_SESSION['Shopping_cart'])>0):
							?>
							<a href="#" class="button">Checkout</a>
							<?php endif; endif; ?>
						</td>
					</tr>
					<?php
					endif;
					?>
				</table>
			</div>
		</div>
	</div>
</body>
</html>

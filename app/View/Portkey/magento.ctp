<table>
	<tr>
		<td>Order ID</td>
		<td>Customer Name</td>
		<td>Customer Email</td>
		<td>Subtotal</td>
		<td>Quantity Ordered</td>
		<td>Order Status</td>
		<td>Order State</td>
		<td>Order Date</td>
	</tr>
<?php foreach ($orders['GoSalesOrder'] as $order): ?>
	<tr>
		<td><?php echo $order['increment_id'] ?></td>
		<td><?php echo $order['customer_firstname']?> <?php echo $order['customer_lastname']?></td>
		<td><?php echo $order['customer_email']?></td>
		<td><?php echo $order['subtotal']?></td>
		<td><?php echo $order['total_qty_ordered']?></td>
		<td><?php echo $order['status']?></td>
		<td><?php echo $order['state']?></td>
		<td><?php echo $order['created_at']?></td>
	</tr>
<?php endforeach; ?>
</table>
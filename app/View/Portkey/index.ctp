<?php echo $this->Html->script('jquery.jeditable.min'); ?>
<script type="text/javascript">
jQuery(function($){
	$('.jedit').editable('<?php echo $this->webroot ?>portkey/save', {
	         indicator : '<?php echo $this->Html->image('ajax-loader.gif') ?>',
	         tooltip   : 'Click to edit...'
	     });
});
</script>
<h2>Inventory List</h2>
<p>Combine list of inventory for PortKey and Magento. All items from the web are
	shown. Only item with Quantity > 0 are shown from PortKey.</p>
<p>The starred items are marked 
	as out of stock in Magento. Click on the Web Quantity number to update this 
	value. A dash in the Web Quantity means this SKU is not in PortKey.</p>
<table>
	<tr>
		<th>Part Number</th>
		<th>Description</th>
		<th>Web Quantity</th>
		<th>Quantity Available</th>
		<th>Quantity OnHold</th>
		<th>Quantity Allocated</th>
		<th>Quantity Required</th>
		<th>Quantity Scheduled</th>
		<th>Quantity Total</th>
	</tr>
<?php foreach ($combined as $d): ?>
	<?php if ($d['QuantityTotal'] > 0 or $d['QuantityTotal'] == -1): ?>
	<tr>
		<td><?php echo $d['PartNumber'] ?></td>
		<td>
			<?php echo empty($d['Description']) ? '<span style="color: grey">NOT IN PORTKEY</span>' : $d['Description'] ?>
			<?php if (isset($d['is_in_stock']) and $d['is_in_stock'] == 0):?><span style="color: red; font-weight: bold;">*</span><?php endif; ?>
		</td>
		<td style="text-align: right;">
			<?php if (isset($d['WebQuantity'])):?>
				<div class="jedit" id="<?php echo $d['product_id'] ?>"><?php echo number_format($d['WebQuantity']); ?></div>
			<?php else: ?>
				-
			<?php endif; ?>
		</td>
		<td style="text-align: right;"><?php echo number_format($d['Quantity']) ?></td>
		<td style="text-align: right;"><?php echo number_format($d['QuantityOnHold']) ?></td>
		<td style="text-align: right;"><?php echo number_format($d['QuantityAllocated']) ?></td>
		<td style="text-align: right;"><?php echo number_format($d['QuantityRequired']) ?></td>
		<td style="text-align: right;"><?php echo number_format($d['QuantityScheduled']) ?></td>
		<td style="text-align: right;"><?php echo number_format($d['QuantityTotal']) ?></td>
	</tr>
	<?php endif; ?>
<?php endforeach; ?>
</table>
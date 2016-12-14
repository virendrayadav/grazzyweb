<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.js');?>"></script>

<?php echo form_open_multipart($this->config->item('admin_folder').'/restaurant/form/'.$restaurant_id); ?>

<div class="tabbable">

	<ul class="nav nav-tabs">
		<li class="active"><a href="#description_tab" data-toggle="tab"><?php echo lang('description');?></a></li>
		<li><a href="#timings_tab" data-toggle="tab">Timings</a></li>
		<li><a href="#attributes_tab" data-toggle="tab">Image</a></li>
		<li><a href="#pitstop_tab" data-toggle="tab">Pitstop</a></li>
		<li><a href="#location_tab" data-toggle="tab">Location</a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active" id="description_tab">
			<fieldset style="padding:10px;">
				<div class="form-group">	
					<label for="name">Restaurant name</label>
					<?php
					$data	= array('name'=>'restaurant_name', 'value'=>set_value('restaurant_name', $restaurant_name), 'class'=>'form-control');
					echo form_input($data);
					?>
				</div>
				<div class="form-group">	
					<label for="restaurant_address">Restaurant address</label>
					<?php
					$data	= array('name'=>'restaurant_address', 'class'=>'', 'value'=>set_value('restaurant_address', $restaurant_address));
					echo form_textarea($data);
					?>
				</div>
				<div class="form-group">	
					<label for="restaurant_address">Restaurant phone</label>
					<?php
					$data	= array('name'=>'restaurant_phone', 'value'=>set_value('restaurant_phone', $restaurant_phone), 'class'=>'form-control');
					echo form_input($data);
					?>
				</div>
				<div class="form-group">
					<label for="restaurant_address">Restaurant email</label>
					<?php
					$data	= array('name'=>'restaurant_email', 'value'=>set_value('restaurant_email', $restaurant_email), 'class'=>'form-control');
					echo form_input($data);
					?>
				</div>
				<div class="form-group">
					<label for="restaurant_address">Restaurant branch</label>
					<?php
					$data	= array('name'=>'restaurant_branch', 'value'=>set_value('restaurant_branch', $restaurant_branch), 'class'=>'form-control');
					echo form_input($data);
					?>
				</div>
				<div class="form-group">
					<label for="restaurant_manager">Restaurant manager</label>
					<select name="restaurant_manager" class="form-control form-control">
						<option value="">Select Restaurant manager</option>
						<?php foreach($managers as $manager){
							if($restaurant_manager == $manager->id){$select="selected";}else{$select="";}?>
							<option value="<?=$manager->id?>" <?=$select;?>><?=$manager->username;?></select>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">	
					<label for="enabled"><?php echo lang('enabled');?> </label>
					<?php echo form_dropdown('enabled', array('0' => lang('disabled'), '1' => lang('enabled')), set_value('enabled',$enabled),'class="form-control"'); ?>
				</div>
				<div class="form-group" style="display:none;">	
					<label for="preparation_time">Preparation time(In mins)</label>
					<?php
					$data	= array('name'=>'preparation_time', 'value'=>set_value('preparation_time', $preparation_time), 'class'=>'form-control');
					echo form_input($data);
					?>
				</div>
				<div class="form-group">	
					<label for="commission">Commission(%)</label>	
					<?php
					$data	= array('name'=>'commission', 'value'=>set_value('commission', $commission), 'class'=>'form-control');
					echo form_input($data);
					?>
				</div>
				<div class="form-group">
					<label for="penalty">Penalty(%)</label>
					<?php
					$data	= array('name'=>'penalty', 'value'=>set_value('penalty', $penalty), 'class'=>'form-control');
					echo form_input($data);
					?>
				</div>
				<div class="form-group">
					<label for="servicetax">Service tax(%)</label>
					<?php
					$data	= array('name'=>'servicetax', 'value'=>set_value('servicetax', $servicetax), 'class'=>'form-control');
					echo form_input($data);
					?>
				</div>
			</fieldset>
		</div>
		
		<div class="tab-pane" id="timings_tab">
			<fieldset style="padding: 10px;">
				<div class="form-group">
					<label for="fromtime">From time</label>
					<input type="time" name="fromtime" class="form-control" value="<?=$fromtime;?>">
				</div>
				<div class="form-group">
					<label for="fromtime">To time</label>
					<input type="time" name="totime" class="form-control"  value="<?=$totime;?>">
				</div>
				<div class="form-group">
					<label for="days">Days</label>
					<br/><?php $dayss = unserialize($days);  ?>
					<input type="checkbox" name="days[]" value="all" class="days" onchange="checkAll(this)" <?php if(is_array($dayss) && in_array('all',$dayss)){ echo "checked=checked"; } ?>> All<br>
					<input type="checkbox" name="days[]" value="sunday" class="days" <?php if(is_array($dayss) && in_array('sunday',$dayss)){ echo "checked=checked"; } ?>> Sunday<br>
					<input type="checkbox" name="days[]" value="monday" class="days" <?php if(is_array($dayss) && in_array('monday',$dayss)){ echo "checked=checked"; } ?>> Monday<br>
					<input type="checkbox" name="days[]" value="tuesday" class="days" <?php if(is_array($dayss) && in_array('tuesday',$dayss)){ echo "checked=checked"; } ?>> Tuesday<br>
					<input type="checkbox" name="days[]" value="wednesday" class="days" <?php if(is_array($dayss) && in_array('wednesday',$dayss)){ echo "checked=checked"; } ?>> Wednesday<br>
					<input type="checkbox" name="days[]" value="thursday" class="days" <?php if(is_array($dayss) && in_array('thursday',$dayss)){ echo "checked=checked"; } ?>> Thursday<br>
					<input type="checkbox" name="days[]" value="friday" class="days" <?php if(is_array($dayss) && in_array('friday',$dayss)){ echo "checked=checked"; } ?>> Friday<br>
					<input type="checkbox" name="days[]" value="saturday" class="days" <?php if(is_array($dayss) && in_array('saturday',$dayss)){ echo "checked=checked"; } ?>> Saturday<br>
				</div>
				<script>
					function checkAll(ele) {
						 var checkboxes = document.getElementsByTagName('input');
						 if (ele.checked) {
							 for (var i = 0; i < checkboxes.length; i++) {
								 if (checkboxes[i].type == 'checkbox') {
									 checkboxes[i].checked = true;
								 }
							 }
						 } else {
							 for (var i = 0; i < checkboxes.length; i++) {
								 if (checkboxes[i].type == 'checkbox') {
									 checkboxes[i].checked = false;
								 }
							 }
						 }
					 }
				
				</script>
			</fieldset>
		</div>
		<div class="tab-pane" id="attributes_tab">
			
			<fieldset style="padding: 10px;">
				<div class="form-group">
					<label for="image"><?php echo lang('image');?> </label>
					<div class="input-append">
						<?php echo form_upload(array('name'=>'image','class'=>'form-control'));?><span class="add-on"><?php echo lang('max_file_size');?> <?php echo  $this->config->item('size_limit')/1024; ?>kb</span>
					</div>
				</div>
				<div class="form-group">		
					<?php if($restaurant_id && $image != ''):?>
					
					<div style="text-align:center; padding:5px; border:1px solid #ddd;"><img src="<?php echo base_url('uploads/images/small/'.$image);?>" alt="current"/><br/><?php echo lang('current_file');?></div>
					
					<?php endif;?>
				</div>	
			</fieldset>
			
		</div>
		
		<div class="tab-pane" id="pitstop_tab" style="padding: 10px;">
				<div class="row">
					<div class="col-sm-8">
						<label><strong>Select pitstops</strong></label>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-4" style="text-align:center">
						<div class="row">
							<div class="form-group">
								<input class="form-control" type="text" id="product_search" />
								<script type="text/javascript">
								$('#product_search').keyup(function(){
									$('#product_list').html('');
									run_product_query();
								});
						
								function run_product_query()
								{
									$.post("<?php echo site_url($this->config->item('admin_folder').'/restaurant/pitstops_autocomplete/');?>", { name: $('#product_search').val(), limit:10},
										function(data) {
									
											$('#product_list').html('');
									
											$.each(data, function(index, value){
									
												if($('#related_product_'+index).length == 0)
												{
													$('#product_list').append('<option id="product_item_'+index+'" value="'+index+'">'+value+'</option>');
												}
											});
									
									}, 'json');
								}
								</script>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<select class="form-control" id="product_list" size="10" style="margin:0px;"></select>
							</div>
						</div>
						<div class="row">
							<div class="form-group" style="margin-top:8px;">
								<a href="#" onclick="add_related_product();return false;" class="btn btn-primary" title="Add Related Product">Add Pitstop</a>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<table class="table table-striped" style="margin-top:10px;">
							<tbody id="product_items_container">
							<?php 
							foreach($related_pitstops as $rel)
							{
								echo related_items($rel->pitstop_id, $rel->pitstop_name);
							}
							?>
							</tbody>
						</table>
					</div>
				</div>
		</div>
	
		<div class="tab-pane" id="location_tab">
			<fieldset style="padding: 10px;">
				<label for="restaurant_address">Restaurant latitude</label>
				<input type="text" class="form-control" value="<?=$restaurant_latitude;?>" name="restaurant_latitude" id="lat">
				
				
				<label for="restaurant_address">Restaurant langitude</label>
				<input type="text" class="form-control" value="<?=$restaurant_langitude;?>" name="restaurant_langitude" id="lng">
				
				<div id="map_canvas" style="width:500px;height:500px;" class="col-sm-8"></div>	
			</fieldset>
		</div>
	</div>

</div>

<div class="form-actions">
	<button type="submit" class="btn btn-primary"><?php echo lang('save');?></button>
</div>
</form>

<?php  
$lat='';
$lon='';
if(isset($restaurant_latitude) && $restaurant_latitude !='' ) {
	$lat=$restaurant_latitude;
}
else {
	 $lat=54.95869420484606;
}
if(isset($restaurant_langitude) && $restaurant_langitude!='' ) {
	$lon=$restaurant_langitude;
}
else {
	$lon=-2.7575678906250687;
}

?>
<script type="text/javascript">
 var map;
jQuery(document).ready(function() {
  var myLatlng = new google.maps.LatLng(<?php echo $lat;  ?>,<?php echo $lon; ?>);

  var myOptions = {
     zoom: 10,
     center: myLatlng,
	 center: new google.maps.LatLng(19.0760, 72.8777),
     mapTypeId: google.maps.MapTypeId.ROADMAP
   }
  map = new google.maps.Map(document.getElementById("map_canvas"), myOptions); 

  var marker = new google.maps.Marker({
  draggable: true,
  position: myLatlng, 
  map: map,
  title: "Your location"
  });

google.maps.event.addListener(marker, 'dragend', function (event) {

 document.getElementById("lat").value = this.getPosition().lat();
    document.getElementById("lng").value = this.getPosition().lng();

 });

});
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDKUsjUabpe8-dBedcqnKchPAVfsNqFnlE"></script>
<script type="text/javascript">
$('form').submit(function() {
	$('.btn').attr('disabled', true).addClass('disabled');
});
</script>
<script type="text/javascript">

function add_related_product()
{
	//if the related product is not already a related product, add it
	if($('#related_product_'+$('#product_list').val()).length == 0 && $('#product_list').val() != null)
	{
		<?php $new_item	 = str_replace(array("\n", "\t", "\r"),'',related_items("'+$('#product_list').val()+'", "'+$('#product_item_'+$('#product_list').val()).html()+'"));?>
		var related_product = '<?php echo $new_item;?>';
		$('#product_items_container').append(related_product);
		run_product_query();
	}
	else
	{
		if($('#product_list').val() == null)
		{
			alert('<?php echo lang('alert_select_product');?>');
		}
		else
		{
			alert('<?php echo lang('alert_product_related');?>');
		}
	}
}

function remove_related_product(id)
{
	if(confirm('<?php echo lang('confirm_remove_related');?>'))
	{
		$('#related_product_'+id).remove();
		run_product_query();
	}
}

function photos_sortable()
{
	$('#gc_photos').sortable({	
		handle : '.gc_thumbnail',
		items: '.gc_photo',
		axis: 'y',
		scroll: true
	});
}
//]]>
</script>
<?php
function related_items($id, $name) {
	return '
			<tr id="related_product_'.$id.'">
				<td>
					<input type="hidden" name="related_pitstops[]" value="'.$id.'"/>
					'.$name.'</td>
				<td>
					<a class="btn btn-danger pull-right btn-mini" href="#" onclick="remove_related_product('.$id.'); return false;"><i class="fa fa-trash"></i> '.lang('remove').'</a>
				</td>
			</tr>
		';
}
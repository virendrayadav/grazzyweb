<?php 
$f_name				= form_input('name', set_value('name', $name), 'class="form-control"');
$f_discount			= form_input('discount', set_value('discount', $discount), 'class="form-control"');
$f_discount_type	= form_dropdown('discount_type', array('percent'=>'percent','fixed'=>'fixed'), set_value('discount_type', $discount_type), 'class="form-control"');

echo form_open($this->config->item('admin_folder').'/customers/edit_group/'.$id); 

?>
<div class="form-group">	
<label><?php echo lang('group_name');?></label>
<?php echo $f_name; ?>
</div>
<div class="form-group">		
<label><?php echo lang('discount');?></label>
<?php echo $f_discount ?>
</div>
<div class="form-group">
<label><?php echo lang('discount_type');?></label>
<?php echo $f_discount_type  ?>
</div>
<div class="form-actions">
	<input class="btn btn-primary" type="submit" value="<?php echo lang('save');?>"/>
</div>

</form>

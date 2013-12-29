

<div id="content">
<?php echo validation_errors();

 echo form_open('elfelejtettjelszo'); ?>

	<h5>Felhasznlónév</h5>
	<input type="text" name="username" value="<?php echo set_value('username'); ?>" size="50" />
	<h5>Email cím</h5>
	<input type="text" name="email" value="<?php echo set_value('email'); ?>" size="50" /><br>
			<?php  echo $image;?><br>
			Írja be a fentebb látható karaktereket:<br><input type="text" name="captcha" value="" /><br>
	<input type="submit" value="Küldés" />

</form>
</div>


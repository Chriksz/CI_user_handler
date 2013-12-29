
<div id="content">
	<?php echo validation_errors();
	echo form_open('bejelentkezes'); ?>
	
		Felhasználó név:<br>
		<input type="text" name="username" size="10" class="regfields" value=""><br>
		Jelszó:<br>
		<input type="password" name="password" size="10" class="regfields" value="" /><br>
		<?php if (isset($image)): echo $image;?><br>
		Írja be a fentebb látható karaktereket:<br><input type="text" name="captcha" value="" /><br>
		<?php endif;?>
		<input type="submit" name="btn_login" value="Bejelentkezés"  />
	</form>
		<a href="<?php echo prep_url(site_url('regisztracio'));?>">Regisztrálj! </a><br>
		<a href="<?php echo prep_url(site_url('elfelejtettjelszo'));?>">Elfelejtettt jelszó </a>
</div>


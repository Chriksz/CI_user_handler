
<div id="content">
<?php echo validation_errors(); ?>
<?php echo form_open('regisztracio'); ?>

<h5>Felhasznlónév</h5>
<input type="text" name="username" value="<?php echo set_value('username'); ?>" size="50" />

<h5>Jelszó</h5>
<input type="password" name="password" value="<?php echo set_value('password'); ?>" size="50" />

<h5>Jelszó mégegyszer</h5>
<input type="password" name="passconf" value="<?php echo set_value('passconf'); ?>" size="50" />

<h5>Email cím</h5>
<input type="text" name="email" value="<?php echo set_value('email'); ?>" size="50" />
<h5>Születési idő:</h5>
<? 


$options = array();
echo "<select name='days'>";
for ($i=1; $i<32; $i++){
$select = set_select('days', $i);
echo "<option $select value='$i'>$i</option>";
}
echo "</select>";


echo "<select name='months'>";
for ($i=1; $i<13; $i++){
$select = set_select('months', $i);
echo "<option $select value='$i'>$i</option>";
}
echo "</select>";

echo "<select name='years'>";


for ($i=1900; $i<=date('Y'); $i++){
$select = set_select('years', $i);
echo "<option $select value='$i'>$i</option>";
}
echo "</select>";

     
    ?>
	Minimum 14 évesnek kell lennie!
	<br>
<input type="checkbox" name="license" value="1" <?php echo set_checkbox('license', '1'); ?> />Elfogadom, hogy sok sok pénzt költök el itt<br>
			<?php  echo $image;?><br>
			Írja be a fentebb látható karaktereket:<br><input type="text" name="captcha" value=""   /><br>
<input type="submit" value="Regisztrálás" />

</form>

</div>



<div id="content">
<?php echo validation_errors();
 echo form_open("elfelejtettjelszo/$pwres"); ?>

<h5>Jelszó:</h5>
<input type="password" name="password" value="" size="50" />
<h5>jelszó újra:</h5>
<input type="password" name="passconf" value="" size="50" /><br>


<input type="submit" value="Küldés" />

</form>
</div>

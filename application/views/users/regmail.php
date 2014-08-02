<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<html>
    <head></head>
    <body>
        <h1> Köszönjük regisztrálását!</h1> 
        <p>Ha a honalappal kapcsolatban bármi problémája van, keressen fel minket: service@addonline.hu</p>
        <p> Az alábbi linkre kattintva érvényesítheti regisztrációját:</p>
        <p><a href='<?php echo prep_url(site_url("regisztracio/$username/$key")); ?>'>Link</a></p></body></html>
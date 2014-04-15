<?php
if (isset($scripts)){
  foreach((array) $scripts as $script)
  {
    echo "<script src='/scripts/{$script}.js' type='text/javascript'> </script>";
  }
  }   ?>

</body>
</html>

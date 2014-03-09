<?php
if (isset($scripts)){
  if (is_array ($scripts)){
  foreach($scripts as $script){
  echo "<script src='/scripts/{$script}.js' type='text/javascript'> </script>";
  }
  }
  else{
  echo "<script src='/scripts/{$scripts}.js' type='text/javascript' ></script>";
  }
  }   ?>

</body>
</html>
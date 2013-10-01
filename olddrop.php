<?php
function readImageDirectory($directory) {
  $imgs = array();
  foreach(glob($directory .'/{*.jpg,*.gif,*.jpeg,*.png,*.bmp,*.txt}', GLOB_BRACE) as $file)   
  {
    array_push($imgs, removedirMainfolder($file));
    //echo "Filename: " . $file . "<br />";      
  }
  foreach (glob($directory .'/*', GLOB_ONLYDIR) as $subdir) {
    foreach(glob($subdir .'/{*.jpg,*.gif,*.jpeg,*.png,*.bmp,*.txt}', GLOB_BRACE) as $file)   
    {
      array_push($imgs, removedirMainfolder($file));
    //echo "Filename: " . $file . "<br />";      
    }
  }
  print_r($imgs);
  return $imgs;
}

function displayImage($imgs, $dir) {
  $lastdir = 'toto';
  foreach ($imgs as $idx=>$img) {
    $currentdir = removeSubfolder($img);
    $pos = strpos($img, '/');
    if ($pos !== false) {
      if ($currentdir !== $lastdir) {
        $subtitle = removedirPrefix($currentdir);
        echo '</div>';
        echo '<div class="row">';
        echo '<div class="story-title"><p>'.$subtitle.'</p></div>';
        $lastdir = $currentdir;
      }
      createWeb($img, $dir);
    }
    else {
      createWeb($img, $dir);
    }
  }
}

function createWeb ($img, $dir) {
  $pos = strpos($img, 'full');
  if (preg_match("/\.(txt)$/", $img)) {
    $f = fopen($dir . '/' . $img, "r");
    echo '<div class="col-md-6 story-img-list"><a href=""><img src="blank_alt.jpg"></a><div class="story-list-text">';
    while(!feof($f)) { 
      echo fgets($f);
    }
    echo '</div></div>';
    fclose($f);
  }
  else {
    if ($pos !== false) {
      echo '<div class="col-md-12 story-img-list full-size"><a href=' . $dir . '/' . $img . ' title=' . $img . '><img src=' . $dir . '/' . $img . '></a></div>';
    }
    else {
      $pos = strpos($img, 'big');
      if ($pos !== false) {
        echo '<div class="col-md-12 story-img-list full-width"><a href=' . $dir . '/' . $img . ' title=' . $img . '><img src=' . $dir . '/' . $img . '></a></div>';
      }
      else {
        echo '<div class="col-md-6 story-img-list"><a href=' . $dir . '/' . $img . ' title=' . $img . '><img src=' . $dir . '/' . $img . '></a></div>';
      }
    }
  }
}

function createMainTitle($dir){
  
  // foreach(glob($dir .'/*/{*.jpg,*.gif,*.jpeg,*.png,*.bmp,*.txt}', GLOB_BRACE) as $file)   
  // {
  //   while ($notfound) {
  //     echo "Filename: " . $file . "<br />";
  //     displayMainImage($file, $dir, '1');
  //     $notfound= false;
  //   }
  //   //echo "Filename: " . $file . "<br />";      
  // }
  foreach (glob($dir .'*', GLOB_ONLYDIR) as $subdir) {
    //echo "Filename: " . $subdir . "<br />";
    $notfound = true;
    $filelist = glob($subdir .'/{*.jpg,*.gif,*.jpeg,*.png,*.bmp}', GLOB_BRACE);
    if (count($filelist)) {
      displayMainImage(array_pop($filelist), $subdir, '1');
    }


    // {
    //   //echo "Filename: " . $file . "<br />";
    //   while ($notfound) {
    //     displayMainImage($file, $subdir, '1');
    //     //break 2;
    //     $notfound= false;   
    //    }
    // }
  }

  // //scan all directory
  // $dirs = array_filter(glob('*'), 'is_dir');
  // foreach ($dirs as $key => $dir) {
  //   if ($dh = opendir($dir)) {
  //     $notfound = true;
  //     while ( $notfound and ($file = readdir($dh)) !== false) {
  //       if (!is_dir($file) && preg_match("/\.(bmp|jpe?g|gif|png)$/", $file)) {
  //         //directory contains img
  //         displayMainImage($file, $dir, '1');
  //         $notfound= false;
  //       }
  //       else {
  //         $dirs_second = array_filter(glob($dir.'/*'), 'is_dir');
  //         foreach ($dirs_second as $key => $dir_second) {
  //           if ($dh_second = opendir($dir_second)) {
  //             $notfound_second = true;
  //             while ( $notfound_second and ($file_second = readdir($dh_second)) !== false) {
  //               if (!is_dir($file_second) && preg_match("/\.(bmp|jpe?g|gif|png)$/", $file_second)) {
  //                 displayMainImage($file_second, $dir_second, '2');
  //                 $notfound_second = false;
  //                 $notfound= false;
  //               }
  //             }
  //           }
  //         }
  //       }
  //     }
  //     //createStory($dir);
  //     closedir($dh);
  //   } else {
  //     die('cannot open ' . $dir);
  //   }
  // }
}


function removedirPrefix($Imagedirectory) {
  $pos = strpos($Imagedirectory, '_');
  if ($pos !== false) {
    $pos = $pos + 1;
    $Dirwithoutprefix = substr($Imagedirectory, $pos);
    return $Dirwithoutprefix;
  }
  else {
    return$Imagedirectory;
  }
}

function removedirMainfolder($Imagedirectory) {
  $pos = strpos($Imagedirectory, '/');
  if ($pos !== false) {
    $pos = $pos + 1;
    $Dirwithoutprefix = substr($Imagedirectory, $pos);
    return $Dirwithoutprefix;
  }
  else {
    return$Imagedirectory;
  }
}

function removeSubfolder($directory) {
  $pos = strpos($directory, '/');
  if ($pos !== false) {
    $pos = $pos;
    $Imagedirectorywithoutsub = substr($directory,'0' ,$pos);
    return $Imagedirectorywithoutsub;
  }
  else {
    return $directory;
  }
}

function displayMainImage($image, $directory, $depth) {
  if ($depth == '1') {
    $Imagedirectorywithoutprefix = removedirPrefix($directory);
    echo '<div class="col-md-6 story-img-list main-story-img-list">
    <a href=index.php?dir=' .$directory. '><img src='.$image. '>
      <div class="story-img-list-text">'.$Imagedirectorywithoutprefix.'</div></a>
    </div>';
  }
  elseif ($depth == '2') {
    $Imagedirectorywithoutsub = removeSubfolder($directory);
    $Imagedirectorywithoutprefix = removedirPrefix($Imagedirectorywithoutsub);
    echo '<div class="col-md-6 story-img-list main-story-img-list">
    <a href=index.php?dir=' .$Imagedirectorywithoutsub. '><img src=' .$directory.'/'.$image. '>
      <div class="story-img-list-text">'.$Imagedirectorywithoutprefix.'</div></a>
    </div>';
  }
}


?>
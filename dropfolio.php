<?php

function displayMainTitle() {
  echo '<div class="main-title"><h1>Dropfolio</h1><p>Welcome to your dropfolio - Choose your story.</p></div>';
}

function displayStoryTitle($directory) {
  $Imagedirectorywithoutsub = removeSubfolder($directory);
  $Imagedirectorywithoutprefix = removedirPrefix($Imagedirectorywithoutsub);
  echo '<div class="story-menu"><a href="index.php"><img src="back.png"></a></div>';
  echo '<div class="story-title"><h1>' .$Imagedirectorywithoutprefix. '</h1></div>';
}

function scanDirAndPush($dir) {
  $imgArray = array();
  foreach(glob($dir .'/{*.jpg,*.gif,*.jpeg,*.png,*.bmp,*.txt}', GLOB_BRACE) as $file) {
    array_push($imgArray, removedirMainfolder($file));
  }
  return $imgArray;
}

function createImgtree($directory) {
  $imgTree = array();
  $imgs = array();
  foreach (glob($directory .'*', GLOB_ONLYDIR) as $subdir) {
    $imgs = scanDirAndPush($subdir);
    foreach (glob($subdir .'/*', GLOB_ONLYDIR) as $subsubdir) {
      $subimgs = scanDirAndPush($subsubdir);
      $imgs[removeDotSlash($subsubdir)]=$subimgs;
    }
    if (empty($imgs) && is_dir($subdir)) {
      foreach (glob($subdir .'/*', GLOB_ONLYDIR) as $subsubdir) {
        $subimgs = scanDirAndPush($subsubdir);
        $imgs[removeDotSlash($subsubdir)]=$subimgs;
      }
    }
    if (!empty($imgs)) {
      $imgTree[removeDotSlash($subdir)]=$imgs;
    }
  }
  //print_r($imgTree);
  return $imgTree;
}

function displayStoryList($imgTree) {
  foreach ($imgTree as $directory => $dir) {
    //echo $directory .' '.$value .' ';
    foreach ($dir as $key => $img) {
     if (is_array($img)) {
      foreach ($img as $subdir => $subimg) {
        $Imagedirectorywithoutprefix = removedirPrefix($directory);
        $dirSpace = str_replace(' ', '%20', $directory);
        $imgSpace = str_replace(' ', '%20', $subimg);
        echo '<div class="col-md-6 story-img-list main-story-img-list">
        <a href=index.php?dir=' .$dirSpace. '><img src='.$imgSpace. '>
         <div class="story-img-list-text">'.$Imagedirectorywithoutprefix.'</div></a>
       </div>';
       break 2;
     }
   }
   else {
    $Imagedirectorywithoutprefix = removedirPrefix($directory);
    $dirSpace = str_replace(' ', '%20', $directory);
    $imgSpace = str_replace(' ', '%20', $img);
    echo '<div class="col-md-6 story-img-list main-story-img-list">
    <a href=index.php?dir=' .$dirSpace. '><img src='.$imgSpace. '>
      <div class="story-img-list-text">'.$Imagedirectorywithoutprefix.'</div></a>
    </div>';
    break;
  }
}
}
}

function displayStory($imgTree, $Imagedirectory) {
  $imgArray = $imgTree[$Imagedirectory];
  $imgArray = changeOrderFullSize($imgArray);
  foreach ($imgArray as $key => $file) {
    $type = getFileType($file);
    $size = getFileSize($file);
    if (is_array($file)) {
      $subfolder = removedirPrefix(removedirMainfolder($key));
      echo '</div><div class="row">';
      echo '<div class="story-title"><p>'.$subfolder.'</p></div>';
      foreach ($file as $subkey => $subfile) {
        $subtype = getFileType($subfile);
        $subsize = getFileSize($subfile);
        createFileHTML($subfile, $subtype, $subsize);
        //createWeb($subfile, $Imagedirectory);
      }
    }
    else {
      createFileHTML($file, $type, $size);
      //createWeb($img, $Imagedirectory);
    }
  }
}

function changeOrderFullSize($imgArray) {
  //re order the tree for better full image display
  $nbFullWidth = 0;
  foreach ($imgArray as $key => $file) {
    $size = getFileSize($file);
    if ($size !== 'normal' && $key+$nbFullWidth & 1 && !is_array($file)) {
      $item = $imgArray[ $key ];
      $imgArray[ $key ] = $imgArray[ $key + 1 ];
      $imgArray[ $key + 1 ] = $item;
      $nbFullWidth = $nbFullWidth + 1;
    }
    if (is_array($file)) {
      $file = changeOrderFullSize($file);
      $imgArray[ $key ] = $file;
    }
  }
  return $imgArray;
}

function getFileType($file) {
  $type = 'img';
  if (preg_match("/\.(txt)$/", $file)) {
    $type = 'txt';
  }
  return $type;
}

function getFileSize($file) {
  $size = 'normal';
  $pos = strpos($file, 'full');
  if ($pos !== false) {
    $size = 'full';
  }
  else {
    $pos = strpos($file, 'big');
    if ($pos !== false) {
      $size = 'big';
    }
  }
  return $size;
}


function createFileHTML($file, $type, $size) {
  $html = '<div class="story-img-list ';
  if ($size == 'full') {
    $html .= 'col-md-12 full-size';
  }
  elseif ($size == 'big') {
    $html .= 'col-md-12 full-width';
  }
  else {
    $html .= 'col-md-6';
  }
  $html .= '">';
  if ($type == 'txt') {
    $f = fopen($file, "r");
    $html .= '<a href=""><img src="blank_alt.jpg"></a><div class="story-list-text">';
    while(!feof($f)) { 
      $html .= fgets($f);
    }
    $html .= '</div>';
    fclose($f);
  }
  else {
    $imgSpace = str_replace(' ', '%20', $file);
    $imgTitle = removedirMainfolder(removedirMainfolder($file));
    $html .= '<a href=' . $imgSpace . ' title=' . $imgTitle . '><img src=' . $imgSpace . '></a>';
  }
  $html .= '</div>';
  displayHTML($html);
}

function displayHTML ($html) {
  echo $html;
}

function removeDotSlash($Imagedirectory) {
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

?>
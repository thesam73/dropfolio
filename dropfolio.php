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

function displayEndStory($directory) {
	$Imagedirectorywithoutsub = removeSubfolder($directory);
	$Imagedirectorywithoutprefix = removedirPrefix($Imagedirectorywithoutsub);
	echo '</div></div><div class="container">
	   		<div class="row">
	   		<div class="story-title"><h1>' .$Imagedirectorywithoutprefix. '<p><a href="#">Back to top</a></p></h1>
	   		</div>';
}
function scanDirAndPush($dir) {
  $imgArray = array();
  foreach(glob($dir .'/{*.jpg,*.gif,*.jpeg,*.png,*.bmp,*.txt}', GLOB_BRACE) as $file) {
  //echo $file;
    array_push($imgArray, removedirMainfolder($file));
  }
  //$imgArray = array_map("basename", $imgArray);
  natcasesort($imgArray);
  $imgArray = array_values($imgArray);
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
  //sort($imgTree);
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
        echo '<div class="col-xs-12 col-md-6 story-img-list main-story-img-list">
        <div class="main-img blur" style="background-image: url(' . $imgSpace . ');">
        <a href=index.php?dir=' .$dirSpace. '><img src='.$imgSpace. '>
         <div class="story-img-list-text">'.$Imagedirectorywithoutprefix.'</div></a>
       </div></div>';
       break 2;
     }
   }
   else {
    $Imagedirectorywithoutprefix = removedirPrefix($directory);
    $dirSpace = str_replace(' ', '%20', $directory);
    $imgSpace = str_replace(' ', '%20', $img);
    echo '<div class="col-xs-12 col-md-6 story-img-list main-story-img-list">
    <div class="main-img blur" style="background-image: url(' . $imgSpace . ');">
    <a href=index.php?dir=' .$dirSpace. '><img src='.$imgSpace. '>
      <div class="story-img-list-text">'.$Imagedirectorywithoutprefix.'</div></a>
    </div></div>';
    break;
  }
}
}
}

function displayStory($imgTree, $Imagedirectory) {
  $imgArray = $imgTree[$Imagedirectory];
  //print_r($imgArray);
  $imgArray = changeOrderFullSize($imgArray);
  //print_r($imgArray);
  foreach ($imgArray as $key => $file) {
    if (is_array($file)) {
      $subfolder = removedirPrefix(removedirMainfolder($key));
      echo '</div></div></div><div class="bg-story"><div class="container"><div class="row">';
      echo '<div class="story-title"><p>'.$subfolder.'</p></div>';
      foreach ($file as $subkey => $subfile) {
      	if (is_file($subfile)) {
        	$subtype = getFileType($subfile);
        	$subsize = getFileSize($subfile);
        	createFileHTML($subfile, $subtype, $subsize);
        	//createWeb($subfile, $Imagedirectory);
        	}
      }
    }
    elseif (is_file($file)) {
    	$type = getFileType($file);
    	$size = getFileSize($file);
      createFileHTML($file, $type, $size);
      //createWeb($img, $Imagedirectory);
    }
  }
}

function changeOrderFullSize($imgArray) {
	$order_changed = false;
  //re order the tree for better full image display
  $nbFullWidth = 0;
   foreach ($imgArray as $key => $file) {
    if (is_array($file)) {
      $file = changeOrderFullSize($file);
      $imgArray[ $key ] = $file;
    }
    elseif (is_file($file)) {
        $size = getFileSize($file);
        if ($size !== 'normal' && !is_array($file)) {
        	$nbFullWidth = $nbFullWidth + 1;
        }
        $magicnumber = $key+$nbFullWidth;
        if ($size !== 'normal' && $magicnumber % 2 == 0 && !is_array($file)) {
          $item = $imgArray[ $key ];
          $imgArray[ $key ] = $imgArray[ $key + 1 ];
          $imgArray[ $key + 1 ] = $item;
          //$nbFullWidth = $nbFullWidth + 1;
          $order_changed = true;
          //print_r($imgArray);
          //echo '<hr> toto'.$nbFullWidth.$key;
          }
    }
  }
  if ($order_changed) {
  	$imgArray = changeOrderFullSize($imgArray);
  }
  return $imgArray;
}

function getFileType($file) {
	
	  $type = 'img';
	  if (is_file($file)) {
	  if (preg_match("/\.(txt)$/", $file)) {
	    $type = 'txt';
	  }
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
    $html .= 'col-xs-12 col-md-12 full-size';
  }
  elseif ($size == 'big') {
    $html .= 'col-xs-12 col-md-12 full-width';
  }
  else {
    $html .= 'col-xs-12 col-md-6';
  }
  $html .= '">'."\n".'<div class="main-img"';
  if ($type == 'txt') {
    $f = fopen($file, "r");
    //$html .= '<a href=""><img src="blank_alt.jpg"></a><div class="story-list-text">';
    $html .= '><div class="story-list-text">';
    while(!feof($f)) { 
      $html .= fgets($f);
    }
    $html .= '</div>'."\n";
    fclose($f);
  }
  else {
    $imgSpace = str_replace(' ', '%20', $file);
    $imgTitle = removedirMainfolder(removedirMainfolder($file));
    $html .= ' style="background-image: url(' . $imgSpace . ');">';
    $html .= '<a href=' . $imgSpace . ' title=' . $imgTitle . '><img src=' . $imgSpace . '></a>'."\n";
  }
  $html .= '</div>'."\n".'</div>'."\n";
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

<?php
include ('dropfolio.php');
include('header.php');

$params = array_merge( array( "dir" => null ), $_GET );
$imgTree = createImgtree('./');
//print_r($imgTree);

if(is_null($params["dir"]))
{
	displayMainTitle();
	displayStoryList($imgTree);
	
}
else
{
	$Imagedirectory = $params["dir"];
	displayStoryTitle($Imagedirectory);
	displayStory($imgTree, $Imagedirectory);
	displayEndStory($Imagedirectory);
	//$imgs = readImageDirectory($Imagedirectory);

	//displayImage($imgs, $Imagedirectory);
}



include('bottom.php');

?>
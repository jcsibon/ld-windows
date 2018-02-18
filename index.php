<?php

$products = [
	"FPC804167"		=> "PVC-AB-1V-BA-VRI0-CLE0",
	"FPC804205"		=> "PVC-FE-1V-OF-VRI0-CLE0",
	"FPC804214"		=> "PVC-FE-1V-OB-VRI0-CLE0",
//	"FPC804223"		=> "PVC-FE-1V-OF-VRI0-CLE0",
	"FPC804179"		=> "PVC-FE-2V-OF-VRI0-CLE0",
	"FPC804193"		=> "PVC-FE-2V-OB-VRI0-CLE0",
	"FPC804197"		=> "PVC-FE-2V-OB-VRI1-CLE0",
	"FPC804227"		=> "PVC-PF-1V-OF-VRI0-CLE1",
	"FPC804239"		=> "PVC-PF-1V-OF-VRI1-CLE0",
	"FPC804231"		=> "PVC-PF-2V-OF-VRI0-CLE1",
	"FPC804243"		=> "PVC-PF-2V-OF-VRI1-CLE0",
	"FPC2207631"	=> "PVC-FE-1V-OF-VRI0-CLE1",
	"FPC407363"		=> "PVC-FE-1V-OF-VRI0-CLE0",
	"FPC804454"		=> "ALU-BC-2V-CO-VRI0-CLE0",
	"FPC407363"		=> "PVC-BC-2V-CO-VRI0-CLE0",
	"FPC2207631"	=> "PVC-BC-2V-CO-VRI0-CLE1",
	"FPC2311400"	=> "PIN-AB-1V-BA-VRI0-CLE0",
	"FPC2309081"	=> "PIN-FE-1V-OF-VRI0-CLE0",
	"FPC2309820"	=> "PIN-FE-2V-OF-VRI0-CLE0",
	"FPC2311114"	=> "PIN-PF-1V-OF-VRI0-CLE1",
	"FPC2311290"	=> "PIN-PF-2V-OF-VRI0-CLE1",
	"FPC2308151"	=> "PIN-BC-2V-CO-VRI0-CLE0",
	"FPC2310630"	=> "CHE-AB-1V-BA-VRI0-CLE0",
	"FPC367123"		=> "CHE-FE-1V-OF-VRI0-CLE0",
	"FPC2304400"	=> "CHE-FE-2V-OF-VRI0-CLE0",
	"FPC2306684"	=> "CHE-PF-1V-OF-VRI0-CLE1",
	"FPC2316413"	=> "CHE-PF-2V-OF-VRI0-CLE1",
	"FPC2304071"	=> "CHE-BC-2V-CO-VRI0-CLE0"
];


$count = 0;

foreach($products as $key=>$product)
{
	$count += count(json_decode(utf8_encode(file_get_contents("products/".$key.".json")),1)['product']['itemList']['item']);
}
// echo $count." articles".PHP_EOL;
// echo count(glob("files/*"))." visuels".PHP_EOL;


// header('Content-Type: application/json');

foreach(glob("files/*") as $image)
{
	$images[substr(basename($image,".png"), 0, strlen(basename($image,".png"))-10)][] = basename($image,".png");
}
// die(json_encode($images,JSON_PRETTY_PRINT));

foreach($products as $key=>$product)
{

	$productdata = json_decode(utf8_encode(file_get_contents("products/".$key.".json")),1)['product'];
	echo $key." - ".$productdata['label']['content'].PHP_EOL;

	foreach($productdata['itemList']['item'] as $item)
	{
		preg_match('/[\S\s]*(?:Tableau|Tab.|Tabl.|TabL.)[\s]*H[\.]*[\s]*(?P<H>[0-9]+)[\s]*x[\s]*[l]*[\.]*[\s]*(?P<l>[0-9]+)/', $item['label']['content'], $matches);
		
		$matches['s'] = 0;

		if(strpos($item['label']['content'], "Tirant D"))
			$matches['s'] = "D";
		if(strpos($item['label']['content'], "Tirant G"))
			$matches['s'] = "G";

		if(strpos($item['label']['content'], "D à G"))
			$matches['s'] = "D";
		if(strpos($item['label']['content'], "G à D"))
			$matches['s'] = "G";

		$result[$item['sku']]['label'] = $item['label']['content'];
		$result[$item['sku']]['images'] = array();
		
		echo $item['sku']." (".$product."-".$matches['s']."-".substr("0".$matches['H'],-3)."x".substr("0".$matches['l'],-3).") : ";

		foreach($images as $key => $files)
		{
			if($key == $product."-".$matches['s']."-".substr("0".$matches['H'],-3)."x".substr("0".$matches['l'],-3))
			{
				$result[$item['sku']]['images'] = $files;
				echo "\tOK".PHP_EOL;
				foreach($files as $file)
				{
					$images_with_sku[] = $file;
				}
			}
		}
		if(!count($result[$item['sku']]['images']))
		{
			$skus_without_image[] = $item['sku'].' - '.$item['label']['content'];
			echo "Absent".PHP_EOL;
		}	
	}
	echo PHP_EOL;	
}

echo "Visuels sans affectation".PHP_EOL;

$images_without_sku = 0;

foreach(glob("files/*") as $image)
{
	if(!in_array(basename($image,".png"), $images_with_sku))
	{
		echo basename($image,".png").PHP_EOL;
		$images_without_sku++;
	}
}
echo PHP_EOL;	
echo "Skus sans image : ".count($skus_without_image)." / ".$count.PHP_EOL;
echo "Images sans sku : ".$images_without_sku." / ".count(glob("files/*")).PHP_EOL;

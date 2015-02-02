<?php
function xml_to_csv_conversion($xml,$csv_filename)
 {
	$f = fopen($csv_filename, 'w');

	$Asin_Tag_name="ASIN";
	$DetailPageURL_Tag_name="DetailPageURL";
	$technical_detail_Tag_name="Description";
	$All_offer_url_Tag_name="All_Offer_Url";
	$SalesRank_Tag_name="SalesRank";
	$SmallImage_Tag_name="SmallImage";
	$MediumImage_Tag_name="MediumImage";
	$LargeImage_Tag_name="LargeImage";
	$Binding_Tag_name="Binding";
	$Brand_Tag_name="Brand";
	$CatalogNumberList_Tag_name="CatalogNumberList";
	$Color_Tag_name="Color";
	$EAN_Tag_name="EAN";
	$EANListElement_Tag_name="EANListElement";
	$Feature_Tag_name="Feature";
	$ItemDimensions_Tag_name="ItemDimensions";
	$Label_Tag_name="Label";
	$ListPrice_Tag_name="ListPrice";
	$Manufacturer_Tag_name="Manufacturer";
	$Model_Tag_name="Model";
	$MPN_Tag_name="MPN";
	$NumberOfItems_Tag_name="NumberOfItems";
	$PackageDimensions_Tag_name="PackageDimensions";
	$PackageQuantity_Tag_name="PackageQuantity";
	$PartNumber_Tag_name="PartNumber";
	$ProductGroup_Tag_name="ProductGroup";
	$ProductTypeName_Tag_name="ProductTypeName";
	$Publisher_Tag_name="Publisher";
	$SKU_Tag_name="SKU";
	$Studio_Tag_name="Studio";
	$Title_Tag_name="Title";
	$UPC_Tag_name="UPC";
	$Warranty_Tag_name="Warranty";
	$LowestNewPrice_Tag_name="LowestNewPrice";
	$TotalNew_Tag_name="TotalNew";
	$TotalCollectible_Tag_name="TotalCollectible";
	$TotalRefurbished_Tag_name="TotalRefurbished";
	$OfferPrice_Tag_name="OfferPrice";
	$AmountSaved_Tag_name="AmountSaved";
	$PercentageSaved_Tag_name="PercentageSaved";
	$Availability_Tag_name="Availability";
	$IsEligibleForSuperSaverShipping_Tag_name="IsEligibleForSuperSaverShipping";
	$CustomerReviews_Tag_name="CustomerReviews";
	$EditorialReviews_Tag_name="EditorialReviews";
	
	fwrite($f,$Asin_Tag_name."|".$DetailPageURL_Tag_name."|".$technical_detail_Tag_name."|".$All_offer_url_Tag_name."|".$SalesRank_Tag_name."|".$SmallImage_Tag_name."|".$MediumImage_Tag_name."|".$LargeImage_Tag_name."|".$Binding_Tag_name."|".$Brand_Tag_name."|".$CatalogNumberList_Tag_name."|".$Color_Tag_name."|".$EAN_Tag_name."|".$EANListElement_Tag_name."|".$Feature_Tag_name."|".$ItemDimensions_Tag_name."|".$Label_Tag_name."|".$ListPrice_Tag_name."|".$Manufacturer_Tag_name."|".$Model_Tag_name."|".$MPN_Tag_name."|".$NumberOfItems_Tag_name."|".$PackageDimensions_Tag_name."|".$PackageQuantity_Tag_name."|".$PartNumber_Tag_name."|".$ProductGroup_Tag_name."|".$ProductTypeName_Tag_name."|".$Publisher_Tag_name."|".$SKU_Tag_name."|".$Studio_Tag_name."|".$Title_Tag_name."|".$UPC_Tag_name."|".$Warranty_Tag_name."|".$LowestNewPrice_Tag_name."|".$TotalNew_Tag_name."|".$TotalCollectible_Tag_name."|".$TotalRefurbished_Tag_name."|".$OfferPrice_Tag_name."|".$AmountSaved_Tag_name."|".$PercentageSaved_Tag_name."|".$Availability_Tag_name."|".$IsEligibleForSuperSaverShipping_Tag_name."|".$CustomerReviews_Tag_name."|".$EditorialReviews_Tag_name."|");
	
	fwrite($f,"".PHP_EOL);
	foreach($xml->Items->children() as $child)
			{
				$child_name=$child->getName();
				if($child_name=="Item")
				{	
					$flag_for_itemlink=1;
					$flag_for_Feature=1;
					$flag_for_ItemDimensions=1;
					$flag_for_ListPrice=1;
					$flag_for_feature=0;
					$flag_for_Imageset=1;
					$counter="ok";
					$SmallImage=0;
					$arr123=array();
					$subcounter=0;
					foreach($child->children() as $subchild)
					{	
						$subchild_name=$subchild->getName();
						if($subchild->children())
						{
						}
						else
						{
							if($subchild_name=="ParentASIN")
							{
							}
							else
							{
								array_push($arr123,$subchild_name);
							}
						}
					}
					foreach($child->children() as $subchild)
					{	
						$Subchild_arr=array();
						$subchild_name=$subchild->getName();
						if($subchild->children())
						{
							$flag_for_itemlink=1;
							$SmallImage=1;
							$MediumImage=1;
							$LargeImage=1;
							$All_Offers=1;
							$Binding=1;
							$Brand=1;
							$CatalogNumberList=1;
							$Color=1;
							$EAN=1;
							$EANList=1;
							$Feature=1;
							$ItemDimensions=1;
							$Label=1;
							$ListPrice=1;
							$Manufacturer=1;
							$Model=1;
							$MPN=1;
							$NumberOfItems=1;
							$PackageDimensions=1;
							$PackageQuantity=1;
							$PartNumber=1;
							$ProductGroup=1;
							$ProductTypeName=1;
							$Publisher=1;
							$SKU=1;
							$Studio=1;
							$Title=1;
							$UPC=1;
							$Warranty=1;
							$LowestNewPrice=1;
							$TotalNew=1;
							$TotalCollectible=1;
							$TotalRefurbished=1;
							$Offer=1;
							$flag_for_ItemDimensions=2;
							$flag_for_ListPrice=2;
							$flag_for_PackageDimensions=2;
							$flag_for_Editorial_Review=1;
							$CustomerReviews=1;
							$Technical_Details=1;
							$feature_string=NULL;
							$arr=array();
							$ImageSet_arr=array();
							$OfferSummary_arr=array();
							$Offers_arr=array();
							$feature_count=array();
							
							foreach($subchild->children() as $subchild1)
							{
								$subchild1_name=$subchild1->getName();
								if($subchild_name=="ImageSets")
								{
									if($subchild1_name!="ImageSet")
									{
										$SmallImage="ok";
										$MediumImage="ok";
										$LargeImage="ok";
									}
									else
									{
										if($subchild1->SmallImage->getName()=="SmallImage")
										{
											array_push($ImageSet_arr,$subchild1->SmallImage->getName());
										}
										if($subchild1->MediumImage->getName()=="MediumImage")
										{
											array_push($ImageSet_arr,$subchild1->MediumImage->getName());
										}
										if($subchild1->LargeImage->getName()=="LargeImage")
										{
											array_push($ImageSet_arr,$subchild1->LargeImage->getName());
										}
									}
								}
								
								if($subchild_name=="OfferSummary")
								{
									array_push($OfferSummary_arr,$subchild1_name);
								}
								if($subchild_name == "Offers")
								{
									array_push($Offers_arr,$subchild1_name);
								}
								
								if($subchild_name=="ItemAttributes")
								{
									array_push($arr,$subchild1_name);
									if($subchild1_name=="Feature")
									{
										if(in_array("Feature",$arr))
										{
											if(!in_array("ItemDimensions", $arr) || !in_array("Label", $arr) || !in_array("ListPrice", $arr))
											{	
												array_push($feature_count,"1");
											}
										}
									}
								}
							}
							$feature_length_count=sizeof($feature_count);
							foreach($subchild->children() as $subchild1)
							{
								$subchild1_name=$subchild1->getName();
								if($subchild_name=="ItemLinks")
								{
									if($subchild1_name != "ItemLink" || $subchild1->Description!="Technical Details")
									{
										$Technical_Details="ok";
									}
									if($subchild1_name != "ItemLink" || $subchild1->Description!="All Offers")
									{
										$All_Offers="ok";
									}
								}
								if($subchild_name=="ImageSets")
								{
									if (!in_array("SmallImage", $ImageSet_arr))
									{	
										$SmallImage="ok";
									}
									if (!in_array("MediumImage", $ImageSet_arr))
									{	
										$MediumImage="ok";
									}
									if (!in_array("LargeImage", $ImageSet_arr))
									{	
										$LargeImage="ok";
									}
								}
								if($subchild_name=="OfferSummary")
								{
									if (!in_array("LowestNewPrice", $OfferSummary_arr))
									{	
										$LowestNewPrice="ok";
									}
									if (!in_array("TotalNew", $OfferSummary_arr))
									{	
										$TotalNew="ok";
									}
									if (!in_array("TotalCollectible", $OfferSummary_arr))
									{	
										$TotalCollectible="ok";
									}
									if (!in_array("TotalRefurbished", $OfferSummary_arr))
									{	
										$TotalRefurbished="ok";
									}
								}
								if($subchild_name=="Offers")
								{
									if (!in_array("Offer", $Offers_arr))
									{	
										$Offer="ok";
									}
								}
								/*if($subchild_name=="CustomerReviews")
								{
									if ($subchild1_name!="IFrameURL" && $subchild1_name=="HasReviews")
									{	
										
										echo "<br>customer Review is not there".$subchild1_name;
									}
								}*/
								if($subchild_name=="ItemAttributes")
								{
									
									if (!in_array("Binding", $arr))
									{	
										$Binding="ok";
									}
									if (!in_array("Brand", $arr))
									{	
										$Brand="ok";
									}
									if (!in_array("CatalogNumberList", $arr))
									{	
										$CatalogNumberList="ok";
									}
									if (!in_array("Color", $arr))
									{	
										$Color="ok";
									}
									if (!in_array("EAN", $arr))
									{	
										$EAN="ok";
									}
									if (!in_array("EANList", $arr))
									{	
										$EANList="ok";
									}
									if(!in_array("Feature",$arr))
									{
										$Feature="ok";
									}
									if (!in_array("ItemDimensions", $arr))
									{	
										$ItemDimensions="ok";
									}
									if (!in_array("Label", $arr))
									{	
										$Label="ok";
									}
									if (!in_array("ListPrice", $arr))
									{	
										$ListPrice="ok";
									}
									if (!in_array("Manufacturer", $arr))
									{	
										$Manufacturer="ok";
									}
									if (!in_array("Model", $arr))
									{	
										$Model="ok";
									}
									if (!in_array("MPN", $arr))
									{	
										$MPN="ok";
									}
									if (!in_array("NumberOfItems", $arr))
									{	
										$NumberOfItems="ok";
									}
									if (!in_array("PackageDimensions", $arr))
									{	
										$PackageDimensions="ok";
									}
									if (!in_array("PackageQuantity", $arr))
									{	
										$PackageQuantity="ok";
									}
									if (!in_array("PartNumber", $arr))
									{	
										$PartNumber="ok";
									}
									if (!in_array("ProductGroup", $arr))
									{	
										$ProductGroup="ok";
									}
									if (!in_array("ProductTypeName", $arr))
									{	
										$ProductTypeName="ok";
									}
									if (!in_array("Publisher", $arr))
									{	
										$Publisher="ok";
									}
									if (!in_array("SKU", $arr))
									{	
										$SKU="ok";
									}
									if (!in_array("Studio", $arr))
									{	
										$Studio="ok";
									}
									if (!in_array("Title", $arr))
									{	
										$Title="ok";
									}
									if (!in_array("UPC", $arr))
									{
										$UPC="ok";
									}
									if (!in_array("Warranty", $arr))
									{
										$Warranty="ok";
									}
								}
								if($subchild1_name=="ItemLink" && $subchild1->Description=="Technical Details")
								{	
									$ItemLink=$subchild1->URL;
									$ItemLink="\"$ItemLink\"";
									fwrite($f,$ItemLink."|");
									$counter=2;
								}
								if($Technical_Details=="ok" && $counter==1)
								{
									fwrite($f,"\"BLANK\"|");
									$counter=2;
								}
								if($subchild1_name=="ItemLink" && $subchild1->Description=="All Offers")
								{	
									$ItemLink=$subchild1->URL;
									$ItemLink="\"$ItemLink\"";
									fwrite($f,$ItemLink."|");
									$counter=3;
								}
								/*if($All_Offers=="ok" && $counter==2)
								{
									fwrite($f,"\"BLANK\"|");
									$counter=3;
								}*/
								if($subchild1_name=="ImageSet" && !in_array("SalesRank", $arr123) && $counter==3)
								{
									$counter=4;
									fwrite($f,"\"BLANK\"|");
								}
								if($subchild1_name=="ImageSet" && $flag_for_Imageset==1)
								{	
									$smallimage=$subchild1->SmallImage->URL;
									$smallimage="\"$smallimage\"";
									$mediumimage=$subchild1->MediumImage->URL;
									$mediumimage="\"$mediumimage\"";
									$largeimage=$subchild1->LargeImage->URL;
									$largeimage="\"$largeimage\"";
									fwrite($f,$smallimage."|".$mediumimage."|".$largeimage."|");
									$counter=5;
									$flag_for_Imageset++;
								}
								/*if($SmallImage=="ok" && counter==4 && $subchild1_name=="ImageSet")
								{
									fwrite($f,"\"BLANK\"|");
								}
								if($MediumImage=="ok" && counter==4 && $subchild1_name=="ImageSet")
								{
									fwrite($f,"\"BLANK\"|");
								}
								if($LargeImage=="ok" && counter==4 && $subchild1_name=="ImageSet")
								{
									fwrite($f,"\"BLANK\"|");
								}*/
								if($subchild1_name=="Binding")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$counter=6;
								}
								if($Binding=="ok" && $counter==5)
								{	
									fwrite($f,"\"BLANK\"|");
									$counter=6;
									$Binding=1;
								}
								if($subchild1_name=="Brand")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$counter=7;
								}
								if($Brand=="ok" && $counter==6)
								{	
									fwrite($f,"\"BLANK\"|");
									$counter=7;
									$Brand=1;
								}
								if($subchild1_name=="CatalogNumberList")
								{	
									$CatalogNumberListElement=$subchild1->CatalogNumberListElement;
									$CatalogNumberListElement="\"$CatalogNumberListElement\"";
									fwrite($f,$CatalogNumberListElement."|");
									$counter=8;
								}
								if($CatalogNumberList=="ok" && $counter==7)
								{	
									fwrite($f,"\"BLANK\"|");
									$counter=8;
									$CatalogNumberList=1;
								}
								if($subchild1_name=="Color")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$counter=9;
								}
								if($Color=="ok" && $counter==8)
								{	
									fwrite($f,"\"BLANK\"|");
									$counter=9;
									$Color=1;
								}
								if($subchild1_name=="EAN")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$counter=10;
								}
								if($EAN=="ok" && $counter==9)
								{
								fwrite($f,"\"BLANK\"|");
								$counter=10;
								$EAN=1;
								}														
								if($subchild1_name=="EANList")
								{	
									$EANListElement=$subchild1->EANListElement;
									$EANListElement="\"$EANListElement\"";
									fwrite($f,$EANListElement."|");
									$counter=11;
								}
								if($EANList=="ok" && $counter==10)
								{
									fwrite($f,"\"BLANK\"|");
									$counter=11;
									$EANList=1;
								}
								if($subchild1_name=="Feature")
								{	
									$flag_for_feature++;
									if($feature_string==NULL)
									{
										$feature_string=$subchild1;
									}
									else
									{
										$feature_string=$feature_string.", ".$subchild1;
									}
									if($flag_for_feature==$feature_length_count)
									{
										$feature_string = preg_replace('/\s+/', ' ', trim($feature_string));
										$feature_string="\"$feature_string\"";
										fwrite($f,$feature_string."|");
									}
									$counter=12;
								}
								if($Feature=="ok" && $counter==11)
								{	
									fwrite($f,"\"BLANK\"|");
									$counter=12;
									$Feature=1;
								}
								if($subchild1_name=="ItemDimensions")
								{	
									$ItemDimensions_filtered=$subchild1->Height->getname().":".$subchild1->Height.";".$subchild1->Length->getname().":".$subchild1->Length.";".$subchild1->Weight->getname().":".$subchild1->Weight.";".$subchild1->Width->getname().":".$subchild1->Width;
									$ItemDimensions_filtered="\"$ItemDimensions_filtered\"";
									fwrite($f,$ItemDimensions_filtered."|");
									$counter=13;
								}
								if($ItemDimensions=="ok" && $flag_for_ItemDimensions=2 && $counter==12 && $flag_for_feature==$feature_length_count)
								{
									fwrite($f,"\"BLANK\"|");
									$flag_for_ItemDimensions++;
									$ItemDimensions=1;
									$counter=13;
								}
								if($subchild1_name=="Label")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$counter=14;
								}
								if($Label=="ok" && $counter==13)
								{
									fwrite($f,"\"BLANK\"|");
									$Label=1;
									$counter=14;
								}
								if($subchild1_name=="ListPrice")
								{	
									$List_price=$subchild1->Amount->getName().":".$subchild1->Amount.";".$subchild1->CurrencyCode->getName().":".$subchild1->CurrencyCode.";".$subchild1->FormattedPrice->getName().":".$subchild1->FormattedPrice;
									$List_price="\"$List_price\"";
									fwrite($f,$List_price."|");
									$counter=15;
								}
								if($ListPrice=="ok" && $flag_for_ListPrice=2 && $counter==14)
								{
									fwrite($f,"\"BLANK\"|");
									$flag_for_ListPrice++;
									$ListPrice=1;
									$counter=15;
								}
								if($subchild1_name=="Manufacturer")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									 
									$counter=16;
								}
								if($Manufacturer=="ok" && $counter==15)
								{
									fwrite($f,"\"BLANK\"|");
									$Manufacturer=1;
									$counter=16;
								}
								if($subchild1_name=="Model")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$counter=17;
								}
								if($Model=="ok" && $counter==16)
								{
									fwrite($f,"\"BLANK\"|");
									$Model=1;
									$counter=17;
								}
								if($subchild1_name=="MPN")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$counter=18;
								}
								if($MPN=="ok" && $counter==17)
								{
									fwrite($f,"\"BLANK\"|");
									$MPN=1;
									$counter=18;
								}
								if($subchild1_name=="NumberOfItems")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$counter=19;
								}
								if($NumberOfItems=="ok" && $counter==18)
								{
									fwrite($f,"\"BLANK\"|");
									$NumberOfItems=1;
									$counter=19;
								}
								if($subchild1_name=="PackageDimensions")
								{	
									$packagedimensions=$subchild1->Height->getName().":".$subchild1->Height.";".$subchild1->Length->getName().":".$subchild1->Length.";".$subchild1->Weight->getName().":".$subchild1->Weight.";".$subchild1->Width->getName().":".$subchild1->Width;
									$packagedimensions="\"$packagedimensions\"";
									fwrite($f,$packagedimensions."|");
									$counter=20;
								}
								if($PackageDimensions=="ok" && $flag_for_PackageDimensions==2 && $counter==19)
								{
									fwrite($f,"\"BLANK\"|");
									$flag_for_PackageDimensions++;
									$PackageDimensions=1;
									$counter=20;
								}
								if($subchild1_name=="PackageQuantity")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$counter=21;
								}
								if($PackageQuantity=="ok" && $counter==20)
								{
									fwrite($f,"\"BLANK\"|");
									$PackageQuantity=1;
									$counter=21;
								}
								if($subchild1_name=="PartNumber")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$counter=22;
								}
								if($PartNumber=="ok" && $counter==21)
								{
									fwrite($f,"\"BLANK\"|");
									$PartNumber=1;
									$counter=22;
								}
								if($subchild1_name=="ProductGroup")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									
									$counter=23;
								}
								if($ProductGroup=="ok" && $counter==22)
								{
									fwrite($f,"\"BLANK\"|");
									$ProductGroup=1;
									$counter=23;
								}
								if($subchild1_name=="ProductTypeName")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$counter=24;
								}
								if($ProductTypeName=="ok" && $counter==23)
								{
									fwrite($f,"\"BLANK\"|");
									$ProductTypeName=1;
									$counter=24;
								}
								if($subchild1_name=="Publisher")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$counter=25;
								}
								if($Publisher=="ok" && $counter==24)
								{

									fwrite($f,"\"BLANK\"|");
									$counter=25;
									$Publisher=1;
								}
								if($subchild1_name=="SKU")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$counter=26;
								}
								if($SKU=="ok" && $counter==25)
								{
									fwrite($f,"\"BLANK\"|");
									$SKU=1;
									$counter=26;
								}
								if($subchild1_name=="Studio")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$counter=27;
								}
								if($Studio=="ok" && $counter==26)
								{
									fwrite($f,"\"BLANK\"|");
									$Studio=1;
									$counter=27;
								}
								if($subchild1_name=="Title")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$counter=28;
								}
								if($Title=="ok" && $counter==27)
								{
									fwrite($f,"\"BLANK\"|");
									$Title=1;
									$counter=28;
								}
								if($subchild1_name=="UPC")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$counter=29;
								}
								if($UPC=="ok" && $counter==28)
								{
									fwrite($f,"\"BLANK\"|");
									$UPC=1;
									$counter=29;
								}
								if($subchild1_name=="Warranty")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$counter=30;
								}
								if($Warranty=="ok" && $counter==29)
								{
									fwrite($f,"\"BLANK\"|");
									$Warranty=1;
									$counter=30;
								}
								if($subchild1_name=="LowestNewPrice")
								{	
									$lowestnewprice=$subchild1->Amount->getName().":".$subchild1->Amount.";".$subchild1->CurrencyCode->getName().":".$subchild1->CurrencyCode.";".$subchild1->FormattedPrice->getName().":".$subchild1->FormattedPrice;
									$lowestnewprice="\"$lowestnewprice\"";
									fwrite($f,$lowestnewprice."|");
									$LowestNewPrice=1;
									$counter=31;
								}
								if($counter==30 && $LowestNewPrice=="ok")
								{
									fwrite($f,"\"BLANK\"|");
									$counter=31;
								}
								if($subchild1_name=="TotalNew")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$TotalNew=1;
									$counter=32;
								}
								if($counter==31 && $TotalNew=="ok")
								{
									fwrite($f,"\"BLANK\"|");
									$counter=32;
								}
								if($subchild1_name=="TotalCollectible")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$TotalCollectible=1;
									$counter=33;
								}
								if($counter==32 && $TotalCollectible=="ok")
								{
									fwrite($f,"\"BLANK\"|");
									$counter=33;
								}
								if($subchild1_name=="TotalRefurbished")
								{	
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$TotalRefurbished=1;
									$counter=34;
								}
								if($counter==33 && $TotalRefurbished=="ok")
								{
									fwrite($f,"\"BLANK\"|");
									$counter=34;
								}
								if($subchild1_name=="Offer")
								{	
									$price_amount_name=$subchild1->OfferListing->Price->Amount->getName();
									$price_currencycode_name=$subchild1->OfferListing->Price->CurrencyCode->getName();
									$price_formattedprice_name=$subchild1->OfferListing->Price->FormattedPrice->getName();
									$price_amount_value=$subchild1->OfferListing->Price->Amount;
									$price_currencycode_value=$subchild1->OfferListing->Price->CurrencyCode;
									$price_formattedprice_value=$subchild1->OfferListing->Price->FormattedPrice;
									$amountsaved=$subchild1->OfferListing->AmountSaved->Amount;
									$percentage=$subchild1->OfferListing->PercentageSaved;
									$availability=$subchild1->OfferListing->Availability;
									$shipping=$subchild1->OfferListing->IsEligibleForSuperSaverShipping;
									if($amountsaved==NULL || $amountsaved=="")
									{
										$amountsaved="BLANK";
									}
									if($percentage==NULL || $percentage=="")
									{
										$percentage="BLANK";
									}
									if($availability==NULL  || $availability=="")
									{
										$availability="BLANK";
									}
									if($shipping==NULL  || $shipping=="")
									{
										$shipping="BLANK";
									}
									$price=$price_amount_name.":".$price_amount_value.",".$price_currencycode_name.":".$price_currencycode_value.",".$price_formattedprice_name.":".$price_formattedprice_value;
									if($price==NULL || $price=="")
									{
										$price="BLANK";
									}
									$price = preg_replace('/\s+/', ' ', trim($price));
									$price="\"$price\"";
									$amountsaved="\"$amountsaved\"";
									$percentage="\"$percentage\"";
									$availability="\"$availability\"";
									$shipping="\"$shipping\"";
									fwrite($f,$price."|".$amountsaved."|".$percentage."|".$availability."|".$shipping."|");
									$Offer=1;
									$counter=35;
								}
								if($counter==34 && $Offer=="ok")
								{
									fwrite($f,"\"BLANK\"|\"BLANK\"|\"BLANK\"|\"BLANK\"|\"BLANK\"|");
									$counter=35;
								}
								if($subchild1_name=="IFrameURL")
								{	
									$subchild1 = preg_replace('/\s+/', ' ', trim($subchild1));
									$subchild1="\"$subchild1\"";
									fwrite($f,$subchild1."|");
									$IFrameURL=1;
									$counter=36;
								}
							
								if($subchild1_name=="EditorialReview")
								{	
									if($subchild1->Source=="Product Description" )
									{
										$comma_filter_content=$subchild1->Content; 
										$comma_filter_content = preg_replace('/\s+/', ' ', trim($comma_filter_content));
										$comma_filter_content="\"$comma_filter_content";
										
										fwrite($f,$comma_filter_content."<br/>");
										$EditorialReview=1;
										$counter=36;
									  }
								 if($subchild1->Source=="Amazon.com" && $flag_for_Editorial_Review==1 && $counter==36) 
										{
											$comma_filter_content=$subchild1->Content;
											$comma_filter_content = preg_replace('/\s+/', ' ', trim($comma_filter_content)); 
											 $comma_filter_content="$comma_filter_content\"";
												fwrite($f,$comma_filter_content."|");
											$flag_for_Editorial_Review++;
											$EditorialReview=1;
											$counter=37;
										}
									}
								} 
						}
						else 
						{	
							array_push($Subchild_arr,$subchild_name);
							if($subchild_name=="ParentASIN")
							{}
							else
							{
								$subchild="\"$subchild\"";
								if($subchild_name=="ASIN" && in_array("ASIN", $arr123)) 
								{
									$counter=0;
									fwrite($f,$subchild."|");
								}
								if($subchild_name!="ASIN" && !in_array("ASIN", $arr123) && $counter=="ok")
								{
									$counter=0;
									fwrite($f,"\"BLANK\"|");
								}
								if($subchild_name=="DetailPageURL" && in_array("DetailPageURL", $arr123))
								{
									$counter=1;
									fwrite($f,$subchild."|");
								}
								if($subchild_name!="DetailPageURL" && !in_array("DetailPageURL", $arr123) && $counter==0)
								{
									$counter=1;
									fwrite($f,"\"BLANK\"|");
								}
								if($subchild_name=="SalesRank" && in_array("SalesRank", $arr123))
								{
									$counter=4;
									fwrite($f,$subchild."|");
								}
							}
						}
					}
					fwrite($f,"".PHP_EOL);
				}	
				else
				{
				}
			}
	return true;
}
?>
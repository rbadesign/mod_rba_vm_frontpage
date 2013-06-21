<?php
/**
* NOTE: THIS MODULE REQUIRES AN INSTALLED VirtueMart Component!
*
* @module RBA - VirtueMart Frontpage
* @copyright Copyright (C) RBA DESIGN INTERNATIONAL LLC
* @license Commercial
*
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* www.virtuemart.net
*/
$document =& JFactory::getDocument();
$document->addStyleSheet('modules/mod_rba_vm_frontpage/assets/css/frontpage.css');

if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
if (!class_exists( 'VmModel' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'vmmodel.php');

$config = VmConfig::loadConfig();
$categoryModel = VmModel::getModel('category');
$productModel = VmModel::getModel('product');

$numColumns 	= $params->get('columns_num', 3);
$moduleClassSfx	= $params->get('moduleclass_sfx', '');
$width = floor(100/$numColumns);
$style = "width:".$width."%;";
	
	$categoryModel->_selectedOrdering = 'ordering';
	$categoryModel->_selectedOrderingDir = 'ASC';	
	$productModel->_selectedOrdering = 'product_name';
	$productModel->_selectedOrderingDir = 'ASC';	
	$categories 	=	$categoryModel->getCategories( );
	foreach ($categories as $category){
		$category->products = $productModel->getProductsInCategory($category->virtuemart_category_id);	
	}

	$totalRows 	= count($categories);
	foreach ($categories as $category){
		$totalRows 	+= count($category->products);
	}
	$rowsPerColumn = floor(($totalRows+$numColumns-1)/$numColumns);
	
	$row = 0;
	$currentRow = 0;
	echo "<div class='rba_vm_frontpage$moduleClassSfx'>";
	foreach ($categories as $category){
		if ($row == 0) echo "<div class='frontpage-column' style='$style'>";
		echo "<a href='index.php?option=com_virtuemart&view=category&virtuemart_category_id=".$category->virtuemart_category_id."'><div class='category'><span class='category-name'>".$category->category_name."</span></div></a>";
		$row += 1;
		$currentRow += 1;
		if ($row == $rowsPerColumn || $currentRow == $totalRows) {
			 echo "</div>";
			 $row = 0;
		}
		foreach($category->products as $product) {
			if ($row == 0) echo "<div class='frontpage-column' style='$style'>";
			$price = (int)($product->product_price*100);
			$main = (int)($price/100);
			$fraction = $price % 100;
			echo "<a href='".$product->link."'><div class='product'><span class='product-name'>".$product->product_name."</span><div class='product-price'><span class='product-price-main'>".$main."</span><span class='product-price-fraction'>.".($fraction<10?"0":"").$fraction."</span></div></div></a>";
			$row += 1;
			$currentRow += 1;
			if ($row == $rowsPerColumn || $currentRow == $totalRows) {
				 echo "</div>";
				 $row = 0;
			}
		}
	}
	echo "</div>";

?>
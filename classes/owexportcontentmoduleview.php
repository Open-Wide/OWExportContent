<?php

/**
*	@desc 		class OWExportContentModuleView		
*	@author 	David LE RICHE <david.leriche@openwide.fr>
*	@copyright	2013
*	@version 	1.1
*/
class OWExportContentModuleView {
	
	static $prefixDesign = 'migrate';
	
	
	/**
	*	@desc		Return the view to the module
	*	@author 	David LE RICHE <david.leriche@openwide.fr>
	*	@param		array $parmams => params module
	*				mixed $tpl => ezTemplate class loaded
	*	@return		array
	*	@copyright	2013
	*	@version 	1.1
	*/	
	public static function getView($Params, $tpl=false) {
		if (!$tpl) {
			$tpl = eZTemplate::factory();
		}
		$Result = array();
		$Result['content'] = $tpl->fetch( 'design:'.self::$prefixDesign.'/'.$Params['FunctionName'].'.tpl' ); 
		$Result['left_menu'] = "design:".self::$prefixDesign."/leftmenu.tpl"; 
 
		$Result['path'] = array( array( 
			'url'  => self::$prefixDesign.'/'.$Params['FunctionName'],
    		'text' => 'Migration Arborescence' 
		));
		return $Result;
		
	}	
	
	/**
	*	@desc		Export the node
	*	@author 	David LE RICHE <david.leriche@openwide.fr>
	*	@param		array $parmams => params module  
	*				mixed $tpl => ezTemplate class loaded
	*	@return		array
	*	@copyright	2013
	*	@version 	1.1
	*/	
	public static function getViewExportContent($Params, $tpl=false) {
		$statut = true;
		$options = false;
		$class_option = false;
		if (!$tpl) {
			$tpl = eZTemplate::factory();
		}
		
		if ( (isset($_POST['todo']) && $_POST['todo'] == 'exportNode') && (isset($_POST['rootNode']) && is_numeric($_POST['rootNode']))) {
			if (isset($_POST['options'])) {
				$options = $_POST['options']; 
				if (isset($_POST['class_option'])) {
					$class_option = $_POST['class_option'];
				}
			}
			try {
				$export = new OWExportContentExportContent($options, $class_option);
				if ($export->export($_POST['rootNode'])) {
					$tpl->setVariable('noError', 'L\'export s\'est bien déroulé');
				} else {
					$tpl->setVariable('error', 'Un pb est survenu pendant l\'export');
				}
			} catch (exception $e) {
				eZLog::write($e, 'error.log');
			}
		} elseif (isset($_POST['todo']) && $_POST['todo'] == 'exportNode') {
			$tpl->setVariable('error', 'Pb => vérifier que le noeud soit un entier.');
		}
		
		$Result = self::getView($Params, $tpl);		
		return $Result;
		
	}
	
	public static function getViewExportClass($Params, $tpl=false) {	
		if (!$tpl) {
			$tpl = eZTemplate::factory();
		}
		
		if ((isset($_POST['todo']) && $_POST['todo'] == 'exportClass')) {
			if (isset($_POST['class_option'])) {
				$option = $_POST['class_option'];
			}
			try {
				$export = new OWExportContentExportClass($option);
				if ($export->export()) {
					$tpl->setVariable('noError', 'L\'export s\'est bien déroulé');
				} else {
					$tpl->setVariable('error', 'Un pb est survenu pendant l\'export');
				}
			} catch (exception $e) {
				eZLog::write($e, 'error.log');
			}
		} 
		
		$Result = self::getView($Params, $tpl);		
		return $Result;
		
	}
	
	
	public static function d($string) {
		echo '<pre>';
		var_dump($string);
		echo '</pre>';
	}
	
	public static function dd($string) {
		echo '<pre>';
		var_dump($string);
		echo '</pre>';
		exit;
	}
}

?>
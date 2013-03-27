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
	
	public static function getViewImportContent($Params, $tpl = false) {
		if (!$tpl) {
			$tpl = eZTemplate::factory();
		}
		
		$fileList = self::getFileList('content');
		$tpl->setVariable('fileList', $fileList);
	
		$Result = self::getView($Params, $tpl);
		return $Result;
	}
	
	public static function getViewImportClass($Params, $tpl = false) {
		if (!$tpl) {
			$tpl = eZTemplate::factory();
		}
		
		$fileList = self::getFileList('class');
		$tpl->setVariable('fileList', $fileList);
		
		$Result = self::getView($Params, $tpl);
		return $Result;
	}
	
	public static function getFileList($type) {
		$fileList = array();
		$rootDirExport = eZExtension::baseDirectory().'/owexportcontent/data/'.$type.'/';
		$dir = opendir($rootDirExport);
		while($file = readdir($dir)) {
			if( $file != '.' && $file != '..' && !is_dir($dirname.$file)) {
				$fileList[] = $file;
			}
		}
		closedir($dir);
		return $fileList;
	}
	
	public static function getViewDeleteFile($Params, $tpl = false) {
		if (!$tpl) {
			$tpl = eZTemplate::factory();
		}
		if (isset($Params['UserParameters']['file'])) {
			$baseDir = eZExtension::baseDirectory().'/owexportcontent/data/';
			$file = $Params['UserParameters']['file'];
			$type = (strstr($file, 'export_arbo') ? 'content' : (strstr($file, 'export_class') ? 'class' : ''));
			if (file_exists($baseDir.$type.'/'.$file)) {
				unlink($baseDir.$type.'/'.$file);
				$tpl->setVariable('noError', 'Le fichier '.$file.' a été supprimé');
			} else {
				$tpl->setVariable('error', 'Le fichier "'.$file.'" n\'existe pas.');
			}
		} else {
			$tpl->setVariable('error', 'Pas de fichier à supprimer.');
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
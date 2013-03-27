
<?php

/**
*	@desc 		classOWExportContentExportClass		
*	@author 	David LE RICHE <david.leriche@openwide.fr>
*	@copyright	2013
*	@version 	1.1
*/
class OWExportContentExportClass {
	
	public $xml;
	public $tpl;
	public $classList;
	public $option;
	public $statut = true;
	public $rootNode;
	
	public function __construct($option = false, $rootNode = false) {
		if ($option) {
			$this->option = $option; 
		} else {
			$this->option = 'new';
		}
		$this->rootNode = $rootNode;
	} 
	
	public function export() {
		$this->classList = eZContentClass::fetchList( );
		$this->tpl = eZTemplate::factory();
		$this->createXml();
		return $this->statut;
	}	
	
	public function createXml() {
		$optList = array();
		foreach ($this->classList as $class ) {
		    $optList[$class->attribute('identifier')] = array();
		    foreach( $class->attribute('data_map') as $attribute ) {
		        $dataType = $attribute->attribute( 'data_type' );
		
		        $doc = new DOMDocument;
		        $attributeNode = $doc->createElement( 'attribute' );
		        $attributeParametersNode = $doc->createElement( 'datatype-parameters' );
		        $attributeNode->appendChild( $attributeParametersNode );
				
		        $dataType->serializeContentClassAttribute( $attribute, $attributeNode, $attributeParametersNode );
		        $doc->appendChild( $attributeNode );
		
		        $content = $doc->saveXML();
		        $content = str_replace( '<?xml version="1.0" encoding="UTF-8"?>', '', $content );
		        $content = str_replace( '<?xml version="1.0"?>', '', $content );
		
		        $optList[$class->attribute('identifier')][$attribute->attribute('identifier')] = $content;
		    }
		}
		
		$this->tpl->setVariable( 'class_list', $this->classList );
		$this->tpl->setVariable( 'opt_list', $optList );
		$this->tpl->setVariable( "class_count", count( $this->classList ) );
		$this->tpl->setVariable( "option", $this->option);
		
		
		$this->xml = $this->tpl->fetch( 'design:xmlexport/classes.tpl' );
		$this->xml = str_replace('&', '&amp;', $this->xml);
		
		try {
			$this->createFile();
		} catch (exception $e) {
			eZLog::write($e, 'error.log');
		}
	} 
	
	public function createFile() {
		$doc_xml = new DOMDocument();
		if (!$doc_xml->loadXML($this->xml)) {
			$this->statut = false;
			eZLog::write('fichier xml non valide (export de classe)', 'error.log');
		} else {
			$baseDirectory = eZExtension::baseDirectory().'/owexportcontent/data/';
			$this->createDirIfNotExist($baseDirectory);
			$baseDirectory = $baseDirectory.'class/';
			$this->createDirIfNotExist($baseDirectory);
			
			$fileName = "export_class".($this->rootNode ? "_".$this->rootNode : "").".xml";
			$fileHandle = fopen($baseDirectory.$fileName, 'w+') or die("can't open file");
			fwrite($fileHandle, $this->xml);
			fclose($fileHandle);
		}
	}
	
	public function createDirIfNotExist($baseDirectory) {
		if (!is_dir($baseDirectory)) {
			eZDir::mkdir($baseDirectory, octdec('0775'));
		}
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
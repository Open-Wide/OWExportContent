
<?php

/**
*	@desc 		class OWExportContentExportContent		
*	@author 	David LE RICHE <david.leriche@openwide.fr>
*	@copyright	2013
*	@version 	1.1
*/
class OWExportContentExportContent {
	
	public $xml;
	public $nodes;
	public $rootNode;
	public $statut = true;
	public $options = array();
	public $optionsInObject = array('OwnerID', 'SectionID');
	public $exportClass = false;
	public $class_option = false;
	
	public function __construct($options, $class_option) {
		if (is_array($options)) {
			foreach ($options as $option) {
				if ($option == 'class') {
					$this->exportClass = true;
					if ($class_option) {
						$this->class_option = $class_option;
					}
				} else {
					$this->options[] = $option;
				}
			}
		} 
	} 
	
	public function export($rootNode) {
		if($this->exportClass) {
			try {
				$exportClass = new OWExportContentExportClass($this->class_option);
				if (!$exportClass->export()) {
					return false;
				}
			} catch (exception $e) {
				eZLog::write($e, 'error.log');
			}
		}
		
		$this->rootNode = $rootNode;
		$rootNodeObject = eZContentObjectTreeNode::fetch($this->rootNode);
		$childrensRootNodeObject = $rootNodeObject->children();
		$this->nodes = array(
			'rootNode'		=> $rootNodeObject,
			'childs' 	=> $this->getRecursiveNodes($childrensRootNodeObject)
		);
		
		$this->createXml();
		return $this->statut;
	}	
	
	public function getRecursiveNodes($childrensList) {
		$nodes = array();
		foreach ($childrensList as $node) {
			$newChildrensList = $node->children();
			$nodes[] = array(
				'node'		=> $node,
				'childs' 	=> $this->getRecursiveNodes($newChildrensList)
			);
		}
		return $nodes;
	}
	
	public function createXml() {
		try {
			$this->xml = '<eZXMLImporter>';
			
			if ($this->nodes['rootNode']->ParentNodeID != '1') {
				$this->getBaseXml($this->nodes);
			} elseif (count($this->nodes['childs'])) {
				foreach ($this->nodes['childs'] as $child) {						
					$this->getBaseXml($child);
				}
			} else {
				eZLog::write('aucun object à exporter', 'error.log');
			}
			
			$this->xml .= '</eZXMLImporter>';				
			$this->createFile();
		} catch (exception $e) {
			eZLog::write($e, 'error.log');
		}
	} 
	
	public function getBaseXml($nodeElement) {
		$nodeDataMap = $nodeElement['node']->ContentObject->dataMap();
		$contentObjectXml = '<CreateContent parentNode="'.$nodeElement['node']->ParentNodeID.'">';
		$contentObjectXml .= $this->getStructureXml($nodeElement);
		$contentObjectXml .= "</CreateContent>";
		$this->xml .= $contentObjectXml;
	}
	
	public function getRecursiveXmlChilds($childrensList) {
		$contentXml = '';
		foreach ($childrensList as $children) {
			$contentXml .= '<Childs>';
			$contentXml .= $this->getStructureXml($children);
			$contentXml .= "</Childs>";
		}
		return $contentXml;
	}
	
	public function getStructureXml($data) {
		$regex_xml = "#\<\?xml.*#";		
		$contentXml = '';
		$nodeDataMap = $data['node']->ContentObject->dataMap();
		$contentXml .= '<ContentObject contentClass="'.$data['node']->ClassIdentifier.'" ';
		if (count($this->options)) {
			$contentXml .= $this->setOptionsOnXml($data['node']);
		}
		$contentXml .= '>';
		$contentXml .= '<Attributes>';
		foreach ($nodeDataMap as $attributeName => $attribute) {
			if ($attribute->dataType()->DataTypeString == 'ezobjectrelation') {
				$contentXml .= "<".$attributeName.">internal:".str_replace(array('&', '<', '>'), array('&amp;', '&lt;', '&gt;'), $attribute->toString())."</".$attributeName.">";
			} elseif (preg_match($regex_xml, $attribute->toString(), $matches)) {					
				$contentXml .= "<".$attributeName." fullxml='true'><![CDATA[".$attribute->toString()."]]></".$attributeName.">";
			} else {
				$contentXml .= "<".$attributeName.">".str_replace(array('&', '<', '>'), array('&amp;', '&lt;', '&gt;'), $attribute->toString())."</".$attributeName.">";
			}
		}
		$contentXml .= "</Attributes>";	
		if (count($data['childs'])) {
			$contentXml .= $this->getRecursiveXmlChilds($data['childs']);
		}
		$contentXml .= '<SetReference attribute="object_id" value="'.$data['node']->ContentObjectID.'" />';
		$contentXml .= "</ContentObject>";
		
		return $contentXml;
	}
	
	public function setOptionsOnXml($node) {
		$contentObjectXml = '';
		foreach ($this->options as $option) {
			if (in_array($option, $this->optionsInObject)) {
				$contentObjectXml .= ' '.$option.'="'.$node->ContentObject->{$option}.'" ';
			} else {
				$contentObjectXml .= ' '.$option.'="'.$node->{$option}.'" ';
			}
		}
		return $contentObjectXml;
	}
	
	public function createFile() {
		$doc_xml = new DOMDocument();
		if (!$doc_xml->loadXML($this->xml)) {
			$this->statut = false;
			eZLog::write('fichier xml non valide (export de contenu)', 'error.log');
		} 
		$baseDirectory = eZExtension::baseDirectory().'/owexportcontent/data/';
		$this->createDirIfNotExist($baseDirectory);
		$fileName = "export_arbo_".$this->rootNode.".xml";
		$fileHandle = fopen($baseDirectory.$fileName, 'w+') or die("can't open file");
		fwrite($fileHandle, $this->xml);
		fclose($fileHandle);
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
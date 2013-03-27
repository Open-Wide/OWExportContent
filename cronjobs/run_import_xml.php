<?php
    
    $cli = eZCLI::instance();

    $cli->output("Début du cronjob import de fichier xml");  
    
    $cli->output("Vérification si import à lancer");
    $fileListImport = verifImport();   

    if (count($fileListImport)) {
    	importFile($fileListImport);
    	deleteRunImport();
    } else {
    	$cli->output("Aucun fichier à importer");
    }
    
    $cli->output("Fin du cronjob notification");   


    
    function verifImport() {
    	$fileToParse = eZExtension::baseDirectory().'/owexportcontent/runimport/run_import.xml';
    	try {
    		$xml = simplexml_load_file($fileToParse);
    		$listeImport = getFileImport($xml);
    		return $listeImport;
    	} catch(exception $e) {
    		$cli->output("Erreur : ". $e);
    	}
    }
    
    function getFileImport($xml) {
    	$fileList = array();
    	
    	$queryContent = "//runimport/content";    	
    	if ($contents = $xml->xpath($queryContent)) {
    		$fileList['content'] = array();
			foreach ($contents as $content) {
				$fileList['content'][] = (string)$content->file;
			}
    	}
    	
    	$queryClass = "//runimport/class";
    	if ($classes = $xml->xpath($queryClass)) {
    		$fileList['class'] = array();
    		foreach ($classes as $class) {
    			$fileList['class'][] = (string)$class->file;
    		}
    	}

    	return $fileList; 
    }
    
    function importFile($fileListImport) {
    	foreach ($fileListImport as $type => $files) {
    		foreach ($files as $file) {
    			echo exec("php extension/owexportcontent/bin/php/owexportcontent.php --file=extension/owexportcontent/data/$type/$file");
    		}    		
    	}
    }
    
    function deleteRunImport() {
    	$file = eZExtension::baseDirectory().'/owexportcontent/runimport/run_import.xml';
    	if (file_exists($file)) {
			unlink($file);
    	}
    }
?>  
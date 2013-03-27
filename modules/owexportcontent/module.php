<?php

$module = array( 'name' => 'owexportcontent' );
 
$ViewList = array();
$ViewList['home'] = array( 'script' => 'home.php',
									'default_navigation_part' => 'migratenavigationpart',
									'params' => array( 'language'),
                               		'functions' => array( 'read' ));
                               		                     		
$ViewList['exportcontent'] = array( 'script' => 'exportContent.php',
									'default_navigation_part' => 'migratenavigationpart',
                               		'functions' => array( 'read' ));

$ViewList['exportclass'] = array( 'script' => 'exportClass.php',
									'default_navigation_part' => 'migratenavigationpart',
                               		'functions' => array( 'read' ));

$ViewList['importcontent'] = array( 'script' => 'importContent.php',
									'default_navigation_part' => 'migratenavigationpart',
									'functions' => array( 'read' ));

$ViewList['importclass'] = array( 'script' => 'importClass.php',
									'default_navigation_part' => 'migratenavigationpart',
									'functions' => array( 'read' ));
                               		                               		

$FunctionList = array(); 
$FunctionList['read'] = array();


?>

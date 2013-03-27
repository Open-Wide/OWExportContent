<?php

require_once( 'autoload.php' );

function changeSiteAccessSetting( $siteaccess )
{
    global $cli;
    if ( file_exists( 'settings/siteaccess/' . $siteaccess ) )
    {
        $cli->notice( 'Using siteaccess "' . $siteaccess . '" for installation from XML' );
    }
    elseif ( isExtensionSiteaccess( $siteaccess ) )
    {
        $cli->notice( 'Using extension siteaccess "' . $siteaccess . '" for installation from XML' );
        eZExtension::prependExtensionSiteAccesses( $siteaccess );
    }
    else
    {
        $cli->notice( 'Siteaccess "' . $siteaccess . '" does not exist, using default siteaccess' );
    }
}

function isExtensionSiteaccess( $siteaccessName )
{
    $siteINI            = eZINI::instance();
    $extensionDirectory = $siteINI->variable( 'ExtensionSettings', 'ExtensionDirectory' );
    $activeExtensions   = $siteINI->variable( 'ExtensionSettings', 'ActiveExtensions' );

    foreach ( $activeExtensions as $extensionName )
    {
        $possibleExtensionPath = $extensionDirectory . '/' . $extensionName . '/settings/siteaccess/' . $siteaccessName;
        if ( file_exists( $possibleExtensionPath ) )
        {
            return true;
        }
    }
    return false;
}

global $cli;

$cli    = eZCLI::instance();
$script = eZScript::instance( array( 'description'     => ( "eZ Publish XML installer\n\n" .
                                                            ""
                                                          ),
                                      'use-session'    => true,
                                      'use-modules'    => true,
                                      'use-extensions' => true
                                   )
                            );

$script->startup();

$options = $script->getOptions( "[file:][template:]",
                                "",
                                array( 'file' => 'file with xml definition',
                                       'template' => 'name of template to use' ),
                                false,
                                array( 'user' => true ));

$siteAccess = $options['siteaccess'] ? $options['siteaccess'] : false;

if ( $siteAccess )
{
    changeSiteAccessSetting( $siteAccess );
    $script->setUseSiteAccess( $siteAccess );
}

$script->initialize();

if ( !$script->isInitialized() )
{
    $cli->error( 'Error initializing script: ' . $script->initializationError() . '.' );
    $script->shutdown( 0 );
}

$cli->output( "Checking requirements..." );

$user = eZUser::fetchByName( 'admin' );
if ( $user )
{
    eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );
}

if( !( isset( $options['file'] ) || isset( $options['template'] ) ) )
{
    $cli->error( "Need at least a file or a template." );
    $script->shutdown( 1 );
}

if ( isset( $options['file'] ) )
{
    $xml = OWExportContentPrepareXML::prepareXMLFromFile( $options['file'], $cli );
}
elseif ( isset( $options['template'] ) )
{
    $xml = OWExportContentPrepareXML::prepareXMLFromTemplate( $options['template'], $cli );
}
else
{
    $cli->error( "Need at least one argument." );
    $script->shutdown( 1 );
}
$cli->output( "Trying to install data from XML ..." );

if ( $xml == '' )
{
    $cli->error( "No XML data available." );
    $script->shutdown( 1 );
}

$dom = new DOMDocument();
if ( !$dom->loadXML( $xml ) )
{
    $cli->error( "Failed to load XML." );
    $script->shutdown( 1 );
}

$xmlInstaller = new OWExportContentXmlInstaller( $dom );

if ( !$xmlInstaller->proccessXML() )
{
    $cli->error( "Errors while proccessing XML." );
    $script->shutdown( 1 );
}

$cli->output( "Finished." );
$script->shutdown();

?>

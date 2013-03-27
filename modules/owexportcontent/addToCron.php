<?php

$Module = $Params['Module'];
$Result = array();
$tpl = SQLIImportUtils::templateInit();
$importINI = eZINI::instance( 'sqliimport.ini' );
$http = eZHTTPTool::instance();

try
{
    $userLimitations = SQLIImportUtils::getSimplifiedUserAccess( 'sqliimport', 'manageimports' );
    $simplifiedLimitations = $userLimitations['simplifiedLimitations'];
    
    if( $Module->isCurrentAction( 'RequestImport' ) )
    {
        // Check if user has access to handler alteration
        $aLimitation = array( 'SQLIImport_Type' => $Module->actionParameter( 'ImportHandler' ) );
        $hasAccess = SQLIImportUtils::hasAccessToLimitation( $Module->currentModule(), 'manageimports', $aLimitation );
        if( !$hasAccess )
            return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
        
        $importOptions = $Module->actionParameter( 'ImportOptions' );
        $pendingImport = new SQLIImportItem( array(
            'handler'               => $Module->actionParameter( 'ImportHandler' ),
            'user_id'               => eZUser::currentUserID()
        ) );

        if( $importOptions )
            $pendingImport->setAttribute( 'options', SQLIImportHandlerOptions::fromText( $importOptions ) );
        $pendingImport->store();
        $Module->redirectToView( 'list' );
    }

    $importHandlers = $importINI->variable( 'ImportSettings', 'AvailableSourceHandlers' );
    $aValidHandlers = array();
    // Check if import handlers are enabled
    foreach( $importHandlers as $handler )
    {
        $handlerSection = $handler.'-HandlerSettings';
        if( $importINI->variable( $handlerSection, 'Enabled' ) === 'true' )
        {
            $handlerName = $importINI->hasVariable( $handlerSection, 'Name' ) ? $importINI->variable( $handlerSection, 'Name' ) : $handler;
            /*
             * Policy limitations check.
             * User has access to handler if it appears in $simplifiedLimitations['SQLIImport_Type']
             * or if $simplifiedLimitations['SQLIImport_Type'] is not set (no limitations)
             */
            if( ( isset( $simplifiedLimitations['SQLIImport_Type'] ) && in_array ($handler, $simplifiedLimitations['SQLIImport_Type'] ) )
                || !isset( $simplifiedLimitations['SQLIImport_Type'] ) )
                $aValidHandlers[$handlerName] = $handler;
        }
    }
    $tpl->setVariable( 'importHandlers', $aValidHandlers );
}
catch( Exception $e )
{
    $errMsg = $e->getMessage();
    SQLIImportLogger::writeError( $errMsg );
    $tpl->setVariable( 'error_message', $errMsg );
}

$Result['path'] = array(
    array(
        'url'       => false,
        'text'      => SQLIImportUtils::translate( 'extension/sqliimport', 'Request a new immediate import' )
    )
);
$Result['left_menu'] = 'design:sqliimport/parts/leftmenu.tpl';
$Result['content'] = $tpl->fetch( 'design:sqliimport/addimport.tpl' );

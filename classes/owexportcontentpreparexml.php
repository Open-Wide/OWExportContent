<?php

require_once( 'autoload.php' );

if ( !function_exists( 'readline' ) )
{
    function readline( $prompt = '' )
    {
        echo $prompt . ' ';
        return trim( fgets( STDIN ) );
    }
}

class OWExportContentPrepareXML
{

    function OWExportContentPrepareXML( )
    {
    }

    function prepareXMLFromTemplate( $templateName, $cli = false )
    {
        $template = 'design:' . $templateName . '.tpl';
        $tpl = eZTemplate::factory();

        $tpl->setVariable( 'tpl_info', false );

        $content = $tpl->fetch( $template );
        $tplInfo = false;

        if ( $tpl->variable( "tpl_info" ) !== false )
        {
            $tplInfo = $tpl->variable( "tpl_info" );
        }
        if ( is_array( $tplInfo ) )
        {
            foreach ( $tplInfo as $var => $info )
            {
                if ( isset( $info['info'] ) )
                {
                    $query = $info['info'];
                }
                else
                {
                    $query = 'Info for ' . $var;
                }
                $default = '';
                if ( isset( $info['default'] ) )
                {
                    $default = $info['default'];
                }
                $value = eZPrepareXML::getUserInput( "Please enter \"" . $query . "\" (" . $default . "): ", $default );
                $tpl->setVariable( $var, $value );
            }
        }
        $content = $tpl->fetch( $template );
        $xml     = $tpl->variable( "xml_data" );
        return $xml;
    }

    function prepareXMLFromFile( $fileName, $cli = false )
    {
        if ( !file_exists( $fileName ) )
        {
            $cli->error( "Can not open file \"$fileName\"." );
            return false;
        }

        $xml = file_get_contents( $fileName );

        if ( !$xml )
        {
            $cli->error( "File \"$fileName\" is empty." );
            return false;
        }
        return $xml;
    }

    function getUserInput( $query, $defaultValue = false, $acceptValues = false )
    {
        $validInput = false;
        while ( !$validInput )
        {
            $input = readline( $query );
            if ( $acceptValues === false ||
                 in_array( $input, $acceptValues ) )
            {
                $validInput = true;
            }
        }
        if ( !$input )
        {
            return $defaultValue;
        }
        else
        {
            return $input;
        }
    }
}

?>

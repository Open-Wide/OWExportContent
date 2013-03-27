<?php

include_once('extension/owexportcontent/classes/owexportcontenthandler.php');

class OWExportContentProccessInformation extends OWExportContentHandler
{

    function OWExportContentProccessInformation( )
    {
    }

    function execute( &$xml )
    {
        $comment = $xml->getAttribute( 'comment' );
        $this->writeMessage( "Step " . $this->increaseCouter() . ": " . $comment, 'notice' );
    }

    static public function handlerInfo()
    {
        return array( 'XMLName' => 'ProccessInformation', 'Info' => 'Write info about next step.' );
    }
}

?>

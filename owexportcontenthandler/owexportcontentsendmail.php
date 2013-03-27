<?php

include_once('extension/owexportcontent/classes/owexportcontenthandler.php');

class OWExportContentSendMail extends OWExportContentHandler
{

    function OWExportContentSendMail( )
    {
    }

    function execute( $xml )
    {
        $template   = $xml->getAttribute( 'template' );
        $receiverID = $xml->getAttribute( 'receiver' );
        $nodeID     = $xml->getAttribute( 'node' );

        $ini = eZINI::instance();
        $mail = new eZMail();
        $tpl = eZTemplate::factory();

        $node = eZContentObjectTreeNode::fetch( $nodeID );
        if ( !$node )
        {
            $node = eZContentObjectTreeNode::fetch( 2 );
        }

        $emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
        if ( !$emailSender )
            $emailSender = $ini->variable( "MailSettings", "AdminEmail" );

        $receiver = eZUser::fetch( $receiverID );
        if ( !$receiver )
        {
            $emailReceiver = $emailSender;
        }
        else
        {
            $emailReceiver = $receiver->attribute( 'email' );
        }

        $tpl->setVariable( 'node', $node );
        $tpl->setVariable( 'receiver', $receiver );

        $body = $tpl->fetch( 'design:' . $template );
        $subject = $tpl->variable( 'subject' );

        $mail->setReceiver( $emailReceiver );
        $mail->setSender( $emailSender );
        $mail->setSubject( $subject );
        $mail->setBody( $body );

        $mailResult = eZMailTransport::send( $mail );
        return $mailResult;
    }

    static public function handlerInfo()
    {
        return array( 'XMLName' => 'SendMail', 'Info' => 'send mail' );
    }
}

?>

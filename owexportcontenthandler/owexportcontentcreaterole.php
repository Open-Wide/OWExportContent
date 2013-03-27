<?php

include_once('extension/owexportcontent/classes/owexportcontenthandler.php');

class OWExportContentCreateRole extends OWExportContentHandler
{

    function OWExportContentCreateRole( )
    {
    }

    function execute( $xml )
    {
        $roleList = $xml->getElementsByTagName( 'Role' );
        $refArray = array();
        foreach ( $roleList as $roleNode )
        {

            $roleName              = $roleNode->getAttribute( 'name' );
            $createRoleIfNotExists = $roleNode->getAttribute( 'createRole' );
            $replacePolicies       = $roleNode->getAttribute( 'replacePolicies' );
            $referenceID           = $roleNode->getAttribute( 'referenceID' );

            $this->writeMessage( "\tRole '$roleName' will be created." , 'notice' );


            $rolePolicyList = $roleNode->getElementsByTagName( 'Policy' );
            $policyList = array();
            foreach ( $rolePolicyList as $policyNode )
            {
                $policyModule   = $policyNode->getAttribute( 'module' );
                $policyFunction = $policyNode->getAttribute( 'function' );

                $policyLimitationList = array();
                $policyLimitationNodeList = $policyNode->getElementsByTagName( 'Limitations' )->item( 0 );
                if ( $policyLimitationNodeList )
                {
                    $limitations = $policyLimitationNodeList->childNodes;
                    foreach ( $limitations as $limitation )
                    {
                        if ( $limitation->nodeType == XML_ELEMENT_NODE )
                        {
                            if ( !array_key_exists( $limitation->nodeName, $policyLimitationList ) )
                            {
                                $policyLimitationList[$limitation->nodeName] = array();
                            }
                            $policyLimitationList[$limitation->nodeName][] = $this->getLimitationValue($limitation->nodeName, $limitation->textContent);
                        }
                    }
                }
                $policyList[] = array( 'module'     => $policyModule,
                                       'function'   => $policyFunction,
                                       'limitation' => $policyLimitationList );
            }
            $role = eZRole::fetchByName( $roleName );
            if( is_object( $role ) || ( $createRoleIfNotExists == "true" ) )
            {
                if( !is_object( $role ) )
                {
                    $role = eZRole::create( $roleName );
                    $role->store();
                }

                $roleID = $role->attribute( 'id' );
                if( count( $policyList ) > 0 )
                {
                    if ( $replacePolicies == "true" )
                    {
                        $role->removePolicies();
                        $role->store();
                    }
                    foreach( $policyList as $policyDefinition )
                    {
                        if( isset( $policyDefinition['limitation'] ) )
                        {
                            $role->appendPolicy( $policyDefinition['module'], $policyDefinition['function'], $policyDefinition['limitation'] );
                        }
                        else
                        {
                            $role->appendPolicy( $policyDefinition['module'], $policyDefinition['function'] );
                        }
                    }
                }

                if ( $referenceID )
                {
                    $refArray[$referenceID] = $role->attribute( 'id' );
                }
            }
            else
            {
                $this->writeMessage( "\tRole '$roleName' doesn't exist." , 'notice' );
            }
        }
        $this->addReference( $refArray );
    }

    /**
     * Returns a valid limitation value to be saved in database
     *
     * @since 1.2.0
     * @param string $limitationType	Limitation type
     * @param string $limitationValue	Human readable input value
     * @return string	Value to be saved in database
     */
    private function getLimitationValue( $limitationType, $limitationValue ){
        $limitationValue = $this->getReferenceID( $limitationValue );
        switch( $limitationType )
        {
            case 'Class':
            case 'ParentClass':
                if( !is_int( $limitationValue ) )
                {
                    $class = eZContentClass::fetchByIdentifier( $limitationValue );
                    if( $class )
                    {
                        $limitationValue = $class->ID;
                    }
                }
                break;

            case 'Subtree':
                //Subtree limitations need to store path_string instead of node_id
                $val = (int) $limitationValue;
                if( $val > 0 )
                {
                    $node = eZContentObjectTreeNode::fetch( $val );
                    $limitationValue = $node->attribute( 'path_string' );
                }
   	            break;

            case 'SiteAccess':
                //siteaccess name must be crc32'd
                if( !is_int( $limitationValue ) )
                {
	               $limitationValue = eZSys::ezcrc32( $limitationValue );
                }
                break;

            case 'Section':
                if( !is_int( $limitationValue ) )
                {
                   $section = eZSection::fetchByIdentifier( $limitationValue );
	               if( $section )
	               {
	                   $limitationValue = $section->attribute( 'id' );
	               }
                }
                break;
        }

        return $limitationValue;
    }

    static public function handlerInfo()
    {
        return array( 'XMLName' => 'CreateRole', 'Info' => 'create role' );
    }
}

?>

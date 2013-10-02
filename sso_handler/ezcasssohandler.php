<?php

require_once 'extension/owcas/lib/phpCAS/CAS.php';

class eZCASSSOHandler {

    protected $version = '2.0';
    protected $serveur;
    protected $port = 443;
    protected $path = '/cas';

    public function __construct( ) {
        $CASIni = eZINI::instance( 'owcas.ini' );
        if( $CASIni->hasVariable( 'CAS', 'Server' ) ) {
            $this->serveur = $CASIni->variable( 'CAS', 'Server' );
        } else {
            eZDebug::error( 'eZCASSSOHandler::[CAS]Server not defined in owcas.ini.' );
            return FALSE;
        }
        if( $CASIni->hasVariable( 'CAS', 'Version' ) ) {
            $this->version = $CASIni->variable( 'CAS', 'Version' );
        }
        if( $CASIni->hasVariable( 'CAS', 'Port' ) ) {
            $this->port = $CASIni->variable( 'CAS', 'Port' );
        }
        if( $CASIni->hasVariable( 'CAS', 'Path' ) ) {
            $this->path = $CASIni->variable( 'CAS', 'Path' );
        }
    }

    static function initialize( $version, $serveur, $port, $path ) {
        try {
            phpCAS::_validateClientExists( );
        } catch( CAS_OutOfSequenceBeforeClientException $e ) {
            phpCAS::client( $version, $serveur, intval( $port ), $path );
            if( is_callable( 'phpCAS::setNoCasServerValidation' ) ) {
                phpCAS::setNoCasServerValidation( );
            }
        }
    }

    /**
     * Retournez un objet eZUser a loguer dans eZ Publish
     * Si l'authentification echoue, retournez false
     */
    public function handleSSOLogin( ) {
        eZCASSSOHandler::initialize( $this->version, $this->serveur, $this->port, $this->path );
        if( phpCAS::isAuthenticated( ) ) {
            $currentUser = eZUser::fetchByName( phpCAS::getUser( ) );
        } else {
            eZHTTPTool::redirect( phpCAS::getServerLoginURL( ) );
        }
        if( empty( $currentUser ) ) {
            $currentUser = FALSE;
        }
        return $currentUser;
    }

}

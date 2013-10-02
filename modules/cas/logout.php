<?php

$Module = $Params['Module'];

$version = '2.0';
$serveur;
$port = 443;
$path = '/cas';
$http = eZHTTPTool::instance( );

$ini = eZINI::instance( 'owcas.ini' );
if( $ini->hasVariable( 'CAS', 'Server' ) ) {
    $serveur = $ini->variable( 'CAS', 'Server' );
} else {
    eZDebug::error( 'eZCASSSOHandler::[CAS]Server not defined in owcas.ini.' );
    return FALSE;
}
if( $ini->hasVariable( 'CAS', 'Version' ) ) {
    $version = $ini->variable( 'CAS', 'Version' );
}
if( $ini->hasVariable( 'CAS', 'Port' ) ) {
    $port = $ini->variable( 'CAS', 'Port' );
}
if( $ini->hasVariable( 'CAS', 'Path' ) ) {
    $path = $ini->variable( 'CAS', 'Path' );
}

eZCASSSOHandler::initialize( $version, $serveur, $port, $path );
phpCAS::handleLogoutRequests( );
$user = eZUser::instance( );
$user->logoutCurrent( );
$http->removeSessionVariable( 'phpCAS' );
return $Module->redirectTo( phpCAS::getServerLogoutURL( ) );
?>
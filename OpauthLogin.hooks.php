<?php
/**
 * Hooks class declaration for mediawiki extension OpauthLogin
 *
 * @file OpauthLogin.hooks.php
 * @ingroup OpauthLogin
 */

class OpauthLoginHooks {

    public static function onOpauthUserAuthorized( $provider, $uid, $info, $raw )
    {

        global $wgUser, $wgOut;

        // Called when user was successfully authenticated from Opauth

        // This function should compare UID with internal storage and decide to create new account for this user
        // or load existing user from database

        if( OpauthLogin::isUidLinked( $uid, $provider ) ) {

            // Login existing user into system
            $user = OpauthLogin::getUidUser( $uid, $provider );

        }else{

            // Create new user from external data

            /**
             * We set UID based string as user name in mediawiki to avoid
             * user nicknames override and collisions problems. We store external user name into
             * "real name" field of user object. This should be supported in skin.
             */
            $user = User::newFromName( md5( $provider.$uid ) . '_' . $uid, false  );

            $user->setRealName( $info['name'] );
            if( array_key_exists('email', $info) ) {
                if( !OpauthLogin::isEmailCollate( $info['email'] ) ) {
                    $user->setEmail( $info['email'] );
                }
            }
            $user->setPassword( md5( $info['name'] . time() ) );
            $user->setToken();
            $user->confirmEmail(); // Mark email address as confirmed by default
            $user->addToDatabase(); // Commit changes to database

            OpauthLogin::addUidLink( $uid, $provider, $user->getId() );

            // Update site stats
            $ssUpdate = new SiteStatsUpdate(0, 0, 0, 0, 1);
            $ssUpdate->doUpdate();

        }

        // Replace current user with new one
        $wgUser = $user;
        $wgUser->setCookies( null, null, true );

        $wgOut->redirect( Title::newMainPage()->getFullURL() );

        return true;
    }

    public static function onUserLoadFromSession( $user, &$result ) {

        // Called when user was loaded (or not) from session

        return true;
    }

    /**
     * @param DatabaseUpdater $updater
     */
    public static function onLoadExtensionSchemaUpdates( $updater ) {
        global $wgOpauthLoginDir;
        $updater->addExtensionTable(
            'opauth_login',
            $wgOpauthLoginDir .'/schema/opauth_login.sql'
        );
    }

}
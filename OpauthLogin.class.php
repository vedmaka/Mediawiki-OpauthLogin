<?php
/**
 * Class declaration for mediawiki extension OpauthLogin
 *
 * @file OpauthLogin.class.php
 * @ingroup OpauthLogin
 */

class OpauthLogin {

    public static function addUidLink( $uid, $provider, $user_id )
    {
        $dbw = wfGetDB(DB_MASTER);
        $dbw->insert(
            'opauth_login',
            array(
                'uid' => $uid,
                'provider' => $provider,
                'user_id' => $user_id
            )
        );
        $dbw->commit();
    }

    /**
     * Checks if external UID already linked to some user account
     * @param string $uid
     * @param string $provider
     * @return bool
     */
    public static function isUidLinked( $uid, $provider )
    {
        $dbr = wfGetDB(DB_SLAVE);
        $result = $dbr->select(
            'opauth_login',
            '*',
            array(
                'uid' => $uid,
                'provider' => $provider
            )
        );
        if( $result && $result->numRows() ) {
            return true;
        }

        return false;
    }

    /**
     * Fetch Mediawiki User from external uid and provider pair
     * @param string $uid
     * @param string $provider
     * @return bool|User
     */
    public static function getUidUser( $uid, $provider )
    {
        $dbr = wfGetDB(DB_SLAVE);
        $result = $dbr->selectRow(
            'opauth_login',
            '*',
            array(
                'uid' => $uid,
                'provider' => $provider
            )
        );
        if( $result ) {
            $user = User::newFromId( $result->user_id );
            if( $user && ( $user->getId() != 0 ) ) {
                return $user;
            }
        }
        return false;
    }

    /**
     * Checks if there is already user with same email exists in Mediawiki database
     * @param string $email
     * @return bool
     */
    public static function isEmailCollate( $email )
    {
        $dbr = wfGetDB(DB_SLAVE);
        $result = $dbr->selectRow(
            'user',
            'user_email',
            array(
                'user_email' => $email
            )
        );
        if( $result ) {
            return true;
        }
        return false;
    }

}
<?php

namespace AppBundle\Tool;

/**
 *
 * Helper for signing a quizz form
 *
 * Class Signature
 * @package AppBundle\Tool
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class Signature
{

    /**
     * generate a hash. This hash is used to verify if a quizz form has been modified or not.
     *
     * @param $movieId
     * @param $actorId
     * @param $secret
     * @return string
     */
    public static function generate($movieId, $actorId, $secret)
    {
        $key = pack("H*", $secret);

        return strtolower(hash_hmac("sha1", sprintf('%s:%s', $movieId, $actorId), $key));
    }
}

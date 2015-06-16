<?php

namespace AppBundle\Tool;

/**
 * Class Signature
 * @package AppBundle\Tool
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class Signature
{

    public static function generate($movieId, $actorId, $secret)
    {
        $key = pack("H*", $secret);

        return strtolower(hash_hmac("sha1", sprintf('%s:%s', $movieId, $actorId), $key));
    }
}

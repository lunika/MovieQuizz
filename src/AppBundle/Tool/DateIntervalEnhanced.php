<?php

namespace AppBundle\Tool;

/**
 * This class come from the php.net documentation
 * http://php.net/manual/en/dateinterval.format.php#113204
 *
 * Class DateIntervalEnhanced
 * @package AppBundle\Tool
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class DateIntervalEnhanced extends \DateInterval
{

    public function recalculate()
    {
        $from = new \DateTime();
        $to = clone $from;
        $to = $to->add($this);
        $diff = $from->diff($to);
        foreach ($diff as $k => $v) {
            $this->$k = $v;
        }
        return $this;
    }
}

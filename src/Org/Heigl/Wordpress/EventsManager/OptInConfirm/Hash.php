<?php
/**
 * Copyright (c) 2012-2012 Andreas Heigl<andreas@heigl.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  (c) 2012-2012 Andreas Heigl <andreas@heigl.org>
 * @license    http://www.opensource.org/license/MIT MIT-License
 * @version    1.0.2
 * @since      17.08.2012
 */

namespace Org\Heigl\Wordpress\EventsManager\OptInConfirm;

use Org\Heigl\Wordpress\Wordpress;
use \DateTime;

/**
 * Handling of the Hash.
 *
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  (c) 2012-2012 Andreas Heigl <andreas@heigl.org>
 * @license    http://www.opensource.org/license/MIT MIT-License
 * @version    1.0.2
 * @since      17.08.2012
 */
class Hash
{

    protected static $timeout = 'P1W';

    /**
     * The ID of the hash
     *
     * @var int $id
     */
    protected $id = null;

    /**
     * The Hash to be used
     *
     * @var string $hash
     */
    protected $hash = null;

    /**
     * The booking-id
     *
     * @var int $booking
     */
    protected $booking = null;

    /**
     * The Creation-Date
     *
     * @var DateTime $creationDate
     */
    protected $creationDate = null;

    /**
     * The COnfirmation Date
     *
     * @var DateTime $confirmationDate
     */
    protected $confirmationDate = null;

    /**
     * Set the COnfirmation Date
     *
     * @param DateTime|string $confirmationDate The date the booking was confirmed
     *
     * @return Hash
     */
    public function setConfirmationDate($confirmationDate)
    {
        if (null === $confirmationDate) {
            $confirmationDate = '0000-00-00 00:00:00';
        }
        if ( ! $confirmationDate instanceof DateTime) {
            $confirmationDate = new DateTime($confirmationDate);
        }
        $this->confirmationDate = $confirmationDate;
        return $this;
    }

    /**
     * Get the confirmation date
     *
     * @return DateTime|null
     */
    public function getConfirmationDate()
    {
        if (! $this->confirmationDate instanceof DateTime) {
            return null;
        }
        return $this->confirmationDate;
    }

    /**
     * Set the creation date
     *
     * @param DateTime|string $creationDate The date the booking was created
     *
     * @return Hash
     */
    public function setCreationDate($creationDate)
    {
        if ( ! $creationDate instanceof DateTime) {
            $creationDate = new DateTime($creationDate);
        }
        $this->creationDate = $creationDate;
        return $this;
    }

    /**
     * Get the creation date
     *
     * @return DateTime|null
     */
    public function getCreationDate()
    {
        if (! $this->creationDate instanceof DateTime) {
            return null;
        }
        return $this->creationDate;
    }

    /**
     * Set the booking-ID
     *
     * @param int $booking The ID of the booking that is associated with this hash
     *
     * @return Hash
     */
    public function setBooking($booking)
    {

        $this->booking = (int) $booking;
        return $this;
    }

    /**
     * Get the booking-ID
     *
     * @return int
     */
    public function getBooking()
    {
        return $this->booking;
    }

    /**
     * Set the Hash
     *
     * @param string $hash hash to be used as reference
     *
     * @return Hash
     */
    public function setHash($hash)
    {

        $this->hash = $hash;
        return $this;
    }

    /**
     * Get the Hash
     *
     * @return string
     */
    public function getHash()
    {
        if ( null === $this->hash) {
            $this->hash = sha1(mt_rand(0, 10000) . ':' . microtime());
        }
        return $this->hash;
    }

    /**
     * Set the ID
     *
     * @param int $id Id to be used
     *
     * @return Hash
     */
    public function setId($id)
    {

        $this->id = $id;
        return $this;
    }

    /**
     * Get the ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Create a new Instance and populate it with basic informations
     *
     * @param int $booking The hash to retrieve
     *
     * @return Hash
     */
    public static function getInstance($hash)
    {
        $values = Db::load($hash);
        $hash = new Hash();
        if ($values) {
            $hash->setId($values['id'])
                 ->setBooking($values['booking'])
                 ->setCreationDate($values['creation_date'])
                 ->setConfirmationDate($values['confirmation_date'])
                 ->setHash($values['hash']);
        }
        return $hash;
    }

    /**
     * Check whether the Hash is already confirmed
     *
     * @return boolean
     */
    public function isConfirmed()
    {
        if ( 0 >= $this->getConfirmationDate()->format('Ymd')) {
            return false;
        }
        return true;
    }

    /**
     * Check whether the hash is still valid
     *
     * @return boolean
     */
    public function isValid()
    {
        $now = new DateTime();
        if ( $now > $this->getCreationDate()->add(new \DateInterval(Wordpress::get_option('em_oic_hash_lifetime')))) {
            return false;
        }
        return true;
    }

    /**
     * Store the hash to the database
     *
     * @return int
     */
    public function store()
    {
        if (null === $this->getId()) {
            return Db::insert(array(
                'booking'           => $this->getBooking(),
                'confirmation_date' => $this->getConfirmationDate(),
                'creation_date'     => $this->getCreationDate(),
                'hash'              => $this->getHash(),
            ));
        }
        return Db::update(array(
            'id'                => $this->getId(),
            'booking'           => $this->getBooking(),
            'confirmation_date' => $this->getConfirmationDate(),
            'creation_date'     => $this->getCreationDate(),
            'hash'              => $this->getHash(),
        ));
    }

    /**
     * Create a new Instance for a certain booking
     *
     * @return void
     */
    public function factory($booking)
    {
        $inst = new Hash();
        $inst->setHash(sha1(mt_rand(0, 10000) . ':' . microtime()));
        $inst->setCreationDate(new \DateTime());
        $inst->setBooking($booking);
        return $inst;
    }

    /**
     * Create an instance
     */
}

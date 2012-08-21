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
 * @version    1.0.3
 * @since      17.08.2012
 */

namespace Org\Heigl\Wordpress\EventsManager\OptInConfirm;

use Org\Heigl\Wordpress\Wordpress;
use \DateTime;

/**
 * Handling of the database.
 *
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  (c) 2012-2012 Andreas Heigl <andreas@heigl.org>
 * @license    http://www.opensource.org/license/MIT MIT-License
 * @version    1.0.3
 * @since      17.08.2012
 */
class Db
{
    const VERSION = '1.0.0';

    /**
     * Get the tablename
     *
     * @return string
     */
    protected static function getTableName()
    {
        return self::getWpdb()->prefix . 'events_manager_opt_in_confirm';
    }

    /**
     * Get the Wordpress-Database-class
     *
     * @return ??
     */
    public static function getWpDb()
    {
        global $wpdb;
        return $wpdb;
    }

    /**
     * Bring the database to the current version
     *
     * @return void
     */
    public static function upgrade()
    {
        $currentVersion 1.0.3
        if ( $currentVersion 1.0.3
            $sql = file_get_contents(__DIR__ . '/share/sql/1.0.sql');
            $sql = str_replace('__TABLE__', self::getTableName(), $sql);
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            Wordpress::dbDelta($sql);
            Wordpress::add_option('em_oic_db_version', '1.0');
        }
    }

    /**
     * Insert a new entry into the DB
     *
     * @param array $parameters
     *
     * @return int
     */
    public static function insert($parameters)
    {
        $sql = array();
        foreach ($parameters as $key => $value) {
            if ( null === $value) {
                continue;
            }
            if ($value instanceof \DateTime) {
                $value = $value->format('c');
            }
            $sql[] = $key .'=\'' . $value . '\'';
        }
        $sql = 'INSERT INTO __TABLE__ SET ' . implode(', ', $sql);

        return self::getWpDb()->query(str_replace('__TABLE__', self::getTableName(), $sql));
    }

    /**
     * Update a given entry in the DB
     *
     * @param array $parameters
     *
     * @return int
     */
    public static function update($parameters)
    {
        $sql = array();
        if ( ! isset($parameters['id'])) {
            throw new \InvalidArgumentException('Sorr, but no update without ID');
        }
        foreach ($parameters as $key => $value) {
            if ( null === $value) {
                continue;
            }
            if ($value instanceof \DateTime) {
                $value = $value->format('c');
            }
            $sql[] = $key .'=\'' . $value . '\'';
        }
        $sql = 'UPDATE __TABLE__ SET ' . implode(', ', $sql) . ' WHERE id=\'' . $parameters['id'] . '\'';
error_log($sql);
        return self::getWpDb()->query(str_replace('__TABLE__', self::getTableName(), $sql));
    }

    /**
     * load a given entry from the DB
     *
     * @param string $hash trhe hash to load
     *
     * @return array
     */
    public static function load($hash)
    {
        $sql = 'SELECT * FROM __TABLE__ WHERE hash=\'' . $hash . '\'';

        error_log($sql);

        return self::getWpDb()->get_row(str_replace('__TABLE__', self::getTableName(), $sql), ARRAY_A);
    }

    /**
     * Get all Hashes that became invalid between the given dates and now
     *
     * @param DateTime $start The startdate
     * @param DateTime $end   The End-date
     *
     * @return array
     */
    public static function getInvalidBetween(\DateTime $start, \DateTime $end)
    {
        $sql = "SELECT booking FROM __TABLE__ WHERE creation_date >= '%s' AND creation_date <= '%s'";
        $sql = sprintf($sql, $start->format('c'), $end->format('c'));
        $sql = str_Replace('__TABLE__', self::getTableName(), $sql);
        return self::getWpDb()->get_col($sql);
    }
}

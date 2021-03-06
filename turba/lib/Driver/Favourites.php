<?php
/**
 * Read-only Turba directory driver implementation for favourite
 * recipients. Relies on the contacts/favouriteRecipients API method.
 *
 * Copyright 2010-2011 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL).  If you did
 * did not receive this file, see http://www.horde.org/licenses/asl.php.
 *
 * @author   Jan Schneider <jan@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/asl.php ASL
 * @package  Turba
 */
class Turba_Driver_Favourites extends Turba_Driver
{
    /**
     * Checks if the current user has the requested permissions on this
     * source.
     *
     * @param integer $perm  The permission to check for.
     *
     * @return boolean  True if the user has permission, otherwise false.
     */
     public function hasPermission($perm)
     {
         switch ($perm) {
         case Horde_Perms::DELETE:
         case Horde_Perms::EDIT:
             return false;

         default:
             return true;
         }
     }

    /**
     * Always returns true because the driver is read-only and there is
     * nothing to remove.
     *
     * @param string $user  The user's data to remove.
     *
     * @return boolean  Always true.
     */
    public function removeUserData($user)
    {
        return true;
    }

    /**
     * Searches the favourites list with the given criteria and returns a
     * filtered list of results. If the criteria parameter is an empty array,
     * all records will be returned.
     *
     * @param array $criteria  Array containing the search criteria.
     * @param array $fields    List of fields to return.
     * @param array $blobFields  A list of fields that contain binary data.
     *
     * @return array  Hash containing the search results.
     * @throws Turba_Exception
     */
    protected function _search(array $criteria, array $fields, array $blobFields = array())
    {
        $results = array();

        foreach ($this->_getAddressBook() as $key => $contact) {
            $found = !isset($criteria['OR']);
            foreach ($criteria as $op => $vals) {
                if ($op == 'AND') {
                    foreach ($vals as $val) {
                        if (isset($contact[$val['field']])) {
                            switch ($val['op']) {
                            case 'LIKE':
                                if (stristr($contact[$val['field']], $val['test']) === false) {
                                    continue 4;
                                }
                                $found = true;
                                break;
                            }
                        }
                    }
                } elseif ($op == 'OR') {
                    foreach ($vals as $val) {
                        if (isset($contact[$val['field']])) {
                            switch ($val['op']) {
                            case 'LIKE':
                                if (empty($val['test']) ||
                                    stristr($contact[$val['field']], $val['test']) !== false) {
                                    $found = true;
                                    break 3;
                                }
                            }
                        }
                    }
                }
            }
            if ($found) {
                $results[$key] = $contact;
            }
        }

        return $results;
    }

    /**
     * Reads the given data from the API method and returns the result's
     * fields.
     *
     * @param array $criteria  Search criteria.
     * @param mixed $id        Data identifier.
     * @param string $owner    Filter on contacts owned by this owner.
     * @param array $fields    List of fields to return.
     *
     * @return arry  Hash containing the search results.
     * @throws Turba_Exception
     */
    protected function _read(array $criteria, $ids, $owner, array $fields)
    {
        $book = $this->_getAddressBook();

        $results = array();
        if (!is_array($ids)) {
            $ids = array($ids);
        }

        foreach ($ids as $id) {
            if (isset($book[$id])) {
                $results[] = $book[$id];
            }
        }

        return $results;
    }

    /**
     * TODO
     *
     * @throws Turba_Exception
     */
    protected function _getAddressBook()
    {
        global $registry;

        if (!$registry->hasMethod('contacts/favouriteRecipients')) {
            throw new Turba_Exception(_("No source for favourite recipients exists."));
        }

        try {
            $addresses = $registry->call('contacts/favouriteRecipients', array($this->_params['limit']));
        } catch (Horde_Exception $e) {
            if ($e->getCode() == Horde_Registry::AUTH_FAILURE ||
                $e->getCode() == Horde_Registry::NOT_ACTIVE ||
                $e->getCode() == Horde_Registry::PERMISSION_DENIED) {
                return array();
            }
            throw new Turba_Exception($e);
        } catch (Exception $e) {
            throw new Turba_Exception($e);
        }

        $addressbook = array();
        foreach ($addresses as $address) {
            $addressbook[$address] = array('email' => $address);
        }

        return $addressbook;
    }

}

<?php
/**
 * Tests for the Kolab implementation of the free/busy system.
 *
 * PHP version 5
 *
 * @category Kolab
 * @package  Kolab_FreeBusy
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.fsf.org/copyleft/lgpl.html LGPL
 * @link     http://pear.horde.org/index.php?package=Kolab_FreeBusy
 */

/**
 * A mockup for the main application controller.
 *
 * Copyright 2004-2009 Klarälvdalens Datakonsult AB
 *
 * See the enclosed file COPYING for license information (LGPL). If you did not
 * receive this file, see
 * http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html.
 *
 * @category Kolab
 * @package  Kolab_FreeBusy
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.fsf.org/copyleft/lgpl.html LGPL
 * @link     http://pear.horde.org/index.php?package=Kolab_FreeBusy
 */
class Horde_Kolab_FreeBusy_Stub_Controller_Freebusy
extends Horde_Kolab_FreeBusy_Controller_Base
{
    /**
     * Trigger regeneration of free/busy data in a calender.
     *
     * @return NULL
     */
    public function trigger(Horde_Controller_Response $response)
    {
        $type = isset($this->params->type) ? ' and retrieved data of type "' . $this->params->type . '"' : '';
        $response->setBody('triggered folder "' . $this->params->folder . '"' . $type);
    }

    /**
     * Fetch the free/busy data for a user.
     *
     * @return NULL
     */
    public function fetch(Horde_Controller_Response $response)
    {
        $response->setBody('fetched "' . $this->params->type . '" data for user "' . $this->params->owner . '"');
    }

    /**
     * Regenerate the free/busy cache data.
     *
     * @return NULL
     */
    public function regenerate(Horde_Controller_Response $response)
    {
        $response->setBody('regenerated');
    }

    /**
     * Delete data for a specific user.
     *
     * @return NULL
     */
    public function delete(Horde_Controller_Response $response)
    {
        $response->setBody('deleted data for user "' . $this->params->owner . '"');
    }
}
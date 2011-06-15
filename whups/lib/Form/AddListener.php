<?php
/**
 * Copyright 2001-2002 Robert E. Coyle <robertecoyle@hotmail.com>
 * Copyright 2001-2011 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (BSD). If you
 * did not receive this file, see http://www.horde.org/licenses/bsdl.php.
 */

class Whups_Form_AddListener extends Horde_Form
{
    public function __construct(&$vars, $title = '')
    {
        parent::__construct($vars, $title);

        $this->addHidden('', 'id', 'int', true, true);
        $this->addVariable(_("Email address to notify"), 'add_listener', 'email', true);
    }

}
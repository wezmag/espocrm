<?php
/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2020 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: https://www.espocrm.com
 *
 * EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EspoCRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word.
 ************************************************************************/

namespace Espo\Controllers;

use Espo\Core\{
    Exceptions\Forbidden,
    Exceptions\BadRequest,
    Acl,
    Api\Request,
};

use Espo\{
    Entities\User,
    Tools\Kanban\Orderer,
};

class KanbanOrder
{
    protected $orderer;
    protected $acl;
    protected $user;

    public function __construct(Orderer $orderer, Acl $acl, User $user)
    {
        $this->orderer = $orderer;
        $this->acl = $acl;
        $this->user = $user;
    }

    public function postActionStore(Request $request)
    {
        $data = $request->getParsedBody();

        $entityType = $data->entityType;
        $group = $data->group;
        $ids = $data->ids;

        if (empty($entityType) || !is_string($entityType)) {
            throw new BadRequest();
        }

        if (empty($group) || !is_string($group)) {
            throw new BadRequest();
        }

        if (!is_array($ids)) {
            throw new BadRequest();
        }

        if (! $this->acl->check($entityType, 'read')) {
            throw new Forbidden();
        }

        if ($this->user->isPortal()) {
            throw new Forbidden();
        }

        $this->orderer
            ->setEntityType($entityType)
            ->setGroup($group)
            ->setUserId($this->user->id)
            ->order($ids);

        return true;
    }
}

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

namespace Espo\Tools\Kanban;

use Espo\Core\{
    ORM\EntityManager,
    Utils\Metadata,
};

use LogicException;

class OrdererProcessor
{
    private $entityType;

    private $group;

    private $userId = null;

    private $maxNumber = 50;

    private $entityManager;

    private $metadata;

    public function __construct(EntityManager $entityManager, Metadata $metadata)
    {
        $this->entityManager = $entityManager;
        $this->metadata = $metadata;
    }

    public function setEntityType(string $entityType) : self
    {
        $this->entityType = $entityType;

        return $this;
    }

    public function setGroup(string $group) : self
    {
        $this->group = $group;

        return $this;
    }

    public function setUserId(string $userId) : self
    {
        $this->userId = $userId;

        return $this;
    }

    public function setMaxNumber(int $maxNumber) : self
    {
        $this->maxNumber = $maxNumber;

        return $this;
    }

    public function order(array $ids)
    {
        $this->validate();
    }

    private function validate()
    {
        if (! $this->entityType) {
            throw new LogicException("No entity type.");
        }

        if (! $this->group) {
            throw new LogicException("No group.");
        }

        if (! $this->userId) {
            throw new LogicException("No user ID.");
        }

        if (! $this->metadata->get(['scopes', $this->entityType, 'object'])) {
            throw new LogicException("Not allowed entity type.");
        }

        $statusField = $this->metadata->get(['scopes', $this->entityType, 'statusField']);

        if (! $statusField) {
            throw new LogicException("Not status field.");
        }

        $statusList = $this->metadata->get(['entityDefs', $this->entityType, 'fields', $statusField, 'options']) ?? [];

        if (! in_array($this->group, $statusList)) {
            throw new LogicException("Group is not available in status list.");
        }
    }
}

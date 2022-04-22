<?php

use Wikibase\Repo\ChangeOp;
use Wikibase\DataModel\Entity\EntityId;
class RecordChangeOP {

    private $record = [];

    public function recordApplied( ChangeOp $changeOp, EntityId $entityId ) {
        $this->record[$entityId->getSerialization()][] = $changeOp;
    }

    public function getForEntity( EntityId $entityID ) {
        return $record[$entityId];
    }

}

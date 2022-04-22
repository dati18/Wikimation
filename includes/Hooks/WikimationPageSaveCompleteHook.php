<?php

namespace MediaWiki\Extension\Wikimation\Hooks;

use WikiPage;
use MediaWiki\User\UserIdentity;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Storage\EditResult;

interface WikimationPageSaveCompleteHook {
	public function onWikimationPageSaveComplete(
		WikiPage $wikiPage,
		UserIdentity $user,
		string $summary,
		int $flags,
		RevisionRecord $revisionRecord,
		EditResult $editResult
	);
}

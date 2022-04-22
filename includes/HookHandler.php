<?php

namespace MediaWiki\Extension\Wikimation;
use Wikibase\Lib\Changes\ChangeRow;

class HookHandler {

	public static function onPageSaveComplete (
		$wikiPage,
		$user,
		$summary,
		$flags
	) {
		$hookSuffix = 'wikiPageSaved';
		$http_method = 'POST';
		self::sendWebhookToNode (
			[
				'title' => $wikiPage,
				'user' => $user,
				'summary' => $summary,
				// EDIT_… flags passed to WikiPage::doEditContent()
				'flags' => $flags,
				'flag-new' => $flags & EDIT_NEW, //1
				'flag-update' => $flags & EDIT_UPDATE, //2
				'flag-minor' => $flags & EDIT_MINOR, //4
				'flag-suppressrc' => $flags & EDIT_SUPPRESS_RC, //8
				'flag-forcebot' => $flags & EDIT_FORCE_BOT, //16
//				'flag-deferupdates' => $flags & EDIT_DEFER_UPDATES, //32
//				'flag-autosummary' => $flags & EDIT_AUTOSUMMARY, //64
//				'flag-internal' => $flags & EDIT_INTERNAL, //128
			],
			$hookSuffix,
			$http_method
		);
	}

	public static function onWikibaseChangeNotification (
		$change
	) {
		// 1 config option per hook that you emit an event / webhook for
		// So one called n8nWikibaseChangeNotificationHook for example
		$hookSuffix = "wikiItemCreated";
		$http_method = 'POST';
		$info = json_decode($change->getSerializedInfo(), true, 3);
		if ( isset( $info[ChangeRow::COMPACT_DIFF] ) ) {
			$info[ChangeRow::COMPACT_DIFF] = json_decode($info[ChangeRow::COMPACT_DIFF], true);
		}
		self::sendWebhookToNode (
			$info,
			$hookSuffix,
			$http_method
		);
	}

	public static function sendWebhookToNode (array $data, string $hookSuffix, string $http_method) {
		// TODO: Maybe defer this http call to after response to avoid slowing down responses to users
		// https://www.mediawiki.org/wiki/Manual:Job_queue#Deferred_updates
		// https://www.mediawiki.org/wiki/Manual:Job_queue/For_developers#Differences_with_DeferredUpdates
		// https://www.codegrepper.com/code-examples/php/php+send+webhook

		$curl = curl_init("https://nwm95ijyp0k2tdsqh0j97ts0.hooks.n8n.cloud/webhook-test/" . $hookSuffix);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_method);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_exec($curl);

//		$curl2 = curl_init("https://hungry-turkey-12.hooks.n8n.cloud/webhook-test/endpoint");
		//curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		//curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
//		curl_setopt($curl2, CURLOPT_CUSTOMREQUEST, $http_method);
//		curl_setopt($curl2, CURLOPT_POSTFIELDS, $data);
//		curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
//		curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
//		curl_exec($curl2);
	}

}

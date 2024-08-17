<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index() {
		return inertia('Settings/Index', [
			'settings' => Setting::get()->pluck('value', 'key')->toArray()
		]);
	}

	public function update(Request $request) {
		$request->validate([
			'ig_strategy' => 'required'
		]);

		if ($request->ig_strategy == 'account') {
			$request->validate([
				'ig_login' => 'required',
				'ig_pass' => 'required'
			]);

			settingSet('ig_strategy', 'account');
			settingSet('ig_login', $request->ig_login);
			settingSet('ig_pass', $request->ig_pass);
		} else if (in_array($request->ig_strategy, ['webscrapingapi', 'proxiesapi', 'scrapedo'])) {
			$request->validate([
				'scraper_keys' => 'required'
			]);

			settingSet('ig_strategy', $request->ig_strategy);
			settingSet('scraper_keys', json_encode($request->scraper_keys));
		} else {
			settingSet('ig_strategy', $request->ig_strategy);
		}

		return back()->with(['success' => true]);
	}
}

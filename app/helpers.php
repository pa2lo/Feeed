<?php

use \App\Models\Setting;

if (! function_exists('settingGet')) {
	function settingGet($key = null, $defaultValue = '') {
		if (!$key) return false;

		return Setting::where('key', $key)->first()?->value ?? $defaultValue;
	}
}

if (! function_exists('settingSet')) {
	function settingSet($key = null, $value = null) {
		if (!$key) return false;
		Setting::updateOrCreate([
			'key' => $key
		], [
			'value' => $value
		]);
		return $value;
	}
}

if (! function_exists('getScraperToken')) {
	function getScraperToken() {
		$workingToken = null;

		$keys = json_decode(settingGet('scraper_keys', "[]"), true);

		if ($keys && count($keys) > 0) {
			foreach ($keys as &$key) {
				if (!$key['count'] || $key['count'] > 10) {
					$workingToken = $key['key'];
					break;
				}
			}
		}

		return $workingToken;
	}
}

if (! function_exists('incrementScraperToken')) {
	function incrementScraperToken($id) {
		$setting = Setting::find('scraper_keys');
		$keys = json_decode($setting->value, true);
		foreach ($keys as &$key) {
			if ($key['key'] == $id) {
				if ($key['count']) $key['count'] -= 1;
				break;
			}
		}
		$setting->value = json_encode($keys);
		$setting->save();
		return true;
	}
}

if (! function_exists('getNetworkImage')) {
	function getNetworkImage($url, $dir, $fileName) {
		$path = public_path("$dir/$fileName");
		if (!file_exists($path)) {
			$fetchedFile = fetchNetworkFile($url, $dir, $path);
			if ($fetchedFile == false) return abort(422);
		}
		return response()->file($path);
	}
}

if (! function_exists('fetchNetworkFile')) {
	function fetchNetworkFile($url, $dir, $path, $checkFile = false) {
		if ($checkFile && file_exists($path)) return true;
		try {
			$file = file_get_contents($url);
			if ($file) {
				try {
					file_put_contents($path, $file);
				} catch (\Exception $e) {
					if (!is_dir(public_path("$dir"))) {
						mkdir(public_path("$dir"));
						file_put_contents($path, $file);
					} else return false;
				}
			} else return false;
		} catch (\Exception $e) {
			return false;
		}
		return true;
	}
}
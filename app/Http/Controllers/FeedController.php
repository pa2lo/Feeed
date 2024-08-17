<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use App\Models\Category;
use App\Models\Log;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Http\Request;
use voku\helper\HtmlDomParser;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class FeedController extends Controller
{
	/**
	 * Get logs
	 */
	public function logs() {
		$logs = Log::latest()->paginate(30);

		return inertia('Logs/Index', [
			'logs' => $logs
		]);
	}

	/**
     * Display a listing of the resource.
     */
    public function index() {
		return inertia('Feeds/Index', [
			'items' => Feed::with('categories:id')->withCount('posts')->get()->map(fn ($feed) => [
				'id' => $feed->id,
				'active' => $feed->active,
				'name' => $feed->name,
				'url' => $feed->url,
				'network' => $feed->network,
				'network_id' => $feed->network_id,
				'thumbnail' => $feed->thumbnail,
				'status' => $feed->status,
				'downloaded_at' => $feed->downloaded_at,
				'created_at' => $feed->created_at,
				'categories' => $feed->categories->pluck('id'),
				'posts_count' => $feed->posts_count
			]),
			'categories' => Category::get(['id', 'name'])
		]);
	}

	/**
     * Store a newly created resource in storage.
     */
	public function store(Request $request) {
		$request->validate([
			'name' => 'required|min:3',
			'network' => 'required',
			'url' => 'required|url'
		]);

		$newFeed = Feed::create([
			'active' => $request->active,
			'status' => 'new',
			'name' => $request->name,
			'network' => $request->network,
			'url' => $request->url
		]);

		$newFeed->categories()->sync($request->categories);

		return back()->with(['success' => true]);
	}

	/**
	 * Switch feed active
	 */
	public function switchState(Request $request, Feed $feed) {
		$feed->update([
			'active' => $request->active
		]);

		return response(['success' => true]);
	}
	public function switchStateMultiple(Request $request) {
		$request->validate([
			'feeds' => 'array',
			'active' => 'boolean'
		]);

		Feed::whereIn('id', $request->feeds)->update([
			'active' => $request->active
		]);

		return response(['success' => true]);
	}

	/**
     * Update the specified resource in storage.
     */
	public function update(Request $request, Feed $feed) {
		$request->validate([
			'name' => 'required|min:3',
			'network' => 'required',
			'url' => 'required'
		]);

		$feed->update([
			'active' => $request->active,
			'name' => $request->name,
			'network' => $request->network,
			'url' => $request->url
		]);

		$feed->categories()->sync($request->categories);

		return back()->with(['success' => true]);
	}

	/**
     * Remove the specified resource from storage.
     */
	public function destroy(Feed $feed) {
        $feed->delete();
		return back()->with(['success' => true]);
    }
	public function destroyMultiple(Request $request) {
		$request->validate([
			'feeds' => 'array'
		]);

		Feed::whereIn('id', $request->feeds)->delete();

		return response(['success' => true]);
	}

	/**
	 * scrape data
	 */
	public function processSingleFeed(Request $request, Feed $feed) {
		$dump = isset($request->dump) && $request->dump == 1 ? 1 : null;
		$igStrategy = $feed->network == 'instagram' ? settingGet('ig_strategy', 'default') : null;

		if ($feed->network == 'facebook') return response($this->fetchFBData($feed));
		else if ($feed->network == 'instagram' && $igStrategy == 'account') return response($this->fetchIGDataByLogin($feed));
		else if ($feed->network == 'instagram') return response($this->fetchIGDataBySrcaper($feed, $igStrategy, $dump));
		else return response([
			'success' => false
		]);
	}

	public function processAllFeeds() {
		if (request()->feeds) $feeds = Feed::whereIn('id', request()->feeds)->get();
		else $feeds = Feed::where('active', 1)->get();

		$action = request()->action ?? 'processAllFeeds';

		$successFeeds = [];
		$fetchErrors = [];
		$hasErrors = false;
		$newPosts = 0;
		$log = [];

		$igItems = count(array_filter($feeds->toArray(), fn($f) => $f['network'] == 'instagram'));
		$igStrategy = $igItems ? settingGet('ig_strategy', 'default') : null;

		if ($igStrategy == 'account') $client = (new PendingRequest)->buildClient();

		foreach ($feeds as $feed) {
			if ($feed->network == 'instagram' && in_array($igStrategy, ['default', 'account'])) sleep(rand(5, 16));

			$log[] = time() . " - start fetching {$feed->url}";

			if ($feed->network == 'facebook') $res = $this->fetchFBData($feed);
			else if ($feed->network == 'instagram' && $igStrategy == 'account') $res = $this->fetchIGDataByLogin($feed, $client);
			else if ($feed->network == 'instagram') $res = $this->fetchIGDataBySrcaper($feed, $igStrategy);

			if ($res['success']) {
				$text = $feed->id." - ".$feed->url;
				if ($res['newPosts'] ?? false) {
					$newPosts += $res['newPosts'];
					$text .= " + {$res['newPosts']} posts";
				}
				$successFeeds[] = $text;
				$logMessage = time() . " - fetching {$feed->url} success";
				if ($res['strategy'] ?? false) $logMessage .= " ({$res['strategy']})";
				$log[] = $logMessage;
			} else {
				$hasErrors = true;
				$fetchErrors[$feed->id] = [
					'url' => $feed->url,
					'error' => $res['error'] ?? ''
				];
				$logMessage = time() . " - fetching {$feed->url} error";
				if ($res['strategy'] ?? false) $logMessage .= " ({$res['strategy']})";
				$log[] = $logMessage;
			}
		}

		$log[] = time() . " - finished";
		$message = "Finished $action";
		if ($hasErrors && count($fetchErrors) > 0) $message = $message.' with '.count($fetchErrors).' errors';

		$log = Log::create([
			'service' => $action,
			'message' => $message,
			'has_errors' => $hasErrors,
			'data' => [
				'successFeeds' => $successFeeds,
				'newPosts' => $newPosts,
				'fetchErrors' => $fetchErrors,
				'log' => $log
			]
		]);

		return response($log);
	}

	// PROXY
	public function checkProxy($proxy) {
		try {
			$request = Http::withOptions([
				'version' => 2.0,
				'curl' => [	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_PRIOR_KNOWLEDGE	],
				'proxy' => $proxy
			// ])->get('https://api.ipify.org/');
			])->withHeaders([
				'Accept' => '*/*',
				'Accept-Language' => 'en-US,en;q=0.9,ru;q=0.8',
				'Accept-Encoding' => 'gzip, deflate, br',
				'Referer' => "https://www.instagram.com",
				'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:126.0) Gecko/20100101 Firefox/125.0',
				'x-ig-app-id' => '936619743392459',
			])->get("https://i.instagram.com/api/v1/users/web_profile_info/?username=nasa");

			if ($request->ok()) {
				$igJson = $request->json();
				$igUserData = $igJson['data']['user'] ?? null;
				if ($igUserData) return true;
				else return [
					'success' => false,
					'reason' => "No data->user in response"
				];
			} else return [
				'success' => false,
				'reason' => 'Response code ' . $request->code()
			];
		} catch (\Exception $e) {
			return [
				'success' => false,
				'reason' => $e->getMessage()
			];
		}
	}
	public function getProxy($attempt = 0) {
		$workingProxy = null;

		try {
			$proxy = Http::get('http://pubproxy.com/api/proxy?https=true&limit=5');
			if ($proxy->ok() && ($proxy->json()['count'] ?? false)) {
				$proxies = $proxy->json();
				$a = 0;
				while ($a < $proxies['count']) {
					$proxyTest = $this->checkProxy("http://{$proxies['data'][$a]['ipPort']}");
					if ($proxyTest['success'] == true) {
						$workingProxy = "http://{$proxies['data'][$a]['ipPort']}";
						break;
					}
					$a++;
				}
				if ($workingProxy) return [
					'success' => true,
					'proxy' => $workingProxy
				];
				else {
					if ($attempt == 5) return [
						'success' => false,
						'error' => 'No working proxy found'
					];
					else return $this->getProxy(++$attempt);
				}
			} else {
				if ($attempt == 5) return [
					'success' => false,
					'error' => 'No proxies in response'
				];
				else return $this->getProxy(++$attempt);
			}
		} catch (\Exception $e) {
			if ($attempt == 5) return [
				'success' => false,
				'error' => 'Max attempts reached'
			];
			else return $this->getProxy(++$attempt);
		}
	}
	public function test() {
		$proxy = request()->proxy ?? '1.2.3.4:5678';
		dd($this->checkProxy("http://$proxy"));
	}

	// Instagram
	// insta cookie jar
	private function getIGImageURI($url, $fetchImage = false) {
		if ($fetchImage) fetchNetworkFile($url, 'igimages', public_path('igimages/'.explode('?', basename($url))[0]), true);
		return '/igImage?src='.urlencode($url);
	}
	private function getIGCookies($client, $forceLogin = false) {
		if (cache()->has('igcookies') && !$forceLogin) {
			return [
				'success' => true,
				'cookiejar' => unserialize(cache()->get('igcookies')),
				'source' => 'cache'
			];
		} else {
			$cookieJar = new CookieJar();

			$request = Http::withOptions([
				'version' => 2.0,
				'curl' => [	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_PRIOR_KNOWLEDGE	],
				'cookies' => $cookieJar
			])->setClient($client)->get('https://www.instagram.com/accounts/login/');

			if (!$request->ok()) return [
				'success' => false,
				'error' => "IG request not OK - response " . $request->status()
			];

			$csrfToken = $cookieJar?->getCookieByName('csrftoken')?->getValue();

			if ($csrfToken) {
				$request2 = Http::withOptions([
					'version' => 2.0,
					'curl' => [	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_PRIOR_KNOWLEDGE	],
					'cookies' => $cookieJar
				])->setClient($client)->withHeaders([
					'Accept' => '*/*',
					'Accept-Language' => 'en-US,en;q=0.9,ru;q=0.8',
					'Accept-Encoding' => 'gzip, deflate, br',
					'Referer' => "https://www.instagram.com/accounts/login/",
					"X-Csrftoken" => $csrfToken,
					'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:126.0) Gecko/20100101 Firefox/125.0',
				])->asForm()->post('https://www.instagram.com/accounts/login/ajax/', [
					'enc_password' => '#PWD_INSTAGRAM_BROWSER:0:'.time().':'.settingGet('ig_pass'),
					'optIntoOneTap' => false,
					'queryParams' => "{}",
					'trustedDeviceRecords' => "{}",
					'username' => settingGet('ig_login')
				]);

				$resJson = $request2->json() ?? null;

				if ($request2->ok() && $resJson['user'] ?? false) {
					cache()->put('igcookies', serialize($cookieJar), 60*60*48);
					return [
						'success' => true,
						'cookiejar' => $cookieJar,
						'source' => 'login'
					];
				} else {
					return [
						'success' => false,
						'error' => "IG login response not OK"
					];
				};
			} else {
				return [
					'success' => false,
					'error' => "IG login unable to get CSRFToken"
				];
			};
		}
	}
	private function addIgLinks($text) {
		$hashtags = function($matches) {
			$hashtag = $matches[1];
			$tag = substr($hashtag, 1);
			return '<a href="https://www.instagram.com/explore/tags/' . $tag . '" target="_blank" rel="noopener noreferrer">' . $hashtag . '</a>';
		};

		$profiles = function($matches) {
			$profile = $matches[1];
			$tag = substr($profile, 1);
			return '<a href="https://www.instagram.com/' . $tag . '" target="_blank" rel="noopener noreferrer">' . $profile . '</a>';
		};

		$result = preg_replace_callback('/(#\S+)/u', $hashtags, $text);
		$result = preg_replace_callback('/(@\S+)/u', $profiles, $result);

		return $result;
	}
	private function getIGFetchLink($username, $strategy, $token = null) {
		$baseURL = "https://i.instagram.com/api/v1/users/web_profile_info/?username=$username";
		$encodedURL = urlencode($baseURL);
		if ($strategy == 'default' || !$token) return $baseURL;
		else if ($strategy == 'webscrapingapi') return "https://api.webscrapingapi.com/v2?api_key=$token&url=$encodedURL";
		else if ($strategy == 'proxiesapi') return "http://api.proxiesapi.com/?auth_key=$token&url=$encodedURL&use_headers=true";
		else if ($strategy == 'scrapedo') return "https://api.scrape.do?token=$token&url=$encodedURL&customHeaders=True";
	}
	public function fetchIGDataByLogin($feed, $client = null) {
		try {
			$processedPosts = 0;
			$newPosts = 0;

			$igUserName = str_replace('https://www.instagram.com/', '', $feed->url);

			if (!$client) $client = (new PendingRequest)->buildClient();

			$cookies = $this->getIGCookies($client);

			if (!$cookies['success']) return [
				'success' => false,
				'error' => "Unable to get cookies - {$cookies['error']}",
				'strategy' => 'account'
			];

			$cookieJar = $cookies['cookiejar'];

			$igOriginVisit = Http::withOptions([
				'version' => 2.0,
				'curl' => [	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_PRIOR_KNOWLEDGE	],
				'cookies' => $cookieJar
			])->setClient($client)->withHeaders([
				'Accept' => '*/*',
				'Accept-Language' => 'en-US,en;q=0.9,ru;q=0.8',
				'Accept-Encoding' => 'gzip, deflate, br',
				'Referer' => "https://www.instagram.com",
				'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:126.0) Gecko/20100101 Firefox/125.0',
				'x-ig-app-id' => '936619743392459',
			])->get($feed->url);

			if (!$igOriginVisit->ok()) return [
				'success' => false,
				'error' => 'igOriginVisit response '.$igOriginVisit->status(),
				'strategy' => 'account'
			];

			$igFetch = Http::withOptions([
				'version' => 2.0,
				'curl' => [	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_PRIOR_KNOWLEDGE	],
				'cookies' => $cookieJar
			])->setClient($client)->withHeaders([
				'Accept' => '*/*',
				'Accept-Language' => 'en-US,en;q=0.9,ru;q=0.8',
				'Accept-Encoding' => 'gzip, deflate, br',
				'Referer' => $feed->url,
				'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:126.0) Gecko/20100101 Firefox/125.0',
				'x-ig-app-id' => '936619743392459',
			])->get("https://www.instagram.com/api/v1/feed/user/$igUserName/username/?count=12");

			if (!$igFetch->ok()) return [
				'success' => false,
				'error' => 'response '.$igFetch->status(),
				'strategy' => 'account'
			];

			$igJson = $igFetch->json();
			$igUserData = $igJson['user'] ?? null;

			if (($igJson['status'] ?? 'ok') != 'ok' || !$igUserData) return [
				'success' => false,
				'error' => 'missing status or user in response',
				'strategy' => 'account'
			];

			$networkData = [
				'network_id' => $igUserData['pk_id'] ?? null,
				'thumbnail' => isset($igUserData['profile_pic_url']) ? $this->getIGImageURI($igUserData['profile_pic_url'], true) : null
			];

			if (count($igJson['items'] ?? []) == 0) return [
				'success' => false,
				'error' => 'missing items in response',
				'strategy' => 'account'
			];

			foreach ($igJson['items'] as $post) {
				$postType = ($post['video_duration'] ?? false == true) ? 'video' : 'image';

				$content = [
					'text' => isset($post['caption']['text']) ? $this->addIgLinks($post['caption']['text']) : '',
					'network_link' => "https://www.instagram.com/p/{$post['code']}",
					'shortcode' => $post['code'],
					'id' => $post['id'],
					'comments' => $post['comment_count'] ?? 0,
					'likes' => $post['like_count'] ?? 0
				];

				if ($content['likes'] > 1000) $content['likes'] = round($content['likes'] / 1000, 1) . "K";

				if (count($post['image_versions2']['candidates'] ?? []) > 0) {
					foreach ($post['image_versions2']['candidates'] as $imageNode) {
						if ($imageNode['width'] < 1000) {
							$content[$postType == 'image' ? 'image' : 'thumbnail'] = $this->getIGImageURI($imageNode['url'], true);
							break;
						}
					}
				} else $content[$postType == 'image' ? 'image' : 'thumbnail'] = null;

				if ($postType == 'video') $content['video'] = isset($post['video_versions'][0]['url']) ? $this->getIGImageURI($post['video_versions'][0]['url']) : null;
				if (isset($post['original_width']) && isset($post['original_height'])) $content['aspect-ratio'] = "{$post['original_width']}/{$post['original_height']}";

				$newPost = $feed->posts()->updateOrCreate([
					'network_id' => "instagram-{$postType}-{$post['pk']}"
				], [
					'type' => $postType,
					'time' => $post['taken_at'] ?? null,
					'content' => $content,
				]);

				$processedPosts++;
				if ($newPost->wasRecentlyCreated) $newPosts++;
			}

			$networkData['downloaded_at'] = time();

			$feed->update($networkData);

			return [
				'success' => true,
				'processedPosts' => $processedPosts,
				'newPosts' => $newPosts,
				'strategy' => 'account'
			];
		} catch (\Exception $e) {
			return [
				'success' => false,
				'error' => $e->getMessage(),
				'strategy' => 'account'
			];
		}
	}
	public function fetchIGDataBySrcaper($feed, $igStrategy, $dump = null) {
		try {
			$processedPosts = 0;
			$newPosts = 0;

			$igUserName = str_replace('https://www.instagram.com/', '', $feed->url);
			$igToken = in_array($igStrategy, ['webscrapingapi', 'proxiesapi', 'scrapedo']) ? getScraperToken() : null;
			if (!$igToken) $igStrategy = 'default';

			$headers = [
				'Accept' => '*/*',
				'Accept-Language' => 'en-US,en;q=0.9,ru;q=0.8',
				'Accept-Encoding' => 'gzip, deflate, br',
				'Referer' => $feed->url,
				'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:126.0) Gecko/20100101 Firefox/125.0',
				'x-ig-app-id' => '936619743392459'
			];

			if ($igStrategy == 'webscrapingapi') $headers = [
				'WSA-Accept' => '*/*',
				'WSA-Accept-Language' => 'en-US,en;q=0.9,ru;q=0.8',
				'WSA-Accept-Encoding' => 'gzip, deflate, br',
				'WSA-Referer' => $feed->url,
				'WSA-User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:126.0) Gecko/20100101 Firefox/125.0',
				'WSA-x-ig-app-id' => '936619743392459'
			];

			$igFetch = Http::withOptions([
				'version' => 2.0,
				'curl' => [	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_PRIOR_KNOWLEDGE	]
			])->withHeaders($headers)->get($this->getIGFetchLink($igUserName, $igStrategy, $igToken));

			if ($igToken) incrementScraperToken($igToken);

			if (!$igFetch->ok()) return [
				'success' => false,
				'error' => 'response '.$igFetch->status(),
				'strategy' => $igStrategy
			];

			$igJson = $igFetch->json();
			$igUserData = $igJson['data']['user'] ?? null;

			if (($igJson['status'] ?? 'ok') != 'ok' || !$igUserData) return [
				'success' => false,
				'error' => 'missing status or data>user in response',
				'strategy' => $igStrategy
			];

			if ($dump) dd($igJson);

			$networkData = [
				'network_id' => $igUserData['id'] ?? null,
				'thumbnail' => isset($igUserData['profile_pic_url']) ? $this->getIGImageURI($igUserData['profile_pic_url'], true) : null
			];

			if (count($igUserData['edge_owner_to_timeline_media']['edges'] ?? []) == 0) return [
				'success' => false,
				'error' => 'missing edge_owner_to_timeline_media in response',
				'strategy' => $igStrategy
			];

			foreach ($igUserData['edge_owner_to_timeline_media']['edges'] as $post) {
				$postType = ($post['node']['is_video'] ?? false == true) ? 'video' : 'image';

				$content = [
					'text' => isset($post['node']['edge_media_to_caption']['edges'][0]['node']['text']) ? $this->addIgLinks($post['node']['edge_media_to_caption']['edges'][0]['node']['text']) : '',
					'network_link' => "https://www.instagram.com/p/{$post['node']['shortcode']}",
					'shortcode' => $post['node']['shortcode'],
					'id' => $post['node']['id'],
					'comments' => $post['node']['edge_media_to_comment']['count'] ?? 0,
					'likes' => $post['node']['edge_liked_by']['count'] ?? 0
				];

				if ($content['likes'] > 1000) $content['likes'] = round($content['likes'] / 1000, 1) . "K";

				$content[$postType == 'image' ? 'image' : 'thumbnail'] = isset($post['node']['display_url']) ? $this->getIGImageURI($post['node']['display_url'], true) : null;
				if ($postType == 'video') $content['video'] = isset($post['node']['video_url']) ? $this->getIGImageURI($post['node']['video_url']) : null;
				if (isset($post['node']['dimensions']['width']) && isset($post['node']['dimensions']['height'])) $content['aspect-ratio'] = "{$post['node']['dimensions']['width']}/{$post['node']['dimensions']['height']}";

				$newPost = $feed->posts()->updateOrCreate([
					'network_id' => "instagram-{$postType}-{$post['node']['id']}"
				], [
					'type' => $postType,
					'time' => $post['node']['taken_at_timestamp'] ?? null,
					'content' => $content,
				]);

				$processedPosts++;
				if ($newPost->wasRecentlyCreated) $newPosts++;
			}

			$networkData['downloaded_at'] = time();

			$feed->update($networkData);

			return [
				'success' => true,
				'processedPosts' => $processedPosts,
				'newPosts' => $newPosts,
				'strategy' => $igStrategy
			];
		} catch (\Exception $e) {
			return [
				'success' => false,
				'error' => $e->getMessage(),
				'strategy' => $igStrategy
			];
		}
	}

	// Facebook
	private function getFBImageURI($url, $fetchImage = false) {
		$decodedURL = htmlspecialchars_decode($url);
		if (str_contains($url, '.fbcdn.net/')) {
			if ($fetchImage) fetchNetworkFile($decodedURL, 'fbimages', public_path('fbimages/'.explode('?', basename($decodedURL))[0]), true);
			return '/fbImage?src=' . urlencode(htmlspecialchars_decode($url));
		} return $decodedURL;
	}
	public function fetchFBData($feed) {
		try {
			$processedPosts = 0;
			$newPosts = 0;

			$encodedUrl = urlencode($feed->url);

			$dom = HtmlDomParser::file_get_html("https://www.facebook.com/plugins/page.php?app_id&container_width=500&height=600&hide_cover=true&href=$encodedUrl&locale=en_US&sdk=joey&show_facepile=false&show_posts=true&width=500&_fb_noscript=1");
			$networkLink = $dom->findOneOrFalse('._1dro a');
			if ($networkLink) $networkLinkImg = $networkLink->findOneOrFalse('img');
			if (!$networkLinkImg) $networkLinkImg = $dom->findOneOrFalse('._2lqh ._38vo img');
			$networkData = [
				'network_id' => $networkLink ? str_replace(["https://www.facebook.com/", "?ref=embed_page"], "", $networkLink->getAttribute('href')) : null
			];
			if ($networkLinkImg) $networkData['thumbnail'] = $this->getFBImageURI($networkLinkImg->getAttribute('src'), true);

			$posts = $dom->findMultiOrFalse('.userContentWrapper');
			if (!$posts) return "no posts";

			foreach ($posts as $post) {
				$postData = [
					'type' => 'text'
				];
				$content = [];

				$link = $post->findOneOrFalse('._6a._6b a._39g5');
				if ($link) {
					$postHref = str_replace("?ref=embed_page", "", $link->getAttribute('href'));
					$content['network_link'] = "https://www.facebook.com$postHref";
					if (str_contains($postHref, '/reel/')) {
						$postData['type'] = 'video';
						preg_match("/\/reel\/(\d+)\//", $postHref, $lmatches);
					} else if (str_contains($postHref, '/videos/')) {
						$postData['type'] = 'video';
						preg_match("/\/videos\/(\d+)\//", $postHref, $lmatches);
					} else preg_match("/\/posts\/(\d+)/", $postHref, $lmatches);
				}
				$postID = $link && $lmatches ? $lmatches[1] : null;
				$content['id'] = $postID;

				$abbr = $post->findOneOrFalse('abbr');
				$postData['time'] = $abbr ? $abbr->getAttribute('data-utime') : null;

				$userContent = $post->findOneOrFalse('.userContent');
				if ($userContent) {
					if ($userContent->findOneOrFalse('._4a6n[aria-hidden="true"]')) {
						$content['text'] = trim(str_replace(['href="/'], 'href="https://www.facebook.com/', strip_tags($userContent->findOneOrFalse('._4a6n[aria-hidden="true"]')->innerText(), '<br><a>')));
					} else {
						$content['text'] = trim(str_replace(['href="/'], 'href="https://www.facebook.com/', strip_tags($userContent->innerText(), '<br><a>')));
					}
				}

				if ($post->findOneOrFalse('.mtm')) {
					// external link
					$extLink = $post->findOneOrFalse('.mtm ._52c6[target="_blank"]');
					if ($extLink && $postData['type'] != 'video') {
						$postData['type'] = 'link';
						parse_str(parse_url($extLink->getAttribute('href'))['query'], $parsedLink);
						$linkHref = $parsedLink ? $parsedLink['u'] : $extLink->getAttribute('href');

						$content['link'] = $linkHref;
						if ($extLink->hasAttribute('aria-label')) $content['title'] = $extLink->getAttribute('aria-label');
						else if ($post->findOneOrFalse('.mtm ._3eqz ._6m3 ._6m6')) $content['title'] = $post->findOne('.mtm ._3eqz ._6m3 ._6m6')->text();

						if ($extLink->findOneOrFalse('.accessible_elem')) $content['description'] = $extLink->findOne('.accessible_elem')->text();
						if ($post->findOneOrFalse('.mtm ._6lz._6mb._1t62.ellipsis')) $content['web'] = $post->findOne('.mtm ._6lz._6mb._1t62.ellipsis')->text();
						else if ($post->findOneOrFalse('.mtm ._3eqz ._6m3 ._59tj')) $content['web'] = $post->findOne('.mtm ._3eqz ._6m3 ._59tj')->text();
						try {
							$content['meta'] = get_meta_tags($linkHref);
						} catch (\Exception $ex) {
							//throw $th;
						}
						if (!isset($content['meta']['twitter:image'])) {
							if ($post->findOneOrFalse('.fbStoryAttachmentImage img')) $content['meta']['twitter:image'] = $this->getFBImageURI($post->findOne('.fbStoryAttachmentImage img')->getAttribute('src'), true);
						}
					}
					// reshare
					else if ($post->findOneOrFalse('.mts')) {
						$postData['type'] = 'link';

						$shareLink = $post->findOneOrFalse('.mtm .fwb a[target="_blank"]');

						if ($shareLink) {
							$content['link'] = $shareLink->getAttribute('href');
							$content['web'] = $shareLink->text();
						} else {
							$newShareLink = $post->findOneOrFalse('.mtm ._2l7q a[target="_blank"]');
							if ($newShareLink) $content['link'] = $newShareLink->getAttribute('href');
							$newSharedWeb = $post->findOneOrFalse('.mtm .fwb span');
							if ($newSharedWeb) $content['web'] = $newSharedWeb->text();
						}

						$content['meta'] = [
							'description' => trim(strip_tags($post->findOne('.mts .mtm._5pcm [data-testid="post_message"]')))
						];
						if ($post->findOneOrFalse('.mtm ._2l7q ._1p6f.img')) $content['meta']['twitter:image'] = $this->getFBImageURI($post->findOne('.mtm ._2l7q ._1p6f.img')->getAttribute('src'), true);
					}
					// video
					else if ($postData['type'] == 'video') {
						$strippedStr = $dom->findOne('body')->text();
						$strippedStr = explode("}", explode("\"video_id\":\"$postID\",\"is_live_stream", $strippedStr)[1])[0];

						preg_match('/sd_src_no_ratelimit":"(.*?)"/', $strippedStr, $sdSrc);
						preg_match('/aspect_ratio":(.*?),/', $strippedStr, $aspectRatio);
						$content['video'] = $this->getFBImageURI(stripslashes($sdSrc[1]));
						$content['aspect-ratio'] = $aspectRatio[1];
						$content['thumbnail'] = $this->getFBImageURI($post->findOne('.mtm ._53j5 ._1p6f.img')->getAttribute('src'), true);
					}
					// image
					else if ($post->findOneOrFalse('.mtm ._2l7q ._1p6f.img')) {
						$postData['type'] = 'image';
						$content['image'] = $this->getFBImageURI($post->findOne('.mtm ._2l7q ._1p6f.img')->getAttribute('src'), true);
					}
					// gallery
					else if ($post->findMultiOrFalse('._xcx')) {
						$postData['type'] = 'gallery';
						$content['gallery'] = [];
						$galleryItems = $post->findMulti('._xcx');
						foreach ($galleryItems as $galleryItem) {
							if ($galleryItem->findOneOrFalse('.img')) {
								$img = $galleryItem->findOne('.img');
								$resArr = [
									"image" => $this->getFBImageURI($img->getAttribute('src'), true),
									"alt" => $img->getAttribute('alt')
								];
								if ($galleryItem->findOneOrFalse('._52da ._52db')) $resArr['hasMore'] = $galleryItem->findOne('._52da ._52db')->text();
								$content['gallery'][] = $resArr;
							}
						}
					}
				}

				$content['likes'] = $post->findOneOrFalse('._2pi4._36iq._4lk2[title="Like"]') ? $post->findOne('._2pi4._36iq._4lk2[title="Like"]')->text() : 0;
				$content['comments'] = $post->findOneOrFalse('._2pi4._36iq._4lk2[title="Comment"]') ? $post->findOne('._2pi4._36iq._4lk2[title="Comment"]')->text() : 0;

				$postData['network_id'] = "facebook-{$postData['type']}-$postID";

				$shouldSkipSave = false;
				if ($postData['type'] == 'text' && !$content['text']) $shouldSkipSave = true;

				if (!$shouldSkipSave) {
					$newPost = $feed->posts()->updateOrCreate([
						'network_id' => $postData['network_id']
					], [
						'type' => $postData['type'],
						'time' => $postData['time'],
						'content' => $content,
					]);

					$processedPosts++;
					if ($newPost->wasRecentlyCreated) $newPosts++;
				}
			}

			$networkData['downloaded_at'] = time();

			$feed->update($networkData);

			return [
				'success' => true,
				'processedPosts' => $processedPosts,
				'newPosts' => $newPosts
			];
		} catch (\Exception $e) {
			return [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}
}
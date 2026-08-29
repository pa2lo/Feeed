<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use App\Models\Post;
use App\Models\Category;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
// use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    // index
	public function index(Request $request) {
		$posts = Post::filter($request->only('active', 'feeds', 'categories', 'type'))->orderBy('time', 'desc')->paginate(20)->withQueryString();

		return inertia('Posts/Index', [
			'filters' => $request->only('active', 'feeds', 'categories', 'type'),
			'posts' => $posts,
			'feeds' => Feed::get(['id', 'name', 'url']),
			'categories' => Category::get(['id', 'name'])
		]);
	}

	public function destroy(Post $post) {
        $post->delete();
		return back()->with(['success' => true]);
    }

	public function getInstagramImage(Request $request) {
		if (!$request->has('src')) return abort(404);

		$url = $request->src;
		$fileName = explode('?', basename($request->src))[0];

		return getNetworkImage($url, 'igimages', $fileName);
	}

	public function getFacebookImage(Request $request) {
		if (!$request->has('src')) return abort(404);

		$url = $request->src;
		$fileName = explode('?', basename($request->src))[0];

		return getNetworkImage($url, 'fbimages', $fileName);
	}

	// frontend
	public function getPosts(Request $request) {
		$posts = Post::whereRelation('feed', 'active', 1)->filter($request->only('feeds', 'categories'))->where('time', '<', $request->time)->orderBy('time', 'desc')->simplePaginate(20, ['feed_id', 'network_id', 'type', 'content', 'time']);

		return response([
			'success' => true,
			'posts' => $posts->items(),
			'nextPage' => $posts->nextPageUrl() ? $posts->currentPage()+1 : false
		]);
	}
	public function getFeeds() {
		return response([
			'success' => true,
			'feeds' => Feed::where('active', 1)->get(['id', 'name', 'url', 'network', 'thumbnail'])
		]);
	}
	public function getCategories() {
		return response([
			'success' => true,
			'categories' => Category::with('feeds:id')->get(['id', 'name'])->map(fn ($category) => [
				'id' => $category->id,
				'name' => $category->name,
				'feeds' => $category->feeds->pluck('id')
			])
		]);
	}
	public function getLastUpdate() {
		$last = Log::latest()->first(['created_at', 'data']);
		return response([
			'date' => $last->created_at,
			'posts' => $last->data['newPosts'] ?? 0
		]);
	}

	public function getStatus() {
		$last = Log::where('service', 'processAllFeeds')->latest()->first(['created_at', 'data', 'has_errors']);

		$info = $last->created_at->setTimezone('Europe/Bratislava')->format('H:i');
		if ($last->data['newPosts'])  $info .= " +{$last->data['newPosts']}";

		return response([
			'has_errors' => $last->has_errors,
			'info' => $info
		]);
	}

	public function clearOldPosts() {
		$months = 4;

		$limitTime = time() - ($months * 31 * 24 * 60 * 60);

		$deletedFiles = 0;

		foreach (['igimages', 'fbimages'] as $folder) {
			$path = public_path($folder);
			foreach (glob("$path/*") as $k => $file) {
				if (!is_file($file)) continue;

				$fileTime = filemtime($file);

				if ($fileTime < $limitTime) {
					unlink($file);
					$deletedFiles++;
				}
			}
		}

		$deleteBefore = Carbon::now()->subMonths($months);
		$deleted = Post::where('created_at', '<=', $deleteBefore)->delete();

		return "files $deletedFiles - posts $deleted";
	}
}
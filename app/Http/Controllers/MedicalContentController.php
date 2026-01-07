<?php

namespace App\Http\Controllers;

use App\Models\MedicalContent;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalContentController extends Controller
{
    /**
     * Display medical content index page
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'all'); // all, knowledge_base, faq, template
        $category = $request->get('category');
        $tag = $request->get('tag');
        $search = $request->get('search');

        // Base query - only published content
        $query = MedicalContent::where('status', 'published');

        // Filter by type
        if ($type !== 'all') {
            $query->where('content_type', $type);
        }

        // Filter by category
        if ($category) {
            $query->where('category', $category);
        }

        // Filter by tag
        if ($tag) {
            // Sử dụng whereRaw với JSON_SEARCH để tìm tag trong JSON array
            // JSON_SEARCH tìm kiếm giá trị trong JSON và trả về path nếu tìm thấy
            // Decode URL để lấy tag đúng
            $decodedTag = urldecode($tag);
            $query->whereRaw('JSON_SEARCH(tags, "one", ?) IS NOT NULL', [$decodedTag]);
        }

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Get content
        $knowledgeBase = MedicalContent::where('content_type', 'knowledge_base')
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        $faqs = MedicalContent::where('content_type', 'faq')
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        $templates = MedicalContent::where('content_type', 'template')
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Get filtered results
        $results = $query->orderBy('created_at', 'desc')->paginate(12);

        // Get categories for filter
        $categories = MedicalContent::where('status', 'published')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        // Stats
        $stats = [
            'knowledge_base_count' => MedicalContent::where('content_type', 'knowledge_base')
                ->where('status', 'published')
                ->count(),
            'faqs_count' => MedicalContent::where('content_type', 'faq')
                ->where('status', 'published')
                ->count(),
            'templates_count' => MedicalContent::where('content_type', 'template')
                ->where('status', 'published')
                ->count(),
        ];

        // Get user's bookmarks if logged in
        $bookmarks = collect();
        $bookmarksCount = 0;
        if (Auth::check()) {
            $bookmarks = Auth::user()->bookmarks()
                ->where('status', 'published')
                ->orderBy('bookmarks.created_at', 'desc')
                ->limit(6)
                ->get();
            $bookmarksCount = Auth::user()->bookmarks()->where('status', 'published')->count();
        }

        return view('medical-content.index', compact(
            'knowledgeBase',
            'faqs',
            'templates',
            'results',
            'stats',
            'type',
            'category',
            'tag',
            'search',
            'categories',
            'bookmarks',
            'bookmarksCount'
        ));
    }

    /**
     * Show single knowledge base article
     */
    public function showKnowledgeBase($id)
    {
        $article = MedicalContent::where('content_type', 'knowledge_base')
            ->where('status', 'published')
            ->findOrFail($id);

        // Increment views
        $article->increment('views_count');

        // Check if user has bookmarked this article
        $isBookmarked = false;
        if (Auth::check()) {
            $isBookmarked = $article->isBookmarkedBy(Auth::id());
        }

        // Get related articles
        $related = MedicalContent::where('content_type', 'knowledge_base')
            ->where('status', 'published')
            ->where('id', '!=', $id)
            ->where(function($q) use ($article) {
                if ($article->category) {
                    $q->where('category', $article->category);
                }
            })
            ->limit(4)
            ->get();

        return view('medical-content.knowledge-base.show', compact('article', 'related', 'isBookmarked'));
    }

    /**
     * Show single FAQ
     */
    public function showFAQ($id)
    {
        $faq = MedicalContent::where('content_type', 'faq')
            ->where('status', 'published')
            ->findOrFail($id);

        // Check if user has bookmarked this FAQ
        $isBookmarked = false;
        if (Auth::check()) {
            $isBookmarked = $faq->isBookmarkedBy(Auth::id());
        }

        // Get related FAQs
        $related = MedicalContent::where('content_type', 'faq')
            ->where('status', 'published')
            ->where('id', '!=', $id)
            ->where(function($q) use ($faq) {
                if ($faq->category) {
                    $q->where('category', $faq->category);
                }
            })
            ->limit(4)
            ->get();

        return view('medical-content.faq.show', compact('faq', 'related', 'isBookmarked'));
    }

    /**
     * Show all knowledge base articles
     */
    public function knowledgeBase(Request $request)
    {
        $category = $request->get('category');
        $tag = $request->get('tag');
        $search = $request->get('search');
        $sort = $request->get('sort', 'newest'); // newest, views, helpful, title

        $query = MedicalContent::where('content_type', 'knowledge_base')
            ->where('status', 'published');

        if ($category) {
            $query->where('category', $category);
        }

        if ($tag) {
            // Decode URL để lấy tag đúng (tag có thể bị encode trong URL)
            $decodedTag = urldecode($tag);
            // Sử dụng whereRaw với JSON_SEARCH để tìm tag trong JSON array
            $query->whereRaw('JSON_SEARCH(tags, "one", ?) IS NOT NULL', [$decodedTag]);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Sort
        switch ($sort) {
            case 'views':
                $query->orderBy('views_count', 'desc');
                break;
            case 'helpful':
                $query->orderBy('helpful_count', 'desc');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $articles = $query->paginate(12);

        $categories = MedicalContent::where('content_type', 'knowledge_base')
            ->where('status', 'published')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        return view('medical-content.knowledge-base.index', compact('articles', 'categories', 'category', 'tag', 'search', 'sort'));
    }

    /**
     * Show all FAQs
     */
    public function faqs(Request $request)
    {
        $category = $request->get('category');
        $tag = $request->get('tag');
        $search = $request->get('search');
        $sort = $request->get('sort', 'newest'); // newest, views, helpful, title

        $query = MedicalContent::where('content_type', 'faq')
            ->where('status', 'published');

        if ($category) {
            $query->where('category', $category);
        }

        if ($tag) {
            // Decode URL để lấy tag đúng
            $decodedTag = urldecode($tag);
            // Sử dụng whereRaw với JSON_SEARCH để tìm tag trong JSON array
            $query->whereRaw('JSON_SEARCH(tags, "one", ?) IS NOT NULL', [$decodedTag]);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Sort
        switch ($sort) {
            case 'views':
                $query->orderBy('views_count', 'desc');
                break;
            case 'helpful':
                $query->orderBy('helpful_count', 'desc');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $faqs = $query->paginate(12);

        $categories = MedicalContent::where('content_type', 'faq')
            ->where('status', 'published')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->values();

        return view('medical-content.faq.index', compact('faqs', 'categories', 'category', 'tag', 'search', 'sort'));
    }

    /**
     * Mark content as helpful
     */
    public function markHelpful($id)
    {
        $content = MedicalContent::where('status', 'published')->findOrFail($id);
        $content->increment('helpful_count');

        return response()->json([
            'success' => true,
            'helpful_count' => $content->helpful_count,
        ]);
    }

    /**
     * Toggle bookmark for a content
     */
    public function toggleBookmark($id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để đánh dấu bài viết.',
            ], 401);
        }

        $content = MedicalContent::where('status', 'published')->findOrFail($id);
        $userId = Auth::id();

        $bookmark = Bookmark::where('user_id', $userId)
            ->where('medical_content_id', $id)
            ->first();

        if ($bookmark) {
            // Remove bookmark
            $bookmark->delete();
            $isBookmarked = false;
            $message = 'Đã bỏ đánh dấu bài viết.';
        } else {
            // Add bookmark
            Bookmark::create([
                'user_id' => $userId,
                'medical_content_id' => $id,
            ]);
            $isBookmarked = true;
            $message = 'Đã đánh dấu bài viết.';
        }

        return response()->json([
            'success' => true,
            'is_bookmarked' => $isBookmarked,
            'message' => $message,
        ]);
    }

    /**
     * Get user's bookmarks
     */
    public function myBookmarks(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $type = $request->get('type', 'all');
        $category = $request->get('category');
        $tag = $request->get('tag');
        $search = $request->get('search');

        $query = Auth::user()->bookmarks()
            ->where('status', 'published');

        if ($type !== 'all') {
            $query->where('content_type', $type);
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($tag) {
            // Decode URL để lấy tag đúng
            $decodedTag = urldecode($tag);
            // Sử dụng whereRaw với JSON_SEARCH để tìm tag trong JSON array
            $query->whereRaw('JSON_SEARCH(medical_content.tags, "one", ?) IS NOT NULL', [$decodedTag]);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $bookmarks = $query->orderBy('bookmarks.created_at', 'desc')->paginate(12);

        $categories = Auth::user()->bookmarks()
            ->where('status', 'published')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        return view('medical-content.bookmarks', compact('bookmarks', 'categories', 'type', 'category', 'tag', 'search'));
    }
}


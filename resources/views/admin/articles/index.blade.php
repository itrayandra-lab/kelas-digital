@extends('layouts.admin')

@section('title', 'Manage Articles')

@section('content')

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div class="flex-1">
        </div>
        <a href="{{ route('admin.articles.create') }}"
            class="inline-flex items-center justify-center px-5 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors duration-300">
            <i class="fas fa-plus mr-2 -ml-1 text-base"></i>
            Add New Article
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-sm text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Title</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Author</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Category</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Views</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Published/Scheduled
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($articles as $article)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                @if($article->isPublished())
                                    <a href="{{ route('article.show', $article->slug) }}" class="text-sm font-medium text-gray-900 hover:text-primary-600 transition-colors">{{ $article->title }}</a>
                                @else
                                    <span class="text-sm font-medium text-gray-900">{{ $article->title }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ $article->author }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">
                                    {{ $article->categories->isNotEmpty() ? $article->categories->pluck('name')->join(', ') : 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($article->isPublished())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Published
                                    </span>
                                @elseif($article->isScheduled())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Scheduled
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm text-gray-700">{{ number_format($article->views_count) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">
                                    @if($article->isPublished())
                                        {{ $article->published_at ? $article->published_at->format('d M Y H:i') : $article->created_at->format('d M Y H:i') }}
                                    @elseif($article->isScheduled())
                                        {{ $article->scheduled_at ? $article->scheduled_at->format('d M Y H:i') : 'N/A' }}
                                    @else
                                        Not published
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('admin.articles.show', $article->id) }}"
                                    class="text-gray-500 hover:text-gray-800">View</a>
                                <a href="{{ route('admin.articles.edit', $article->id) }}"
                                    class="text-primary-600 hover:text-primary-800">Edit</a>
                                
                                @if($article->isScheduled())
                                    <form action="{{ route('admin.articles.publish', $article->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800" 
                                                onclick="return confirm('Publish this article immediately?')">Publish</button>
                                    </form>
                                    <form action="{{ route('admin.articles.unschedule', $article->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-800" 
                                                onclick="return confirm('Unschedule this article?')">Unschedule</button>
                                    </form>
                                @endif
                                
                                <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST"
                                    class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this article?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                No articles found. Click "Add New Article" to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $articles->links() }}
    </div>

@endsection

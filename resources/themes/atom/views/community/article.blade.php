<x-app-layout>
    @push('title', $article->title)

    <div class="col-span-12 md:col-span-3 rounded space-y-3">
        <div class="rounded h-24 bg-white border w-full overflow-hidden relative mt-6 md:mt-0 shadow dark:bg-gray-800 dark:border-gray-900">
            <div class="absolute right-1 top-1 bg-white rounded px-2 text-sm font-semibold dark:bg-gray-700 dark:text-gray-100">
                {{ !$article->user->hidden_staff ? $article->user->permission->rank_name : 'Member' }}
            </div>

            <div class="h-[65%] w-full staff-bg" style="background: rgba(0, 0, 0, 0.5) url({{ asset(sprintf('assets/images/%s', $article->user->permission->staff_background)) }});"></div>

            <a href="{{ route('profile.show', $article->user->username) }}" class="absolute top-4 drop-shadow left-1 ">
                <img style="image-rendering: pixelated;" class="transition ease-in-out duration-300 hover:scale-105" src="{{ setting('avatar_imager') }}{{ $article->user->look }}&direction=2&head_direction=3&gesture=sml&action=wav" alt="">
            </a>

            <p class="text-2xl font-semibold ml-[70px] text-white -mt-[35px]">
                {{ $article->user->username }}
            </p>

            <div class="w-full flex justify-between px-4 items-center">
                <p class="ml-[57px] text-sm mt-[10px] font-semibold text-gray-500">
                    {{ $article->user->motto }}
                </p>

                <div class="w-4 h-4 rounded-full mt-2 {{ $article->user->online ? 'bg-green-600' : 'bg-red-600' }}"></div>
            </div>
        </div>

        <x-content.content-section icon="hotel-icon" classes="border dark:bg-gray-800 dark:border-gray-900">
            <x-slot:title>
                {{ __('Other articles')  }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('Our most recent articles') }}
            </x-slot:under-title>

            <div class="flex flex-col gap-y-2">
                @forelse($otherArticles as $art)
                    <a href="{{ route('article.show', $art->slug) }}"
                       style="background: rgba(0, 0, 0, 0.5) url({{ $art->image }}) center;"
                       class="w-full rounded h-12 bg-blue-200 transition ease-in-out duration-200 hover:scale-[103%] text-white flex justify-center items-center font-bold recent-articles">
                        {{ Str::limit($art->title, 20) }}
                    </a>
                @empty
                    <p class="dark:text-gray-400">
                        {{ __('There is currently no other articles') }}
                    </p>
                @endforelse
            </div>
        </x-content.content-section>
    </div>

    <div class="col-span-12 md:col-span-9 space-y-4">
        <div class="rounded bg-white shadow p-3 flex flex-col gap-y-8 relative overflow-hidden dark:bg-gray-800 dark:text-gray-300">
            <div class="relative rounded h-24 flex items-center justify-center overflow-hidden flex-col gap-y-1 text-white px-2" style="background: url({{ $article->image }}) center; background-size: cover;">
                <div class="bg-black bg-opacity-50 w-full h-full absolute"></div>

                <p class="font-semibold text-center text-xl lg:text-2xl xl:text-3xl relative truncate w-full">{{ $article->title }}</p>
                <p class="relative truncate w-full text-center">{{ $article->short_story }}</p>
            </div>

            <div class="px-2">
                {!! $article->full_story  !!}
            </div>

            @include('community.partials.article-reactions')
        </div>


        @if(auth()->check() && !$article->userHasReachedArticleCommentLimit())
            <x-content.content-section icon="hotel-icon" classes="border dark:border-gray-900">
                <x-slot:title>
                    {{ __('Post a comment') }}
                </x-slot:title>

                <x-slot:under-title>
                    {{ __('Post a comment on the article, to let us know what you think about it') }}
                </x-slot:under-title>

                <div class="px-2 text-sm dark:text-gray-200">
                    <form action="{{ route('article.comment.store', $article) }}" method="POST">
                        @csrf

                        <textarea name="comment" class="focus:ring-0 border-2 border-gray-200 rounded dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 focus:border-[#eeb425] w-full min-h-[100px] max-h-[100px] @error('comment') border-red-600 ring-red-500 @enderror"></textarea>

                        <x-form.primary-button>
                            {{ __('Post comment') }}
                        </x-form.primary-button>
                    </form>
                </div>
            </x-content.content-section>
        @endif

        <x-content.content-section icon="hotel-icon" classes="border dark:border-gray-900">
            <x-slot:title>
                {{ __('Comments') }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('Below you will see all the comments, written on this article') }}
            </x-slot:under-title>

            <div class="px-1 dark:text-gray-200 space-y-[13px]">
                @foreach($article->comments->sortByDesc('created_at') as $comment)
                    <div class="relative w-full rounded bg-[#f5f5f5] dark:bg-gray-700 p-4 h-[90px] overflow-hidden flex items-center shadow">
                        <a href="{{ route('profile.show', $comment->user) }}" class="absolute top-2 drop-shadow left-1 ">
                            <img style="image-rendering: pixelated;" class="transition ease-in-out duration-300 hover:scale-105" src="{{ setting('avatar_imager') }}{{ $comment->user->look }}&direction=2&head_direction=3&gesture=sml&action=wav" alt="">
                        </a>

                        <div class="flex justify-between ml-[60px] w-full">
                            <div class="text-sm">
                                <a href="{{ route('profile.show', $comment->user) }}" class="font-semibold text-[#89cdf0] dark:text-blue-300 hover:underline">
                                    {{ $comment->user->username }}
                                </a>

                                <p class="block dark:text-gray-200">
                                    {{ $comment->comment }}
                                </p>
                            </div>

                            <div class="flex gap-x-2">
                                <p class="dark:text-gray-200 text-xs">
                                    {{ $comment->created_at->diffForHumans() }}
                                </p>

                                @if($comment->userCanDeleteComment())
                                    <form action="{{ route('article.comment.destroy', $comment) }}" method="POST" class="hover:scale-105 transition ease-in-out duration-200 cursor-pointer">
                                        @method('DELETE')
                                        @csrf

                                        <button type="submit">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-content.content-section>
    </div>
</x-app-layout>

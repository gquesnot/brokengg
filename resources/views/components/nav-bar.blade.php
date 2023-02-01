<div class="justify-center  py-4 bg-gray-100">
    <div class="flex justify-between w-full">
        <div>
            <a href="{{ route('home') }}" class="text-gray-900 text-xl font-bold tracking-tight">
                {{ config('app.name') }}
            </a>
        </div>
{{--        <div class="">--}}

{{--            @if (Route::has('login'))--}}
{{--                <div class="space-x-4">--}}
{{--                    @auth--}}
{{--                        <a--}}
{{--                            href="{{ route('logout') }}"--}}
{{--                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"--}}
{{--                            class="font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150"--}}
{{--                        >--}}
{{--                            Log out--}}
{{--                        </a>--}}

{{--                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">--}}
{{--                            @csrf--}}
{{--                        </form>--}}
{{--                    @else--}}
{{--                        <a href="{{ route('login') }}"--}}
{{--                           class="font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150">Log--}}
{{--                            in</a>--}}

{{--                        @if (Route::has('register'))--}}
{{--                            <a href="{{ route('register') }}"--}}
{{--                               class="font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150">Register</a>--}}
{{--                        @endif--}}
{{--                    @endauth--}}
{{--                </div>--}}
{{--            @endif--}}
{{--        </div>--}}

    </div>

</div>

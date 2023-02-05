<div class="mb-10 w-10/12 mx-auto">
    <x-nav-bar></x-nav-bar>
    <div class="">
        <div class="flex flex-col">
            <div class="flex justify-between">
                <div>
                    <div class="flex items-center mb-6 my-2">
                        <div class="flex flex-col  mr-4 justify-center">
                            <div class="flex justify-center">
                                <img
                                    src="https://ddragon.leagueoflegends.com/cdn/{{$version}}/img/profileicon/{{$summoner->profile_icon_id}}.png"

                                    alt="profile icon" class="w-16 h-16 rounded-full">
                            </div>

                            <span class="text-center">Lvl {{$summoner->summoner_level}}</span>
                            @if($summoner->best_league)
                                <div class="flex flex-col items-center justify-center">
{{--                                    <img--}}
{{--                                        src="{{$summoner->best_league->tier->url()}}"--}}
{{--                                        alt="tier icon" class="w-32 h-32"/>--}}
                                    <div>{{$summoner->best_league->tier->name}} {{$summoner->best_league->rank}}</div>
                                </div>


                            @endif
                        </div>
                        <h3 class="text-3xl my-2">{{$summoner->name}}</h3>
                        <div class="flex flex-col mx-4">
                            <div class="flex">
                                <button wire:click="updateSummoner"
                                    @class(["my-4  w-fit inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"])>
                                    update
                                </button>
                                <button wire:click="updateSummoner(true)"
                                    @class(["my-4 ml-4 w-fit inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"])>
                                    full update
                                </button>
                            </div>

                            <div class="flex ">
                                <div class="mr-2">Auto Update</div>
                                <button type="button" wire:click="toggleAutoUpdate"
                                        @class(["relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500", "bg-indigo-600" => $summoner->auto_update, "bg-gray-200" => !$summoner->auto_update])
                                        role="switch" aria-checked="false">
                                    <span class="sr-only">auto update</span>
                                    <!-- Enabled: "translate-x-5", Not Enabled: "translate-x-0" -->
                                    <span
                                @class(["pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200", "translate-x-5"=> $summoner->auto_update, "translate-x-0"=> !$summoner->auto_update])>
    <!-- Enabled: "opacity-0 ease-out duration-100", Not Enabled: "opacity-100 ease-in duration-200" -->
    <span
        @class(["absolute inset-0 h-full w-full flex items-center justify-center transition-opacity", "opacity-0 ease-out duration-100"=> $summoner->auto_update, "opacity-100 ease-in duration-200"=> !$summoner->auto_update])
        aria-hidden="true">
      <svg class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 12 12">
        <path d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"
              stroke-linejoin="round"/>
      </svg>
    </span>
                                        <!-- Enabled: "opacity-100 ease-in duration-200", Not Enabled: "opacity-0 ease-out duration-100" -->
    <span
        @class(["absolute inset-0 h-full w-full flex items-center justify-center transition-opacity", "opacity-100 ease-in duration-200"=> $summoner->auto_update, "opacity-0 ease-out duration-100"=> !$summoner->auto_update])
        aria-hidden="true">
      <svg class="h-3 w-3 text-indigo-600" fill="currentColor" viewBox="0 0 12 12">
        <path
            d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z"/>
      </svg>
    </span>
  </span>
                                </button>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="w-7/12">
                    <livewire:filter :summoner="$summoner" :filters="$filters"/>
                </div>
            </div>

        </div>
        <div>
            <div class="mb-4">
                <div class="hidden sm:block">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <!-- Current: "border-indigo-500 text-indigo-600", Default: "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" -->
                            @foreach(\App\Enums\TabEnum::cases() as $tabEnum)
                                @if($tabEnum->only_summoner_id())
                                    <a href="{{route($tabEnum->value, ['summonerId' => $summonerId]).$this->getParamsUrl()}}">
                                        @endif
                                        <div
                                            @class(["border-transparent  whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" => $tab != $tabEnum, "border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" => $tab == $tabEnum, "text-gray-400" => !$tabEnum->only_summoner_id(), " cursor-pointer text-gray-600 hover:text-gray-800 hover:border-gray-300" => $tabEnum->only_summoner_id()])>
                                            {{$tabEnum->title()}}
                                        </div>
                                        @if($tabEnum->only_summoner_id())
                                    </a>
                                @endif

                            @endforeach

                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative">
            <div wire:loading>
                <x-defer-loading/>
            </div>

            <div>
                @if($tab == \App\Enums\TabEnum::MATCHES)
                    <livewire:matches :me="$summoner" :version="$version"
                                      wire:key="matches_{{$filters->toJson()}}"
                                      :filters="$filters"/>
                @elseif($tab ==  \App\Enums\TabEnum::ENCOUNTERS)
                    <livewire:encounters :me="$summoner" :version="$version"
                                         wire:key="encounters_{{$filters->toJson()}}"
                                         :filters="$filters"/>
                @elseif($tab ==  \App\Enums\TabEnum::LIVE_GAME)
                    <livewire:live-game :version="$version" :me="$summoner"
                                        wire:key="live_game_{{$filters->toJson()}}"/>
                @elseif($tab ==  \App\Enums\TabEnum::VERSUS)
                    <livewire:summoner-versus :me="$summoner" :other="$otherSummonerId"
                                              :version="$version"
                                              wire:key="summoner_versus_{{$filters->toJson()}}"
                                              :filters="$filters"/>
                @elseif($tab == \App\Enums\TabEnum::CHAMPIONS)
                    <livewire:champions :me="$summoner" :version="$version"
                                        wire:key="champions_{{$filters->toJson()}}"
                                        :filters="$filters"/>
                @elseif($tab == \App\Enums\TabEnum::MATCH_DETAIL)
                    <livewire:match-detail :me="$summoner" :version="$version" :matchId="$matchId"
                                           wire:key="match_detail_{{$filters->toJson()}}"/>
                @endif
            </div>

        </div>
        <x-all-flash/>
    </div>

</div>

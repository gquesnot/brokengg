<div class="flex flex-col" wire:poll.visible.5s>

	@if($loaded)
		<div class="flex">
			<div class="mx-2">{{str_replace("games","",$info['queue']['description'])}}</div>
			<div class="mx-2">{{$info['map']['name']}}</div>
			<div class="mx-2">{{$info['duration']}}</div>
		</div>
		<div class="flex">
			<div class="w-1/2 mr-4">
				@foreach($participants as $participant)

					@if (!$participant['vs'])
						<x-live-game-summoner :participant="$participant" :version="$version" :me="$me"/>
					@endif
				@endforeach
			</div>
			<div class="w-1/2">
				@foreach($participants as $participant)

					@if ($participant['vs'])
						<x-live-game-summoner :participant="$participant" :version="$version" :me="$me"/>
					@endif
				@endforeach
			</div>
		</div>

	@else
		@if(!$lobbyLoaded)
			<div class="flex flex-col">
				No Active Game
				<div class="py-2">
					Search By Text
				</div>
				<div>
                    <textarea rows="5" class="w-full my-2" wire:model.defer="search" placeholder="player1 joined the lobby
player2 joined the lobby
player3 joined the lobby
player4 joined the lobby
player5 joined the lobby"
                    ></textarea>
					<button
						class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring-indigo active:bg-indigo-700 transition duration-150 ease-in-out"
						wire:click="searchSummoners">Search
					</button>
				</div>

			</div>
		@else
			<div class="flex flex-col">
				@foreach($lobbyParticipants as $participant)
					<div class=" border shadow bg-blue-200 flex p-2 my-1 w-full">
						<div class="font-medium text-xl mx-2">
							<a href="{{route('summoner', ['summonerId'=> $participant['id']])}}">{{$participant['name']}}</a>
						</div>
						<div>

							<a href="{{route('versus', ['summonerId' => $me['id'], 'otherSummonerId' => $participant['id']])}}">
								({{$participant['total']}})
							</a>
						</div>

					</div>
				@endforeach
			</div>
		@endif

	@endif

</div>

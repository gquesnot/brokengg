<div>
	<div class=" text-center flex my-4">
		<div class=" flex justify-around  w-full mr-2">
			<div class="mx-2 flex flex-col">
				<div>{{$stats->avg_kills}} / {{$stats->avg_deaths }} / {{$stats->avg_assists}}</div>
				<div>{{$stats->kda}} KDA</div>

			</div>
			<div class="mx-2">
				{{$stats->kill_participation}} % KP
			</div>
			<div class="mx-2 flex flex-col">
				<div>{{$stats->game_won}} W / {{$stats->game_lose }} L / {{$stats->game_played}} Games</div>
				<div>{{$stats->win_percent}} % win</div>
			</div>
		</div>
	</div>
	<div class="flex flex-col"  wire:poll.visible.5s>
		@foreach($matches as $match)
			<x-match-row :match="$match" :version="$version" :me="$me" ></x-match-row>
		@endforeach
	</div>
	{{$matches->links()}}

</div>

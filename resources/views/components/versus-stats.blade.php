@props([
    /** @var \null */
    'stats',
    'me',
    'other'
])

<div {{ $attributes->class([' text-center flex my-4']) }}>
	<div class=" flex justify-around w-1/2 mr-2">
		<div
			class=" flex flex-col items-center font-medium flex justify-center items-center mx-2">champions
			<div class="font-bold flex items-center">
				<span class="mx-2">{{$stats['me']->avg_kills}}</span>/<span
					class="mx-2 text-red-600">{{$stats['me']->avg_deaths}}</span>/<span
					class="mx-2">{{$stats['me']->avg_assists}}</span>
			</div>
			<div>
				{{$stats['me']->kda}} KDA
			</div>
		</div>
		<div class="mx-2">
			{{$stats['me']->kill_participation}} % KP
		</div>
		<div class="mx-2 flex flex-col">
			<div>{{$stats['me']->game_won}} W / {{$stats['me']->game_lose}} L / {{$stats['me']->game_played}} Games</div>
			<div>{{$stats['me']->win_percent}} % win</div>
		</div>
		<div class="mx-2 text-xl font-medium cursor-pointer">
			<a href="{{route('summoner', ['summonerId'=> $me->id])}}">{{$me->name}}</a>

		</div>
	</div>
	<div class="flex justify-around w-1/2">
		<div class="mx-2 text-xl font-medium cursor-pointer">
			<a href="{{route('summoner', ['summonerId'=> $other->id])}}">{{$other->name}}</a>

		</div>
		<div class="mx-2 flex flex-col">
			<div>{{$stats['other']->game_won}} Win / {{$stats['other']->game_lose}} L / {{$stats['other']->game_played}} Games</div>
			<div>{{$stats['other']->win_percent}} % win</div>
		</div>
		<div class="mx-2">

			{{$stats['other']->kill_participation}} % KP
		</div>

		<div
			class=" flex flex-col items-center font-medium flex justify-center items-center mx-2">
			<div class="font-bold flex items-center">
				<span class="mx-2">{{$stats['other']->avg_kills}}</span>/<span
					class="mx-2 text-red-600">{{$stats['other']->avg_deaths}}</span>/<span
					class="mx-2">{{$stats['other']->avg_assists}}</span>
			</div>
			<div>
				{{$stats['other']->kda}} KDA
			</div>
		</div>
	</div>
</div>
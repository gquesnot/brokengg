@props([
    /** @var \null */
    'stats',
    'me',
    'other'
])

<div {{ $attributes->class([' text-center flex my-4']) }}>
	<div class=" flex justify-around w-1/2 mr-2">
		<div class="mx-2">
			{{$stats['me']['kda']}} KDA
		</div>
		<div class="mx-2">
			{{$stats['me']['kill_participation']}} % KP
		</div>
		<div class="mx-2 flex flex-col">
			<div>{{$stats['me']['game_won']}} Win / {{$stats['me']['game_played']}} Games</div>
			<div>{{$stats['me']['win_percent']}} % win</div>
		</div>
		<div class="mx-2 text-xl font-medium">
			{{$me->name}}
		</div>
	</div>
	<div class="flex justify-around w-1/2">
		<div class="mx-2 text-xl font-medium">
			{{$other->name}}

		</div>
		<div class="mx-2 flex flex-col">
			<div>{{$stats['other']['game_won']}} Win / {{$stats['other']['game_played']}} Games</div>
			<div>{{$stats['other']['win_percent']}} % win</div>
		</div>
		<div class="mx-2">

			{{$stats['other']['kill_participation']}} % KP
		</div>

		<div class="mx-2">
			{{$stats['other']['kda']}} KDA
		</div>
	</div>
</div>
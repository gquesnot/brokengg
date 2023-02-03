<div>
	<div @class(["border shadow flex p-2 my-1", "bg-red-200" => $participant['vs'], "bg-blue-200" => !$participant['vs']])>
		<div>
			<img
				src="https://ddragon.leagueoflegends.com/cdn/{{$version}}/img/champion/{{$participant['champion']['img_url']}}"
				@class(["w-6 h-6" => true, 'rounded-full' => $participant['summonerId'] == $me['summoner_id']  , 'rounded' => $participant['summonerId'] != $me['summoner_id']])>
		</div>
		<div class="font-medium text-xl mx-2">
			{{$participant['summonerName']}}
		</div>

		<div class="flex items-center justify-center">
			<span class="cursor-pointer">
				@if($participant['total'] > 0)
					<a href="{{route(\App\Enums\TabEnum::VERSUS->value, ['summonerId' => $me['id'], 'otherSummonerId' => $participant['id']])}}">
						({{$participant['total']}})
					</a>
				@else
					(0)
				@endif
			</span>
		</div>
	</div>
</div>

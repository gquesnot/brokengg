<tr class="text-center">
	<td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 flex">
		<img
			src="http://ddragon.leagueoflegends.com/cdn/{{$version}}/img/champion/{{$champion->champion->img_url}}" alt="{{$champion->champion->name}}"
			class="w-6 h-6 rounded-full mr-2"/>
		{{$champion->champion->name}}
	</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
		<div class="flex flex-col">
			<div>{{$champion->wins}} W / {{$champion->loses}} L / {{$champion->total}} Games  </div>
			<div>{{$champion->winrate}} % win</div>
		</div>
	</td>

	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
		<div class="flex flex-col">
			<div>
				<span class="mr-1">{{$champion->avg_kills}}</span>/
				<span class="mr-1">{{$champion->avg_deaths}}</span>/
				<span>{{$champion->avg_assists}}</span>
			</div>
			<div>{{$champion->avg_kda}} KDA</div>
		</div>

	</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$champion->avg_gold}}</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$champion->avg_cs}}</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$champion->max_kills}}</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$champion->max_deaths}}</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$champion->avg_damage_dealt_to_champions}}</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$champion->avg_damage_taken}}</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$champion->total_double_kills}}</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$champion->total_triple_kills}}</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$champion->total_quadra_kills}}</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$champion->total_penta_kills}}</td>

</tr>
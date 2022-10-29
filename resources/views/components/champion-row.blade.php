<tr class="text-center">
	<td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 flex">
		<img
			src="http://ddragon.leagueoflegends.com/cdn/{{$version}}/img/champion/{{$champion->img_url}}" alt="{{$champion->name}}"
			class="w-6 h-6 rounded-full mr-2"/>
		{{$champion->name}}
	</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
		<div class="flex flex-col">
			<div>{{$champion->win}} Win / {{$champion->total}} Games  </div>
			<div>{{$champion->winrate}} % win</div>
		</div>
	</td>

	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
		<div class="flex flex-col">
			<div>
				<span class="mr-1">{{$champion->kills}}</span>/
				<span class="mr-1">{{$champion->deaths}}</span>/
				<span>{{$champion->assists}}</span>
			</div>
			<div>{{$champion->kda}} KDA</div>
		</div>

	</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$champion->gold}}</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$champion->cs}}</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$champion->max_kills}}</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$champion->max_death}}</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$champion->avg_damage_dealth}}</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$champion->avg_damage_taken}}</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$champion->double_kills}}</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$champion->triple_kills}}</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$champion->quadra_kills}}</td>
	<td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$champion->penta_kills}}</td>

</tr>
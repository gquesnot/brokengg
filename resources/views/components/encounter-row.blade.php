<div class="flex flex-col px-2 py-2 mx-2 my-2 bg-blue-200">
	<div class="flex  ">
		<div class="w-2/12">{{$other->name}}</div>
		<div class="w-1/12">{{$other->total}} times</div>
		<div class="w-1/12">
			<a href="{{route(\App\Enums\TabEnum::VERSUS->value, ['summonerId' => $me->id, 'otherSummonerId' => $other->id]).$this->getParamsUrl()}}">
				<button
					class=" ml-4 w-fit inline-flex items-center mr-4 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
					versus
				</button>
			</a>
		</div>
		<div class="w-2/12">
			<a href="{{route(\App\Enums\TabEnum::MATCHES->value, ['summonerId' => $other->id])}}">
				<button
					class=" ml-4 w-fit inline-flex items-center mr-4 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
					open summoner
				</button>
			</a>
		</div>
	</div>
</div>

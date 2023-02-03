<div @class(["flex my-0.5" , "w-16"=>$won])>
	<img
		src="{{Champion::url($version, $participant->champion->img_url)}}"
		alt="{{$participant->champion->name}}"
		@class(["w-6 h-6" => true, 'rounded-full' => $participant->id == $match->id  , 'rounded' => $participant->id != $match->id])/>

	<div
		class="flex ml-2 ">
		@if($participant->summoner_id == $me->id)
			<i class="fa-solid fa-crown"></i>
		@else
			<a href="{{route(\App\Enums\TabEnum::VERSUS->value, ['summonerId' => $me->id, 'otherSummonerId' => $participant->summoner_id]).$this->getParamsUrl()}}"><span
					{{--                                                wire:click="showVersus({{$participant->summoner_id}})"--}}
					class="cursor-pointer">
                                                                ({{$participant->total}})
                                                            </span></a>
		@endif
		<a href="{{route(\App\Enums\TabEnum::MATCHES->value, ['summonerId' => $participant->summoner_id])}}"
		   class=" ml-2 truncate w-36">
			{{$participant->summoner->name}}
		</a>
	</div>
</div>

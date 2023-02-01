@props([
      "meId",
      "detail",
      "version"
])
<div @class(["border flex justify-center border-gray my-2 py-2", "bg-blue-200" => $detail->me->won, "bg-red-200" => !$detail->me->won])>
    <div class="flex justify-center text-center items-center mr-4">
        <div>
            {{$detail->sinceMatchEnd()}}
        </div>
    </div>
    <div class="flex flex-col text-center">
        <div class="font-medium">{{$detail->mode->name}}</div>
        <div @class(["text-blue-600" => $detail->me->won , "text-red-600"=>!$detail->me->won, "text-center"])>{{$detail->me->won ? "won" : "lose"}}</div>
        <div>{{$detail->match_duration->format('H:i:s')}}</div>
    </div>
    <div class="flex w-1/3 justify-center">

        <div class="flex flex-col text-center items-center">
            <div class="relative flex justify-center items-center mr-4">
                <img alt="{{$detail->me->champion->name}}"
                     src="http://ddragon.leagueoflegends.com/cdn/{{$version}}/img/champion/{{$detail->me->champion->img_url}}"
                     class="w-16 h-16 rounded-full">
            </div>
            <div>{{$detail->me->champion->name}}</div>
        </div>
        <div
            class=" flex flex-col font-medium flex justify-center items-center">
            <div>
                <span class="mx-2">{{$detail->me->kills}}</span>/<span
                    class="mx-2 text-red-600">{{$detail->me->deaths}}</span>/<span
                    class="mx-2">{{$detail->me->assists}}</span>
            </div>
            <div>
                {{$detail->me->kda}} KDA
            </div>


        </div>


    </div>
    {{--                    <div class="flex justify-center items-center">--}}
    {{--                        <div>{{$detail->me->won == $detail->other->won ? "WITH" : "VS"}}</div>--}}
    {{--                    </div>--}}
    <div class="flex w-1/3 justify-center">
        <div
            class=" flex flex-col font-medium flex justify-center items-center">
            <div>
                <span class="mx-2">{{$detail->other->kills}}</span>/<span
                    class="mx-2 text-red-600">{{$detail->other->deaths}}</span>/<span
                    class="mx-2">{{$detail->other->assists}}</span>
            </div>
            <div>
                {{$detail->other->kda}} KDA
            </div>

        </div>
        <div class="flex flex-col text-center items-center">

            <div class="relative flex justify-center items-center ml-4">
                <img alt="{{$detail->other->champion->name}}"
                     src="http://ddragon.leagueoflegends.com/cdn/{{$version}}/img/champion/{{$detail->other->champion->img_url}}"
                     class="w-16 h-16 rounded-full">
            </div>
            <div>{{$detail->other->champion->name}}</div>
        </div>


    </div>
    <div class="flex items-center justify-end  mr-8">
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded h-fit">
            <a href="{{route(\App\Enums\TabEnum::MATCH_DETAIL->value, ['matchId' => $detail->id, 'summonerId' => $meId])}}">View</a>
        </button>
    </div>
</div>

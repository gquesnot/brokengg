<div>
    @if ($other != null)
        <div class="p-2 bg-gray-50 rounded shadow">
        <x-versus-stats :stats="$stats" :me="$me" :other="$other"/>
            <div class="border-b"></div>

        <div class="">
            <div class="mb-4">
                <div class="sm:hidden">
                    <label  for="with" class="sr-only">Select a tab</label>
                    <!-- Use an "onChange" listener to redirect the user to the selected tab URL. -->
                    <select wire:model="with"  name="with"
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">

                        @foreach($withOptions as $key=>$name)
                            <option value="{{$key}}">{{$name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="hidden sm:block">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8 text-center" aria-label="Tabs">
                            <!-- Current: "border-indigo-500 text-indigo-600", Default: "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" -->
                            @foreach($withOptions as $key => $name)
                                <div
                                    wire:click="$set('with', '{{$key}}')" @class(["cursor-pointer border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm w-full " => $with != $key, " w-full border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" => $with == $key])>
                                    {{$name}}
                                </div>
                            @endforeach

                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col my-4">
            @foreach($details as $detail)
                <x-versus-row :detail="$detail" :version="$version"/>
            @endforeach
        </div>
            {{$details->links()}}
    </div>

    @else
        <div class="text-center">
            <h1>No summoner selected</h1>
        </div>
    @endif
</div>

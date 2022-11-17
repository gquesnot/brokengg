<div>
    <div class="mx-2 ">
        <input type="text" wire:model.debounce.500ms="search"  class="form-control" placeholder="Search Summoner">
    </div>
    <div class="flex flex-col">
        @foreach($encounters as $encounter)
            <x-encounter-row :version="$version" :other="$encounter"  :me="$me"></x-encounter-row>
        @endforeach

    </div>
    {{$encounters->links()}}
</div>

<div>
    <div class="flex flex-col">
        @foreach($encounters as $encounter)
            <x-encounter-row :version="$version" :other="$encounter"  :me="$me"></x-encounter-row>
        @endforeach

    </div>
    {{$encounters->links()}}
</div>

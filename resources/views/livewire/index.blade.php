<div class="relative">
    <x-nav-bar></x-nav-bar>
    <x-all-flash></x-all-flash>
    <div class="w-1/2 mx-2 flex flex-col">
        <div class="w-1/2 flex ">
            <h3 class="text-3xl font-bold">Search Summoner</h3>
            <button wire:click="sync" class=" ml-4 w-fit h-fit inline-flex items-center mr-4 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Sync</button>
        </div>
        <div class="mt-1 sm:mt-0 sm:col-span-2 my-4">
            <input wire:model.lazy="summonerName" type="text" name="SummonerName" id="SummonerName"
                   placeholder="SummonerName"
                   autocomplete="summonername"
                   class="max-w-lg block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:max-w-xs sm:text-sm border-gray-300 rounded-md">
        </div>
        <button type="button" wire:click="searchSummoner"
                class="w-fit inline-flex items-center mr-4 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Search
        </button>
    </div>

</div>


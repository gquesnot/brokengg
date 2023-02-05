<div class="flex flex-col shadow rounded p-4 mt-2 w-full"
     x-data="{
     date_start: @entangle('filters.date_start'),
     date_end: @entangle('filters.date_end'),
     }"
>
    <div class="text-xl">Filters</div>
    <div class="flex ">
        <div class="w-1/2 flex flex-col">
            <div class="flex items-end my-2 ">

                <div class="w-24 flex">
                    <label for="queue" class="block font-medium text-gray-700 ml-2">Queue </label>
                </div>


                <div class="mx-2 flex flex-col">
                    {{--					<select id="queue" name="queue" wire:model="filters.queue"--}}
                    {{--					        class="mt-1 block w-48 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">--}}
                    {{--						<option value=""></option>--}}
                    {{--						@foreach($options['queue'] as  $option)--}}
                    {{--							<option value="{{$option['id']}}">{{$option['description']}}</option>--}}
                    {{--						@endforeach--}}
                    {{--					</select>--}}
                    <x-form.select2 model="filters.queue" :options="$options['queue']" :nullable="true"
                                    placeholder="Queue"/>

                </div>

            </div>
            <div class="flex items-end my-2">
                <div class="w-24 flex">
                    <label for="champion" class="block font-medium text-gray-700 ml-2">Champion </label>
                </div>

                <div class="mx-2 flex flex-col">
                    {{--					<select id="champion" name="champion" wire:model="filters.champion"--}}
                    {{--					        class="mt-1 block w-48 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">--}}
                    {{--						<option value=""></option>--}}
                    {{--						@foreach($options['champion'] as  $option)--}}
                    {{--							<option value="{{$option['id']}}">{{$option['name']}}</option>--}}
                    {{--						@endforeach--}}
                    {{--					</select>--}}
                    <x-form.select2 model="filters.champion" :options="$options['champion']" :opt-group="true"
                                    :nullable="true" placeholder="Champion"/>
                </div>
            </div>
            <div class="flex items-end my-2 ">
                <div class="w-60 flex">
                    <label for="champion" class="block font-medium text-gray-700 ml-2">Apply filters on encounters </label>
                </div>

                <div class="">
                    <x-input-toggle model="filters.filter_encounters"
                                    :value="$filters->filter_encounters"></x-input-toggle>
                </div>
            </div>
        </div>
        <div class="w-1/2 flex flex-col">
            <div class="flex items-end my-2 ">
                <div class="w-24 flex">
                    <label for="champion" class="block font-medium text-gray-700 ml-2">Date Start </label>
                </div>

                <div class="mx-2 flex flex-col">
                    <input type="date" x-model="date_start"
                           class="bg-gray-50 w-48 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

                </div>

                {{-- add tailwind error span --}}
            </div>

            <div class="flex items-end my-2 ">
                <div class="w-24 flex">
                    <label for="date_end" class="block font-medium text-gray-700 ml-2">Date End </label>
                </div>

                <div class="mx-2 flex flex-col">
                    <input type="date" x-model="date_end"
                           class="bg-gray-50 w-48 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

                </div>

            </div>
            <div>
                @error('filters.date_start')
                <x-alert :message="$message" type="error"></x-alert> @enderror
                @error('filters.date_end')
                <x-alert :message="$message" type="error"></x-alert> @enderror
                @error('filters.queue')
                <x-alert :message="$message" type="error"></x-alert> @enderror
                @error('filters.champion')
                <x-alert :message="$message" type="error"></x-alert> @enderror
            </div>

        </div>
    </div>
    <div class="flex items-end my-2 justify-start">
        <button type="submit" wire:click="applyFilters"
                class="ml-4 h-8 mr-4 w-fit inline-flex items-center mr-4 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">

            Apply Filter
        </button>
        <button type="submit" wire:click="clearFilter"
                class="ml-4 h-8 w-fit inline-flex items-center mr-4 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">

            Clear Filter
        </button>
    </div>
</div>


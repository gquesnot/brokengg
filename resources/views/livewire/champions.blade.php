<div class="px-4 sm:px-6 lg:px-8">
    <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300 text-center font-semibold">
                        <thead class="bg-gray-50 ">
                        <tr>
                            <th scope="col"
                                class=" text-center py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Champion
                            </th>

                            <th scope="col" class=" text-center px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Win Rate</th>
                            <th scope="col" class=" text-center px-3 py-3.5 text-left text-sm font-semibold text-gray-900">KDA</th>
                            <th scope="col" class=" text-center px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Gold</th>
                            <th scope="col" class=" text-center px-3 py-3.5 text-left text-sm font-semibold text-gray-900">CS</th>
                            <th scope="col" class=" text-center px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Max
                                Kills
                            </th>
                            <th scope="col" class=" text-center px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Max
                                Death
                            </th>
                            <th scope="col" class=" text-center px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Average
                                Damage Dealt
                            </th>
                            <th scope="col" class=" text-center px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Average
                                Damage Taken
                            </th>
                            <th scope="col" class=" text-center px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Double
                                kill
                            </th>
                            <th scope="col" class=" text-center px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Triple
                                kill
                            </th>
                            <th scope="col" class=" text-center px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Quadra
                                kill
                            </th>
                            <th scope="col" class=" text-center px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Penta
                                kill
                            </th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($champions as $champion)
                            <x-champion-row :champion="$champion" :version="$version"/>
                        @endforeach
                        </tbody>
                    </table>
                    {{$champions->links()}}
                </div>
            </div>
        </div>
    </div>
</div>

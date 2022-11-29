<div>
    <div class="flex pb-4">

        <div class="flex pr-3">

            <template x-for="(item, idx) in lol.items">
                <div style="max-width: 60.6167px" class="m-2"
                     @click="lol.remove_item(idx)"
                     x-tooltip="{

            content : () => lol.get_item_popup(item.id),
            allowHTML : true,
            appendTo: $root

         }"

                >

                    <img alt=""
                         class=" rounded border-b-1 cursor-pointer z-20"
                         style="max-width: 50px; max-height: 50px; object-fit: cover; object-position: center"
                         :src="'http://ddragon.leagueoflegends.com/cdn/'+lol.version+'/img/item/'+item.id +'.png'"/>
                </div>
            </template>


        </div>

        <button
            class="mx-3 inline-flex text-center h-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm"
            @click="lol.open_modal = true">Edit Item
        </button>
        <div class="flex items-center justify-between w-50 ml-6">

            <!-- Enabled: "bg-indigo-600", Not Enabled: "bg-gray-200" -->
            <button type="button"
                    @click="lol.toggle_change_items = !lol.toggle_change_items"
                    :class="lol.toggle_change_items ? ' bg-indigo-600 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500' : ' bg-gray-200 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500'"
                    role="switch" aria-checked="false" aria-labelledby="availability-label"
                    aria-describedby="availability-description"

            >
                <!-- Enabled: "translate-x-5", Not Enabled: "translate-x-0" -->
                <span aria-hidden="true"
                      :class="lol.toggle_change_items ? ' translate-x-5 pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200' : ' translate-x-0 pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200'"
                >

                    </span>

            </button>
            <span class="flex-grow flex flex-col ml-4">
    <span class="text-sm font-medium text-gray-900 " id="availability-label">block items change</span>

  </span>
        </div>

    </div>

    <div x-show="lol.open_modal" x-cloak>
        <div class="fixed z-20 inset-0  overflow-y-auto" aria-labelledby="modal-title" role="dialog">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     aria-hidden="true" @click="lol.open_modal = false"></div>

                <!-- This element is to trick the browser into centering the modal contents. -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                      aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg p-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-4 sm:align-middle sm:w-full sm:p-6"
                    style="max-width: 70rem"
                >
                    <div class="flex flex-col">
                        <div class="w-full flex">

                            <div class="mr-3 items-center self-center w-1/4"> My Item List</div>
                            <div class="flex pr-3 w-1/2 ">
                                <template x-for="(item, idx) in  lol.items">
                                    <x-shop-item-self/>
                                </template>

                                <template x-if="lol.items.length === 0">
                                    <div
                                        class="w-16 h-16 block aspect-w-6 mx-2 rounded-lg bg-gray-100 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-offset-gray-100 focus-within:ring-indigo-500 overflow-hidden">

                                    </div>
                                </template>
                            </div>
                            <div class="items-center self-center">
                                <h5 class="title">Current Gold: <span x-html="lol.current_gold"></span></h5>
                            </div>
                        </div>
                        <div class="w-full flex">
                            <div class="bg-white shadow overflow-hidden rounded-md w-1/4 mr-6 h-full">
                                <ul role="list" class="divide-y divide-gray-200">
                                    <template x-for="(item_category, idx) in lol.items_categories">
                                        <li class="relative">
                                            <button @click="lol.select_category(idx)"
                                                    :class="lol.category == idx ? 'btn text-white w-full focus:border-none bg-indigo-700': 'btn-white w-full'"
                                                    style="height:2rem"
                                            ><span x-html="item_category.name"></span>
                                            </button>
                                        </li>
                                    </template>

                                </ul>
                            </div>

                            <div class="overflow-auto w-3/4 flex flex-wrap content-start" style="max-height: 480px">
                                <template x-for="item in lol.modified_items">
                                    <x-shop-item/>
                                </template>

                            </div>
                        </div>
                        <div class="mt-5 sm:mt-6 flex justify-around">
                            <button type="button" @click="lol.reset_items"
                                    class="mt-3 mx-3 w-1/3 inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                reset
                            </button>

                            <button type="button" @click="lol.open_modal = false"
                                    class="w-1/3 mx-3 inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">

                                Close
                            </button>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>


</div>

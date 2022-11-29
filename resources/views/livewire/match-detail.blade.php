<div >

    <div class=" m-auto" x-data="lol_class(@js($participants), @js($items), @js($version), @js($participant_idx))">
        <div class="flex mt-6">
            <div class="w-1/4 h-10 mr-4">
                <div class="">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Summoners
                    </label>
                    <select

                        x-on:change="lol.select_participant($el.value)"
                        class="form-select appearance-none
              block
              w-full
              px-3
              py-1.5
              text-base
              font-normal
              text-gray-700
              bg-white bg-clip-padding bg-no-repeat
              border border-solid border-gray-300
              rounded
              transition
              ease-in-out
              m-0
              focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                        aria-label="Default select example">
                        <template x-for="(participant_option, idx) in lol.participants">
                            <option :selected="lol.participant_id === participant_option.id"  :value="participant_option.id" x-text="participant_option.name  + ' - '+ participant_option.champion.name"></option>
                        </template>


                    </select>
                </div>
            </div>
            <div class="w-3/4 h-10 ml-44">
                <div class="relative pt-1 flex flex-col">
                    <label for="customRange1" class="form-label">Match Timelines : <span x-text="lol.frame_id"></span>
                        minutes</label>
                    <input
                        x-on:change="lol.select_frame($el.value)"
                        x-model.debounce.750ms="lol.frame_id"
                        min="0"
                        :max="lol.max_frame"
                        type="range"
                        class="w-2/3"
                        id="customRange1"
                    />
                </div>
            </div>
        </div>
        <template x-if="lol.participant != null">
            <div class="">
                <div class="mt-20 flex justify-between">

                    <div class="w-1/4 ml-5">
                        <x-my-summoner-frame></x-my-summoner-frame>
                    </div>
                    <div class="w-2/3">
                        <x-enemy-summoners-frame></x-enemy-summoners-frame>
                    </div>

                </div>
                <div class="mt-12">

                        <x-shop></x-shop>


                </div>
            </div>

        </template>
    </div>


</div>



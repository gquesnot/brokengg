<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">

                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Summoner Name
                        </th>

                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Armor
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Magic Resit
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Physical Damage Taken
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Magical Damage Taken
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dps Ad received
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dps Ap received
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            DPS true damage received
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            DPS received
                        </th>

                    </tr>
                    </thead>
                    <template x-if="lol.enemy_participants != null">
                        <tbody>
                        <template x-for="(enemy_participant, idx) in lol.enemy_participants" :key="idx">
                            <tr :class=" idx %2 === 0  ?'bg-white' : 'bg-gray-100'">
                                <td class=" py-2 whitespace-nowrap text-sm font-medium text-gray-900 flex justify-center content-center" >
                                    <img :src="'http://ddragon.leagueoflegends.com/cdn/' + lol.version+'/img/champion/' + enemy_participant.champion.name + '.png'"
                                         class="h-10 w-10 rounded-full" alt="">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    x-text="enemy_participant.name">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    x-text="`${enemy_participant.stats.armor} -> ${enemy_participant.stats.real_armor}`">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    x-text="`${enemy_participant.stats.mr} -> ${enemy_participant.stats.real_mr}`">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    x-text="enemy_participant.stats.armor_reduction + '%'">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    x-text="enemy_participant.stats.mr_reduction  + '%'">

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    x-text=" enemy_participant.stats.dps_ad_damage_taken">

                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    x-text="enemy_participant.stats.dps_ap_damage_taken">

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    x-text="enemy_participant.stats.dps_true_damage_taken">

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"

                                    x-text="enemy_participant.stats.dps_total_damage_taken">
                                </td>



                            </tr>
                        </template>
                        </tbody>
                    </template>

                </table>
            </div>
        </div>
    </div>
</div>

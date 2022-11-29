<div class="flex flex-col" >
    <template x-if="lol.participant != null">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-3 lg:p2-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stat
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Value
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Odd row -->
                        <tr class="bg-white">
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                Total Gold :

                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                x-text="lol.total_gold">

                            </td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                Current Gold :

                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                x-text="lol.current_gold">

                            </td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                Level :

                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                x-text="lol.participant.current_frame.level">

                            </td>
                        </tr>
                        <tr class="bg-white">
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                AD:

                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                x-text="lol.participant.stats.ad">

                            </td>
                        </tr>
                        <tr class="bg-white">
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                OnHit Ad:

                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                x-text="'not implemented'">

                            </td>
                        </tr>
                        <tr class="bg-white">
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                OnHit Ap:

                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                x-text="'not implemented'">

                            </td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                AS:

                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                x-text="lol.participant.stats.as">

                            </td>
                        </tr>
                        <tr class="bg-white">
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                CRIT:

                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                x-text="lol.participant.stats.crit_percent + '%'">

                            </td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                ARMOR PEN:

                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500" colspan="" x-text="lol.participant.stats.total_armor_pen()">

                                >
                            </td>
                        </tr>
                        <tr class="bg-white">
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                AP:

                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                x-text="lol.participant.stats.ap">

                            </td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                MAGIC PEN:

                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500" x-text="lol.participant.stats.total_magic_pen()">
                            </td>
                        </tr>
                        <tr class="bg-white">
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                HP:

                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                x-text="lol.participant.stats.hp">

                            </td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                ARMOR:

                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                x-text="lol.participant.stats.armor">

                            </td>
                        </tr>
                        <tr class="bg-white">
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                MAGIC RESIST:

                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                x-text="lol.participant.stats.mr">

                            </td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                CDR:

                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                x-text="lol.participant.stats.cdr + '%'">

                            </td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                Dps Ad:

                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                x-text="lol.participant.stats.dps_ad">

                            </td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                Dps Ap:

                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                x-text="lol.participant.stats.dps_ap">

                            </td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                Dps True damage:

                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                x-text="'not implemented'">

                            </td>
                        </tr>
                        <tr class="bg-white">
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                DPS:

                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500"
                                x-text="'not implemented'">

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </template>

</div>

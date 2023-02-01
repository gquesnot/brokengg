import items_categories from "./data/items_category";
import ItemCategory from "./data/item_category";
import Item from "./classes/item/item";
import Participant from "./classes/participant/participant";
import ItemsController from "./classes/items_controller";
import {plainToClass} from 'class-transformer';
import {
    has_guinsoo,
    has_ie,
    is_guinsoo,
    is_ie,
} from "./util/util";
import ParticipantPerks from "./classes/participant/participant_perks";


export default class Lol {

    items_categories: ItemCategory[] = items_categories;
    version: string;
    category_id: number = 0;
    total_gold: number = 0;
    current_gold: number = 0;
    frame_id: number = 0;
    max_frame: number = 0;
    participant_id: number = 0;
    open_modal: boolean = false;
    toggle_change_items: boolean = false;
    all_items: Record<string, Item> = {};
    modified_items: Item[] = [];
    items: Item[] = [];

    participants: Participant[] = [];
    enemy_participants: Participant[] = [];
    participant: Participant;

    items_controller: ItemsController;

    constructor(participants: Participant[], items: Record<string, Item>, version: string, participant_id: number) {
        this.version = version;

        participants.forEach((participant) => {
            participant = plainToClass(Participant, participant);
            participant.perks = plainToClass(ParticipantPerks, participant.perks);
            this.participants.push(participant);
        });
        this.all_items = items;
        this.items = [];
        this.items_controller = new ItemsController(items);
        this.select_category(0);
        this.participant_id = participant_id;
        this.participant = this.participants[participant_id - 1];
        this.max_frame = this.participant.frames.length - 1;
        console.info(this.participant)
        this.select_participant(participant_id);
    }


    select_participant(participant_id: number) {
        this.participant_id = participant_id;
        this.participant = this.participants[participant_id - 1];
        this.update_participants_current_frame();
        this.update_all();
    }

    update_participants_current_frame(){
        this.participants.forEach((participant) => {
            participant.select_participant_frame(this.frame_id);
        });
    }


    select_frame(frame_id: number) {
        this.frame_id = frame_id;
        this.update_participants_current_frame();
        this.update_all();
    }


    set_enemy_participants() {
        this.enemy_participants = [];
        this.participants.forEach((participant) => {
            if (participant.won !== this.participant.won) {
                this.enemy_participants.push(participant);
            }
        });
    }

    update_all(update_items: boolean = true) {

        this.update_participant(update_items);
        this.set_enemy_participants();
        this.update_enemy_participants();
    }

    update_enemy_participants() {


        this.enemy_participants.forEach((participant) => {
           participant.set_enemy_damage_receive(this.participant, this.items);
        });
    }


    update_participant(update_items: boolean = true) {
        this.participant.stats.reset();
        if (update_items && !this.toggle_change_items) {
            this.items_controller.update_items(this.participant, this.frame_id);
            this.items = this.items_controller.items_from_list();
        }

        this.participant.calculate_stats(this.items);
        this.calculate_gold();
        this.participant.calculate_dps(this.items);
        this.participant.stats.round_all();

    }


    calculate_gold() {
        this.total_gold = this.participant.current_frame.total_gold;
        this.current_gold = this.total_gold;
        this.items.forEach((item) => {
            this.current_gold -= item.gold;
        });

    }

    item_has_category(category: ItemCategory, item: Item): boolean {
        for (let i = 0; i < item.tags.length; i++) {
            if (category.tags.includes(item.tags[i])) {
                return true;
            }
        }
        return false;
    }

    select_category(category_id: number) {
        this.category_id = category_id;
        let category = this.items_categories[category_id];
        this.modified_items = [];
        for (const [_, item] of Object.entries(this.all_items)) {
            if ((this.item_has_category(category, item) || category_id === 0) && item) {
                this.modified_items.push(item);
            }
        }


    }

    add_item(item_id: number) {
        let item = this.get_item(item_id);
        if (!item || this.items.length >= 6) {
            return;
        }
        if (has_ie(this.items) && is_guinsoo(item_id) || has_guinsoo(this.items) && is_ie(item_id)) {
            return;
        }
        if (this.items.includes(item)) {
            if (item.type == "legendary" || item.type == "mythic") {
                return;
            }
        }
        if (item.type == "mythic") {
            // check if already have mythic
            let has_mythic = this.items.some((item: Item) => {
                return item.type === 'mythic';
            });

            if (has_mythic) {
                return;
            }
        }
        this.items.push(item);
        this.update_all(false);

    }

    remove_item(index: number) {
        this.items.splice(index, 1);
        this.update_all(false);
    }

    reset_items() {
        this.items = [];
        this.update_all(false);
    }


    get_item(item_id: number): Item | null {
        if (item_id.toString() in this.all_items) {
            return this.all_items[item_id];
        }
        return null;
    }

    get_item_popup(item_id: number): string {
        let item = this.get_item(item_id);
        if (!item) {
            return "";
        }
        let desc = `
         <div class="flex flex-col border  p-4 rounded  bg-indigo-500 relative z-30" x-cloak>
            <div class="flex flex-col ">
                <div class="flex justify-between z-30">
                    <div class="flex z-30">
                        <img alt=""
                         class="border border-1 border-black mr-2 block z-30"
                         style="max-width: 50px"
                         src="https://ddragon.leagueoflegends.com/cdn/${this.version}/img/item/${item.id}.png" />
                        <div  class="z-30 text-base"><span class="font-bold ">${item.name}</span><br>${item.gold} gold</div>
                    </div>

                </div>
           </div>
            <div class="flex flex-col mt-2">
                <div class="font-bold">Stats :</div>

        `;
        if (item.stats == null) {
            desc += "No stats";
        } else {
            Object.entries(item.stats).forEach(([key, value]) => {
                if (value !== 0) {
                    desc += `<div class="ml-2 w-full flex justify-between relative">
                              <div class="">${key}</div>
                              <div >${value}</div>
                          </div>`
                }
            });
        }


        if (item.type === 'mythic' && item.mythic_stats !== null) {
            desc += `<h2 class="font-bold mt-2">Mythic :</h2>`;
            Object.entries(item.mythic_stats).forEach(([key, value]) => {
                if (value !== 0) {
                    desc += `<div class="ml-2 w-full flex justify-between relative">
                              <div class="">${key}</div>
                              <div >${value}</div>
                          </div>`
                }
            });
        }
        desc += `</div></div>`
        return desc
    }





}

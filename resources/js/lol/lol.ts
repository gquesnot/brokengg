import items_categories from "./data/itemsCategory";
import ItemCategory from "./data/ItemCategory";
import Item from "./classes/item";
import Participant from "./classes/Participant";
import ItemsController from "./classes/items_controller";
import {plainToClass} from 'class-transformer';
import {is_brk, is_dominik, is_guinsoo, is_ie, is_nashor, is_rageknife, is_witsend} from "./util/util";


export default class Lol {

    items_categories: ItemCategory[] = items_categories;
    version: string;
    category: number = 0;
    total_gold: number = 0;
    current_gold: number = 0;
    frame_id: number = 0;
    max_frame: number = 0;
    participant_id: number = 0;
    open_modal: boolean = false;
    toggle_change_items: boolean = false;
    all_items: { [name: string]: Item } = {};
    modified_items: Item[] = [];
    items: Item[] = [];

    participants: Participant[] = [];
    enemy_participants: Participant[] = [];
    participant: Participant;

    items_controller: ItemsController;

    constructor(participants: Participant[], items: { [name: string]: Item }, version: string, participant_id: number) {
        this.version = version;

        participants.forEach((participant) => {
            participant = plainToClass(Participant, participant);
            this.participants.push(participant);
        });
        this.all_items = items;
        this.items = [];
        this.items_controller = new ItemsController(items);
        this.select_category(0);
        this.participant_id = participant_id;
        this.participant = this.participants[participant_id - 1];
        this.max_frame = this.participant.frames.length - 1;
        this.select_participant(participant_id);
        console.log('select frame', this.frame_id);
        console.log('update all');

        console.log('select category', 0);
        console.log('lol', this);
    }


    select_participant(participant_id: number) {
        this.participant_id = participant_id;
        this.participant = this.participants[participant_id - 1];
        this.participant.select_participant_frame(this.frame_id);
        this.update_all();
    }


    select_frame(frame_id: number) {
        this.frame_id = frame_id;
        this.participant.select_participant_frame(this.frame_id);
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
            participant.stats.reset();
            participant.set_stats_from_frame(this.frame_id);
            participant.stats.real_armor = (participant.stats.armor * (1 - this.participant.stats.armor_pen_percent / 100)) - this.participant.stats.armor_pen_flat;
            participant.stats.real_mr = (participant.stats.mr * (1 - this.participant.stats.magic_pen_percent / 100)) - this.participant.stats.magic_pen_flat;
            participant.stats.real_mr = participant.stats.real_mr < 0 ? 0 : participant.stats.real_mr;
            participant.stats.real_armor = participant.stats.real_armor < 0 ? 0 : participant.stats.real_armor;
            participant.stats.armor_reduction = (100 / (participant.stats.real_armor + 100)) * 100;
            participant.stats.mr_reduction = (100 / (participant.stats.real_mr + 100)) * 100;
            console.log("enemy", participant.name, participant.stats);

            let base_dps_ad = this.participant.stats.dps_ad;
            //brk
            if (this.has_brk()) {
                if (participant.champion) {
                    let damage_supp = participant.champion.stats.attack_range > 250 ? (participant.stats.hp * 0.08) : (participant.stats.hp * 0.12);
                    damage_supp = damage_supp < 15 ? 15 : damage_supp;
                    base_dps_ad += this.has_guinsoo() ? damage_supp * 0.3 : damage_supp;
                }
            }
            // has dominik
            if (this.has_dominik()) {
                let hp_diff = participant.stats.hp - this.participant.stats.hp;
                hp_diff = hp_diff < 0 ? 0 : hp_diff > 2000 ? 2000 : hp_diff;
                let dps_percent = 1 + (hp_diff * 0.0075 / 100) / 100;
                base_dps_ad *= dps_percent;
            }
            participant.stats.dps_ad_damage_taken = base_dps_ad * participant.stats.armor_reduction / 100;
            participant.stats.dps_ap_damage_taken = this.participant.stats.dps_ap * participant.stats.mr_reduction / 100;
            participant.stats.dps_true_damage_taken = this.participant.stats.dps_true;
            participant.stats.dps_total_damage_taken = participant.stats.dps_ad_damage_taken + participant.stats.dps_ap_damage_taken + participant.stats.dps_true_damage_taken;
            console.log(
                'enemy',
                participant.name,
                base_dps_ad,
                participant.stats.dps_ad_damage_taken,
                participant.stats.dps_ap_damage_taken,
            )
            participant.stats.round_all();
        });
    }


    update_participant(update_items: boolean = true) {
        this.participant.stats.reset();
        console.log('update participant', update_items, !this.toggle_change_items);
        if (update_items && !this.toggle_change_items) {
            this.items_controller.update_items(this.participant, this.frame_id);
            this.items = this.items_controller.items_from_list();
        }
        console.log('items', this.items);
        this.participant.add_champion_stats(this.frame_id);
        this.calculate_items();
        this.calculate_gold();
        this.calculate_dps();
        this.participant.stats.round_all();
    }


    calculate_dps() {
        let has_ie = this.has_ie();
        let has_guinsoo = this.has_guinsoo();
        let has_rageknife = this.has_rageknife();
        let has_nashor = this.has_nashor();
        let has_witsend = this.has_witsend();
        this.participant.stats.dps_ad = this.participant.stats.ad * this.participant.stats.as;
        if (this.participant.stats.crit_percent > 0) {
            if (!has_guinsoo) {
                let crit_damage = 0.75 + (has_ie ? 0.35 : 0);

                this.participant.stats.dps_ad *= 1 + (this.participant.stats.crit_percent/100 * crit_damage);
            } else if(has_guinsoo) {
                this.participant.stats.on_hit_ad += this.participant.stats.crit_percent * 2;
                this.participant.stats.crit_percent = 0;
            }
            else if (!has_rageknife){
                this.participant.stats.on_hit_ad  += this.participant.stats.crit_percent * 1.75
            }
        }
        if (has_nashor) {
            this.participant.stats.on_hit_ap += (15 + this.participant.stats.ap * 0.2)  * (has_guinsoo ? 1.3 : 1);
        }
        if (has_witsend) {
            let witsend_damage = 15;
            let current_frame = this.participant.frames[this.frame_id];
            if (current_frame.level >= 9 ){
                // get level between 9 and 15
                let level = current_frame.level - 8;
                level = level > 6 ? 6 : level;
                witsend_damage += level * 10;

                // get level between 15 and 18
                if (current_frame.level >= 15) {
                    level = current_frame.level - 14;
                    witsend_damage += level * 1.25;
                }
            }
            this.participant.stats.on_hit_ap += witsend_damage  * (has_guinsoo ? 1.3 : 1);
        }

        this.participant.stats.dps_ad += this.participant.stats.on_hit_ad * this.participant.stats.as;
        this.participant.stats.dps_ap = this.participant.stats.on_hit_ap * this.participant.stats.as;
        this.participant.stats.dps_true = 0; // todo: add true damage
        this.participant.stats.dps_total = this.participant.stats.dps_ad + this.participant.stats.dps_ap + this.participant.stats.dps_true;
    }

    calculate_items() {

        let nb_legendary: number = this.items.filter((item: Item) => {
            return item.type === 'legendary';
        }).length;
        this.items.forEach((item) => {
            this.participant.stats.add_item(item, nb_legendary);
        });
        this.participant.stats.as = this.participant.stats.base_as * (1 + this.participant.stats.as_percent / 100);
        if (this.participant.stats.as > 2.5) {
            this.participant.stats.as = 2.5;
        }
        if (this.participant.stats.ah !== 0) {
            this.participant.stats.cdr = (1 - (100 / (100 + this.participant.stats.ah))) * 100;
        }
        else{
            this.participant.stats.cdr = 0;
        }
        if (this.participant.stats.adaptative != 0) {
            // todo: handle adpative, compare ad - base ad and ap
        }

    }


    calculate_gold() {
        this.total_gold = this.participant.frames[this.frame_id].total_gold;
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
        this.category = category_id;
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
        if (this.has_ie(false) && is_guinsoo(item_id) || this.has_guinsoo() && is_ie(item_id)) {
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


    has_guinsoo(): boolean {
        return this.items.some((item) => {
            return is_guinsoo(item.id);
        });
    }

    has_ie(check_crit_percent: boolean = true): boolean {
        return this.items.some((item) => {
            if (!check_crit_percent) {
                return is_ie(item.id);
            }
            return is_ie(item.id) && this.participant.stats.crit_percent > 0.6;
        })
    }

    has_brk(): boolean {
        return this.items.some((item) => {
            return is_brk(item.id);
        });
    }

    has_dominik(): boolean {
        return this.items.some((item) => {
            return is_dominik(item.id);
        });
    }

    has_nashor(): boolean{
        return this.items.some((item) => {
           return is_nashor(item.id)
        });
    }
    has_witsend(): boolean{
        return this.items.some((item) => {
           return is_witsend(item.id)
        });
    }

    has_rageknife(): boolean{
        return this.items.some((item) => {
           return is_rageknife(item.id)
        });
    }


}

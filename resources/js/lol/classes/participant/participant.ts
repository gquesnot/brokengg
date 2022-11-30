import {Frame} from "./frame/frame";
import {Champion} from "../champion/champion";
import Stats from "./stats";
import Item from "../item/item";
import {has_brk, has_dominik, has_guinsoo, has_ie, has_nashor, has_rageknife, has_witsend} from "../../util/util";
import ParticipantPerks from "./participant_perks";



export default class Participant {
    frames: Frame[] = [];
    current_frame: Frame = new Frame();
    id: number = 0;
    name: string = "";
    items: number[] = [];
    champion: Champion = new Champion();
    won: boolean = true;
    profile_icon_id: number = 0;
    puuid: string = "";
    stats: Stats = new Stats();
    perks: ParticipantPerks = new ParticipantPerks();




    add_perks_stats(){
        this.stats.add_perks(this.perks, this.current_frame.level);
    }


    add_champion_stats() {
        if (this.champion !== null) {
            this.stats.add_champion(this.champion, this.current_frame.level);
        }
    }

    set_stats_from_current_frame() {
        this.stats.add_frame(this.current_frame);
    }

    select_participant_frame(frame_id: number) {
        this.current_frame = this.frames[frame_id];
    }

    calulate_items(items: Item[]) {
        let nb_legendary: number = items.filter((item: Item) => {
            return item.type === 'legendary';
        }).length;
        items.forEach((item) => {
            this.stats.add_item(item, nb_legendary);
        });
        this.stats.as = this.stats.base_as * (1 + this.stats.as_percent / 100);
        if (this.stats.as > 2.5) {
            this.stats.as = 2.5;
        }
        if (this.stats.ah !== 0) {
            this.stats.cdr = (1 - (100 / (100 + this.stats.ah))) * 100;
        } else {
            this.stats.cdr = 0;
        }
        if (this.stats.adaptative.ad || this.stats.adaptative.ap) {
            if (this.stats.base_ad > this.stats.ap) {
                this.stats.ad +=  this.stats.adaptative.ad;
                this.stats.ap +=  this.stats.adaptative.ap;
            }
        }
    }

    calculate_dps(items: Item[]) {
        let has_guinsoo_ = has_guinsoo(items);
        this.stats.dps_ad = this.stats.ad * this.stats.as;
        if (this.stats.crit_percent > 0) {
            if (!has_guinsoo_) {
                let crit_damage = 0.75 + (has_ie(items, this.stats.crit_percent) ? 0.35 : 0);

                this.stats.dps_ad *= 1 + (this.stats.crit_percent / 100 * crit_damage);
            } else if (has_guinsoo_) {
                this.stats.on_hit_ad += this.stats.crit_percent * 2;
                this.stats.crit_percent = 0;
            } else if (!has_rageknife(items)) {
                this.stats.on_hit_ad += this.stats.crit_percent * 1.75
            }
        }
        if (has_nashor(items)) {
            this.stats.on_hit_ap += (15 + this.stats.ap * 0.2) * (has_guinsoo_ ? 1.3 : 1);
        }
        if (has_witsend(items)) {
            let witsend_damage = 15;
            if (this.current_frame.level >= 9) {
                // get level between 9 and 15
                let level = this.current_frame.level - 8;
                level = level > 6 ? 6 : level;
                witsend_damage += level * 10;

                // get level between 15 and 18
                if (this.current_frame.level >= 15) {
                    level = this.current_frame.level - 14;
                    witsend_damage += level * 1.25;
                }
            }
            this.stats.on_hit_ap += witsend_damage * (has_guinsoo_ ? 1.3 : 1);
        }

        this.stats.dps_ad += this.stats.on_hit_ad * this.stats.as;
        this.stats.dps_ap = this.stats.on_hit_ap * this.stats.as;
        this.stats.dps_true = 0; // todo: add true damage
        this.stats.dps_total = this.stats.dps_ad + this.stats.dps_ap + this.stats.dps_true;
    }

    set_enemy_damage_receive(participant: Participant, items: Item[]) {
        this.stats.reset();
        this.set_stats_from_current_frame();
        this.stats.real_armor = (this.stats.armor * (1 - participant.stats.armor_pen_percent / 100)) - participant.stats.armor_pen_flat;
        this.stats.real_mr = (this.stats.mr * (1 - participant.stats.magic_pen_percent / 100)) - participant.stats.magic_pen_flat;
        this.stats.real_mr = this.stats.real_mr < 0 ? 0 : this.stats.real_mr;
        this.stats.real_armor = this.stats.real_armor < 0 ? 0 : this.stats.real_armor;
        this.stats.armor_reduction = (100 / (this.stats.real_armor + 100)) * 100;
        this.stats.mr_reduction = (100 / (this.stats.real_mr + 100)) * 100;

        let base_dps_ad = participant.stats.dps_ad;
        //brk
        if (has_brk(items)) {
            if (this.champion) {
                let damage_supp = this.champion.stats.attack_range > 250 ? (this.stats.hp * 0.08) : (this.stats.hp * 0.12);
                damage_supp = damage_supp < 15 ? 15 : damage_supp;
                base_dps_ad += has_guinsoo(items) ? damage_supp * 0.3 : damage_supp;
            }
        }
        // has dominik
        if (has_dominik(items)) {
            let hp_diff = this.stats.hp - participant.stats.hp;
            hp_diff = hp_diff < 0 ? 0 : hp_diff > 2000 ? 2000 : hp_diff;
            let dps_percent = 1 + (hp_diff * 0.0075 / 100) / 100;
            base_dps_ad *= dps_percent;
        }
        this.stats.dps_ad_damage_taken = base_dps_ad * this.stats.armor_reduction / 100;
        this.stats.dps_ap_damage_taken = participant.stats.dps_ap * this.stats.mr_reduction / 100;
        this.stats.dps_true_damage_taken = participant.stats.dps_true;
        this.stats.dps_total_damage_taken = this.stats.dps_ad_damage_taken + this.stats.dps_ap_damage_taken + this.stats.dps_true_damage_taken;
        this.stats.round_all();
    }


}

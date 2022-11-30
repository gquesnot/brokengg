import { round } from "../../util/util";
import { AdaptativeStats } from "../../data/adaptative_stats";
import HpPerk from "../../data/hp_perk";
import { isNumber } from "lodash";
export default class Stats {
    constructor() {
        this.ad = 0;
        this.ap = 0;
        this.armor = 0;
        this.armor_pen_percent = 0;
        this.armor_pen_flat = 0;
        this.as_percent = 0;
        this.base_as = 0;
        this.crit_percent = 0;
        this.hp = 0;
        this.hp_regen_percent = 0;
        this.life_steal_percent = 0;
        this.magic_pen_percent = 0;
        this.magic_pen_flat = 0;
        this.mr = 0;
        this.mp = 0;
        this.mp_regen_percent = 0;
        this.ms = 0;
        this.ms_percent = 0;
        this.omnivamp_percent = 0;
        this.tenacity_percent = 0;
        this.heal_power_percent = 0;
        this.ah = 0;
        this.cdr = 0;
        this.adaptative = new AdaptativeStats();
        this.magic_pen_percent_bonus = 0;
        this.armor_pen_percent_bonus = 0;
        this.as = 0;
        this.dps_ad = 0;
        this.dps_ap = 0;
        this.dps_true = 0;
        this.dps_ad_damage_taken = 0;
        this.dps_ap_damage_taken = 0;
        this.dps_true_damage_taken = 0;
        this.dps_total_damage_taken = 0;
        this.dps_total = 0;
        this.real_armor = 0;
        this.real_mr = 0;
        this.armor_reduction = 0;
        this.mr_reduction = 0;
        this.on_hit_ad = 0;
        this.on_hit_ap = 0;
        this.base_ad = 0;
    }
    reset() {
        this.ad = 0;
        this.base_ad = 0;
        this.ap = 0;
        this.armor = 0;
        this.armor_pen_percent = 0;
        this.magic_pen_percent_bonus = 0;
        this.armor_pen_percent_bonus = 0;
        this.armor_pen_flat = 0;
        this.as_percent = 0;
        this.base_as = 0;
        this.crit_percent = 0;
        this.hp = 0;
        this.hp_regen_percent = 0;
        this.life_steal_percent = 0;
        this.magic_pen_percent = 0;
        this.magic_pen_flat = 0;
        this.mr = 0;
        this.mp = 0;
        this.mp_regen_percent = 0;
        this.ms = 0;
        this.ms_percent = 0;
        this.omnivamp_percent = 0;
        this.tenacity_percent = 0;
        this.heal_power_percent = 0;
        this.adaptative.ad = 0;
        this.adaptative.ap = 0;
        this.on_hit_ad = 0;
        this.on_hit_ap = 0;
        this.as = 0;
        this.dps_ad = 0;
        this.dps_ap = 0;
        this.dps_ad_damage_taken = 0;
        this.dps_ap_damage_taken = 0;
        this.armor_reduction = 0;
        this.dps_true = 0;
        this.dps_total = 0;
        this.dps_true_damage_taken = 0;
        this.dps_total_damage_taken = 0;
        this.mr_reduction = 0;
        this.real_armor = 0;
        this.real_mr = 0;
        this.cdr = 0;
        this.ah = 0;
    }
    add_item(item, nb_legendary = 0) {
        if (item.stats) {
            this.add_item_stats(item.stats);
        }
        if (item.mythic_stats) {
            this.add_item_mythic_stats(item.mythic_stats, nb_legendary);
        }
    }
    add_item_stats(stats) {
        this.ad += stats.ad;
        this.ap += stats.ap;
        this.armor += stats.armor;
        this.armor_pen_percent += stats.armor_pen_percent;
        this.armor_pen_flat += stats.flat_armor_pen;
        this.as_percent += stats.as_percent;
        this.crit_percent += stats.crit_percent;
        this.hp += stats.hp;
        this.hp_regen_percent += stats.hp_regen_percent;
        this.life_steal_percent += stats.life_steal_percent;
        this.magic_pen_percent += stats.magic_pen_percent;
        this.magic_pen_flat += stats.flat_magic_pen;
        this.mr += stats.mr;
        this.mp += stats.mp;
        this.mp_regen_percent += stats.mp_regen_percent;
        this.ms += stats.ms;
        this.ms_percent += stats.ms_percent;
        this.omnivamp_percent += stats.omnivamp_percent;
        this.tenacity_percent += stats.tenacity_percent;
        this.heal_power_percent += stats.heal_power_percent;
        this.ah += stats.ah;
    }
    add_item_mythic_stats(stats, nb_legendary) {
        this.ad += stats.ad * nb_legendary;
        this.ap += stats.ap * nb_legendary;
        this.ah += stats.ah * nb_legendary;
        this.armor += stats.armor * nb_legendary;
        this.armor_pen_percent += stats.armor_pen_percent * nb_legendary;
        this.armor_pen_flat += stats.armor_pen_flat * nb_legendary;
        this.as_percent += stats.as_percent * nb_legendary;
        this.hp += stats.hp * nb_legendary;
        this.magic_pen_percent += stats.magic_pen_percent * nb_legendary;
        this.magic_pen_flat += stats.magic_pen_flat * nb_legendary;
        this.mr += stats.mr * nb_legendary;
        this.ms_percent += stats.ms_percent * nb_legendary;
        this.heal_power_percent += stats.heal_power_percent * nb_legendary;
        this.omnivamp_percent += stats.omnivamp_percent * nb_legendary;
    }
    apply_grow(base, grow, level) {
        level--;
        return base + grow * level * (0.7025 + 0.0175 * level);
    }
    add_champion(champion, level) {
        this.ad = this.apply_grow(champion.stats.ad, champion.stats.ad_per_level, level);
        this.base_ad = this.ad;
        this.hp = this.apply_grow(champion.stats.hp, champion.stats.hp_per_level, level);
        this.armor = this.apply_grow(champion.stats.armor, champion.stats.armor_per_level, level);
        this.mr = this.apply_grow(champion.stats.mr, champion.stats.mr_per_level, level);
        this.base_as = champion.stats.base_as;
        this.as_percent = this.apply_grow(0, champion.stats.as_percent_per_level, level);
    }
    add_frame(frame) {
        this.ad = frame.stats.ad;
        this.ap = frame.stats.ap;
        this.armor = frame.stats.armor;
        this.mr = frame.stats.mr;
        this.armor_pen_percent = frame.stats.armor_pen_percent + frame.stats.armor_pen_percent_bonus;
        this.armor_pen_flat = frame.stats.armor_pen_flat + frame.stats.magic_pen_percent_bonus;
        this.as = frame.stats.as;
        this.ah = frame.stats.ah;
        this.cdr = frame.stats.cdr;
        this.hp = frame.stats.hp;
    }
    add_perks(perks, level) {
        this.add_perk(perks.get_defense(), level);
        this.add_perk(perks.get_offense(), level);
        this.add_perk(perks.get_flex(), level);
    }
    add_perk(perk, level) {
        if (perk.key == "adaptative" && perk.value instanceof AdaptativeStats) {
            this.adaptative = perk.value;
        }
        else if (perk.key == "hp" && perk.value instanceof HpPerk) {
            this.hp += perk.value.base + perk.value.per_level * level;
        }
        else if (isNumber(perk.value)) {
            switch (perk.key) {
                case "armor":
                    this.armor += perk.value;
                    break;
                case "ah":
                    this.ah += perk.value;
                    break;
                case "as":
                    this.as_percent += perk.value;
                    break;
                case "mr":
                    this.mr += perk.value;
            }
        }
    }
    round_all() {
        this.ad = round(this.ad);
        this.ap = round(this.ap);
        this.armor = round(this.armor);
        this.armor_pen_percent = round(this.armor_pen_percent);
        this.armor_pen_flat = round(this.armor_pen_flat);
        this.as_percent = round(this.as_percent);
        this.crit_percent = round(this.crit_percent);
        this.hp = round(this.hp);
        this.hp_regen_percent = round(this.hp_regen_percent);
        this.life_steal_percent = round(this.life_steal_percent);
        this.magic_pen_percent = round(this.magic_pen_percent);
        this.magic_pen_flat = round(this.magic_pen_flat);
        this.mr = round(this.mr);
        this.mp = round(this.mp);
        this.mp_regen_percent = round(this.mp_regen_percent);
        this.ms = round(this.ms);
        this.ms_percent = round(this.ms_percent);
        this.omnivamp_percent = round(this.omnivamp_percent);
        this.tenacity_percent = round(this.tenacity_percent);
        this.heal_power_percent = round(this.heal_power_percent);
        this.ah = round(this.ah);
        this.as = round(this.as, 2);
        this.cdr = round(this.cdr);
        this.dps_ad = round(this.dps_ad);
        this.dps_ap = round(this.dps_ap);
        this.armor_reduction = round(this.armor_reduction);
        this.mr_reduction = round(this.mr_reduction);
        this.crit_percent = round(this.crit_percent);
        this.dps_ad_damage_taken = round(this.dps_ad_damage_taken);
        this.dps_ap_damage_taken = round(this.dps_ap_damage_taken);
        this.real_armor = round(this.real_armor);
        this.real_mr = round(this.real_mr);
        this.dps_true = round(this.dps_true);
        this.dps_true_damage_taken = round(this.dps_true_damage_taken);
        this.dps_total_damage_taken = round(this.dps_total_damage_taken);
        this.dps_total = round(this.dps_total);
        this.on_hit_ap = round(this.on_hit_ap);
        this.on_hit_ad = round(this.on_hit_ad);
    }
    total_magic_pen() {
        return this.magic_pen_flat + ' + ' + this.magic_pen_percent + '% + ' + this.magic_pen_percent_bonus + '%';
    }
    total_armor_pen() {
        return this.armor_pen_flat + ' + ' + this.armor_pen_percent + '% + ' + this.armor_pen_percent_bonus + '%';
    }
}

import { AdaptativeStats } from "./adaptative_stats";
import HpPerk from "./hp_perk";
import { Perk } from "./perk";
export const perks = {
    5002: new Perk('armor', 6),
    5008: new Perk('adaptative', new AdaptativeStats(5.4, 9)),
    5001: new Perk('hp', new HpPerk(7.35, 7.7)),
    5007: new Perk('ah', 8),
    5005: new Perk('as', 10),
    5003: new Perk('mr', 8),
};

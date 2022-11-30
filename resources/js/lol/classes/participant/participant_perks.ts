import {Perk} from "../../data/perk";
import {perks} from "../../data/perks";

export default class ParticipantPerks{
    defense: number = 0;
    offense: number = 0;
    flex: number = 0;

    get_defense(): Perk {
        return perks[this.defense.toString()];
    }

    get_offense(): Perk {
        return perks[this.offense.toString()];
    }

    get_flex(): Perk {
        return perks[this.flex.toString()];
    }
}

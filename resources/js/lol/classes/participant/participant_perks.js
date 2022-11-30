import { perks } from "../../data/perks";
export default class ParticipantPerks {
    constructor() {
        this.defense = 0;
        this.offense = 0;
        this.flex = 0;
    }
    get_defense() {
        return perks[this.defense.toString()];
    }
    get_offense() {
        return perks[this.offense.toString()];
    }
    get_flex() {
        return perks[this.flex.toString()];
    }
}

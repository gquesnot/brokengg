import { ChampionStats } from "./champion_stats";
export class Champion {
    constructor() {
        this.id = 0;
        this.img_url = "";
        this.name = "";
        this.stats = new ChampionStats();
    }
}

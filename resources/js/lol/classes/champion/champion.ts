import {ChampionStats} from "./champion_stats";

export  interface  ChampionInterface {
    id:  number ;
    name:  string ;
    img_url:  string ;
    stats: ChampionStats;
}


export class Champion implements ChampionInterface{
    id: number = 0;
    img_url: string = "";
    name: string = "";
    stats: ChampionStats = new ChampionStats();
}

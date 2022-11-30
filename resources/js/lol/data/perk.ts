
import HpPerk from "./hp_perk";
import {AdaptativeStats} from "./adaptative_stats";


export class Perk{
    key: string;
    value: number | AdaptativeStats | HpPerk;

    public constructor(key: string, value: number | AdaptativeStats | HpPerk){
        this.key = key;
        this.value = value;
    }
}

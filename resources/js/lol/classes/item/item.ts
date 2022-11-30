import ItemStats from "./ItemStats";
import ItemMythicStats from "./ItemMythicStats";

export default interface Item {
    id: number;
    name: string;
    description: string;
    tags: string[];
    stats:ItemStats;
    mythic_stats: ItemMythicStats| null;
    gold: number;
    img_url: string;
    type: string;
    colloq: string;

}

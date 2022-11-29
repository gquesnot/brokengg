import { ItemCategory } from "./data/itemsCategory";
export default class Lol {
    itemsCategory: ItemCategory[];
    version: string;
    timeline: [];
    category: number;
    total_gold: number;
    current_gold: number;
    match: object;
    frame_id: number;
    max_frame: number;
    participant_id: number;
    open_modal: boolean;
    toggle_change_items: boolean;
    items: [];
    modified_items: [];
    constructor(match: any, timeline: any, items: any, version: any);
}
//# sourceMappingURL=lol.d.ts.map
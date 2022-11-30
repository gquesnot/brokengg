import ItemCategory from "./data/item_category";
import Item from "./classes/item/item";
import Participant from "./classes/participant/participant";
import ItemsController from "./classes/items_controller";

interface LolInterface  {
    items_categories: ItemCategory[];
    version: string;
    category: number;
    total_gold: number;
    current_gold: number;
    frame_id: number;
    max_frame: number;
    participant_id: number;
    open_modal: boolean;
    toggle_change_items: boolean;
    all_items:  Record<string, Item>;
    modified_items: Item[];
    items: Item[];

    participants: Participant[];
    enemy_participants: Participant[];
    participant: Participant;

    items_controller: ItemsController;
}

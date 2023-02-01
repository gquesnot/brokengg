import Lol from "../lol";
import Item from "./item/item";
import ShopEvent from "./participant/frame/shop_event";
import Participant from "./participant/participant";

export default class ItemsController {
    items: number[] = [];
    events: ShopEvent[] = [];
    base_items :  Record<string, Item> = {};


    constructor(items:  Record<string, Item>) {
        this.base_items = items;
    }


    items_from_list(): Item[]{
        let items: Item[] = [];
        this.items.forEach((item_id) => {
            let item = this.base_items[item_id.toString()];
            if (item) {
                items.push(item);
            }
        });
        return items;
    }

    update_items(participant:Participant, frame_id: number) {
        this.items = [];
        this.events = [];
        let del_items: number[] = [];

        participant.frames.forEach((frame, index) => {
           if (index <= frame_id) {
               this.events = this.events.concat(frame.events);
           }
        });

        // merge all events
        let add_items_event = this.events.filter((event) => event.type === "ITEM_PURCHASED" || (event.type === "ITEM_UNDO" && event.after_id != 0));
        let remove_items_event = this.events.filter((event) => event.type !== "ITEM_PURCHASED");
        add_items_event.forEach((event) => {
            let item_id =  event.type === "ITEM_PURCHASED" ? event.item_id : event.after_id;
            if (item_id) {
                let item = this.base_items[item_id.toString()];
                if (item) {
                    this.items.push(item_id);
                }
            }
        });

        // remove items
        remove_items_event.forEach((event) => {
            let item_id = event.type === "ITEM_DESTROYED" || event.type === "ITEM_SOLD" ? event.item_id : event.before_id;
            if (item_id) {
                let item = this.base_items[item_id.toString()];
                if (item) {
                    del_items.push(item_id);
                }
            }
        });

        // diff
        while (del_items.length > 0) {
            let del_item = del_items.pop();
            if (del_item){
                let found = this.items.indexOf(del_item);
                if (found !== -1) {
                    this.items.splice(found, 1);
                }
            }
        }
    }

}

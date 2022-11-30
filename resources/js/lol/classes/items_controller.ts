import Lol from "../lol";
import Item from "./item/item";
import ShopEvent from "./participant/frame/shop_event";
import Participant from "./participant/participant";

export default class ItemsController {
    items: number[] = [];
    to_buy: number[] = [];
    gold_diff: number = 0;
    events: ShopEvent[][] = [];
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
        this.to_buy = [];
        this.gold_diff = 0;
        this.events = [];
        for (let i = 0; i <= frame_id; i++) {
            let frame = participant.frames[i];
            this.add_events(frame.events);

        }
    }

    add_events(events: ShopEvent[]) {
        let has_undo = events.some((event) => event.type === "ITEM_UNDO");
        let only_destroyed = events.every((event) => event.type === "ITEM_DESTROYED");
        if (!has_undo && !only_destroyed) {
            events.forEach((event) => {
                if (event.type === "ITEM_PURCHASED") {
                    this.apply_item_purchased(event);
                } else if (event.type === "ITEM_SOLD" || event.type === "ITEM_DESTROYED") {
                    this.apply_item_destroyed(event);
                } else if (event.type === "ITEM_UNDO") {
                    this.apply_item_undo(event);
                }
            });
            this.events.push(events);
        } else if (has_undo && !only_destroyed) {
            this.restore_items(events);
        }
        if (!only_destroyed) {
            this.events.push(events);
        }


    }


    private apply_item_undo(event: ShopEvent) {
        let item_id = event.before_id;
        if (!item_id) {
            return;
        }
        let item = this.base_items[event.item_id.toString()];
        if (item) {
            let found = this.items.indexOf(event.item_id);
            if (found !== -1) {
                this.items.splice(found, 1);
            } else {
                this.to_buy.push(event.item_id);
            }
            if (event.after_id) {
                let new_event = new ShopEvent(
                    "ITEM_PURCHASED",
                    event.after_id,
                    event.timestamp,
                    event.participant_id
                );
                this.apply_item_purchased(new_event);
            }
        }
    }

    private apply_item_destroyed(event: ShopEvent) {
        let item = this.base_items[event.item_id.toString()];
        if (item) {
            let found = this.items.indexOf(event.item_id);
            if (found !== -1) {
                this.items.splice(found, 1);
            } else {
                this.to_buy.push(event.item_id);
            }
        }
    }

    private apply_item_purchased(event: ShopEvent) {
        let item = this.base_items[event.item_id.toString()];
        if (item) {
            if (!this.to_buy.includes(event.item_id)) {
                this.items.push(event.item_id);
            } else {
                this.to_buy.splice(this.to_buy.indexOf(event.item_id), 1);
            }
        }
    }

    private restore_items(events: ShopEvent[]) {
        events.forEach((event) => {
            if (event.type === "ITEM_UNDO") {
                for (let i = this.events.length - 1; i > 0; i--) {
                    let events = this.events[i];
                    let founds = events.filter((e) => {
                        return e.item_id === event.before_id && e.type === "ITEM_PURCHASED"
                    });
                    if (founds.length > 0) {
                        founds.forEach((found) => {
                            if (found.type == "ITEM_DESTROYED") {
                                this.apply_item_destroyed(found);
                            }
                            else if (found.type == "ITEM_PURCHASED") {
                                this.apply_item_purchased(found);
                            }
                        });
                    }
                }
            }
        });
    }
}

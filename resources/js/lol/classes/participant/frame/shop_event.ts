export default class ShopEvent{
    type: string;
    timestamp: number;
    item_id: number;
    participant_id: number;
    gold_gain: number|null;
    after_id: number|null;
    before_id: number|null;

    constructor(type: string, timestamp: number, item_id: number, participant_id: number, after_id: number|null=null, before_id: number|null=null, gold_gain: number|null=null){
        this.type = type;
        this.timestamp = timestamp;
        this.item_id = item_id;
        this.participant_id = participant_id;
        this.after_id = after_id;
        this.before_id = before_id;
        this.gold_gain = gold_gain;
    }
}

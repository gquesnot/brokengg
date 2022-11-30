export default class ShopEvent {
    constructor(type, timestamp, item_id, participant_id, after_id = null, before_id = null, gold_gain = null) {
        this.type = type;
        this.timestamp = timestamp;
        this.item_id = item_id;
        this.participant_id = participant_id;
        this.after_id = after_id;
        this.before_id = before_id;
        this.gold_gain = gold_gain;
    }
}

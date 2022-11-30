import ParticipantFrameStats from "./participant_frame_stats";
export class Frame {
    constructor() {
        this.current_gold = 0;
        this.events = [];
        this.level = 0;
        this.stats = new ParticipantFrameStats();
        this.total_gold = 0;
    }
}

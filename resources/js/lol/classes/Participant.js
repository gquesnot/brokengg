import Stats from "./stats";
export default class Participant {
    constructor() {
        this.frames = [];
        this.current_frame = null;
        this.id = 0;
        this.name = "";
        this.items = [];
        this.champion = null;
        this.won = true;
        this.profile_icon_id = 0;
        this.puuid = "";
        this.stats = new Stats();
    }
    add_champion_stats(frame_id) {
        let frame = this.frames[frame_id];
        if (this.champion !== null) {
            this.stats.add_champion(this.champion, frame.level);
        }
    }
    set_stats_from_frame(frame_id) {
        this.stats.add_frame(this.frames[frame_id]);
    }
    select_participant_frame(frame_id) {
        this.current_frame = this.frames[frame_id];
    }
}

import Frame from "./frame";
import Champion from "./champion";
import Stats from "./stats";

export default class Participant {
    frames: Frame[] = [];
    current_frame : Frame|null = null;
    id: number = 0;
    name: string = "";
    items: number[] = [];
    champion: Champion|null = null;
    won: boolean = true;
    profile_icon_id: number = 0;
    puuid: string = "";

    stats: Stats = new Stats();


    add_champion_stats(frame_id: number) {
        let frame : Frame = this.frames[frame_id];
        if (this.champion !== null) {
            this.stats.add_champion(this.champion, frame.level);
        }
    }

    set_stats_from_frame(frame_id: number) {
        this.stats.add_frame(this.frames[frame_id]);
    }

    select_participant_frame(frame_id: number) {
        this.current_frame = this.frames[frame_id];
    }
}

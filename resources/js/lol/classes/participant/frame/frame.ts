import ShopEvent from "./shop_event";
import ParticipantFrameStats from "./participant_frame_stats";

export interface FrameInterface{
    events: ShopEvent[];
    stats: ParticipantFrameStats;
    total_gold: number;
    current_gold: number;
    level: number;

}
export class Frame implements FrameInterface {
    current_gold: number = 0;
    events: ShopEvent[] = [];
    level: number = 0;
    stats: ParticipantFrameStats = new ParticipantFrameStats();
    total_gold: number = 0;


}

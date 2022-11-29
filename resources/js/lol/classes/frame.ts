import ShopEvent from "./shop_event";
import ParticipantFrameStats from "./participant_frame_stats";

export default interface Frame{
    events: ShopEvent[];
    stats: ParticipantFrameStats;
    total_gold: number;
    current_gold: number;
    level: number;

}

import {Alpine} from "alpinejs";
import LolInterface from "../lol/lol_interface";

declare global {
    interface Window {
        Alpine:Alpine
        Lol: LolInterface
    }
}

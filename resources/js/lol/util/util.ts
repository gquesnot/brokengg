import Item from "../classes/item/item";

export function round(value: number, decimal = 0) {
    return decimal === 0 ? Math.round(value) : Math.round(value * 10 * decimal) / (10 * decimal);
}

export function is_guinsoo(item_id: number) {
    return item_id === 3124;
}

export function has_guinsoo(items: Item[]): boolean {
    return items.some((item) => {
        return is_guinsoo(item.id);
    });
}

export function is_ie(item_id: number) {
    return item_id === 3031;
}

export function has_ie(items: Item[], crit_percent: number = -1): boolean {
    return items.some((item) => {
        if (crit_percent === -1) {
            return is_ie(item.id);
        }
        return is_ie(item.id) && crit_percent > 0.6;
    })
}

export function is_brk(item_id: number) {
    return item_id === 3153;
}

export function has_brk(items: Item[]): boolean {
    return items.some((item) => {
        return is_brk(item.id);
    });
}

export function is_dominik(item_id: number) {
    return item_id === 3036;
}

export function has_dominik(items: Item[]): boolean {
    return items.some((item) => {
        return is_dominik(item.id);
    });
}

export function is_rageknife(item_id: number) {
    return item_id === 6677;
}

export function has_nashor(items: Item[]): boolean {
    return items.some((item) => {
        return is_nashor(item.id)
    });
}

export function is_witsend(item_id: number) {
    return item_id === 3091;
}

export function has_witsend(items: Item[]): boolean {
    return items.some((item) => {
        return is_witsend(item.id)
    });
}

export function is_nashor(item_id: number) {
    return item_id === 3115;
}

export function has_rageknife(items: Item[]): boolean {
    return items.some((item) => {
        return is_rageknife(item.id)
    });
}

export function is_rabadon(item_id: number) {
    return item_id === 3089;
}

export function has_rabadon(items: Item[]) {
    return items.some((item: Item) => {
        return is_rabadon(item.id);
    });
}

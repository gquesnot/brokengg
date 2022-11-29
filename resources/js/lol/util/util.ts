export function round(value:number, decimal = 0) {
    return decimal === 0 ? Math.round(value) : Math.round(value * 10 * decimal) / (10 * decimal);
}


export function is_guinsoo(item_id: number) {
    return item_id === 3124;
}

export function is_ie(item_id: number) {
    return item_id === 3031;
}

export function is_brk(item_id: number) {
    return item_id === 3153;
}

export function is_dominik(item_id: number) {
    return item_id === 3036;
}

export function is_rageknife(item_id: number) {
    return item_id === 6677;
}

export function is_witsend(item_id: number) {
    return item_id === 3091;
}

export function is_nashor(item_id: number) {
    return item_id === 3115;
}

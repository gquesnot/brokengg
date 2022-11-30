export function round(value, decimal = 0) {
    return decimal === 0 ? Math.round(value) : Math.round(value * 10 * decimal) / (10 * decimal);
}
export function is_guinsoo(item_id) {
    return item_id === 3124;
}
export function is_ie(item_id) {
    return item_id === 3031;
}
export function is_brk(item_id) {
    return item_id === 3153;
}
export function is_dominik(item_id) {
    return item_id === 3036;
}
export function is_rageknife(item_id) {
    return item_id === 6677;
}
export function is_witsend(item_id) {
    return item_id === 3091;
}
export function is_nashor(item_id) {
    return item_id === 3115;
}
export function has_guinsoo(items) {
    return items.some((item) => {
        return is_guinsoo(item.id);
    });
}
export function has_ie(items, crit_percent = -1) {
    return items.some((item) => {
        if (crit_percent === -1) {
            return is_ie(item.id);
        }
        return is_ie(item.id) && crit_percent > 0.6;
    });
}
export function has_brk(items) {
    return items.some((item) => {
        return is_brk(item.id);
    });
}
export function has_dominik(items) {
    return items.some((item) => {
        return is_dominik(item.id);
    });
}
export function has_nashor(items) {
    return items.some((item) => {
        return is_nashor(item.id);
    });
}
export function has_witsend(items) {
    return items.some((item) => {
        return is_witsend(item.id);
    });
}
export function has_rageknife(items) {
    return items.some((item) => {
        return is_rageknife(item.id);
    });
}

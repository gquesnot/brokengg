export function round(value:number, decimal = 0) {
    return decimal === 0 ? Math.round(value) : Math.round(value * 10 * decimal) / (10 * decimal);
}

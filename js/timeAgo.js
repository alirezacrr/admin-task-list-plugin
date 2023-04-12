function ago(val) {
    val = 0 | (Date.now() - val) / 1000;
    var unit, length = {
        ثانیه: 60, دقیقه: 60, ساعت: 24, روز: 7, هفته: 4.35,
        ماه: 12, سال: 10000
    }, result;

    for (unit in length) {
        result = val % length[unit];
        if (!(val = 0 | val / length[unit]))
            return result + ' ' + (result - 1 ? unit : unit) + ' ' + 'پیش';
    }
}
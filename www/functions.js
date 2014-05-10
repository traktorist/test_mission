// проверяет json-строку на валидность
function IsJsonString(str) {
    try {
        JSON.parse(str);
    }
    catch (e) {
        return false;
    }

    return true;
}

// получает счёт игроков
function get_users() {
    $.post('/handler.php', {
        action: 'get_users'
    }).done(function(response) {
            if (!IsJsonString(response)) {                      // если нет данных из БД или есть проблемы при их получении
                alert('Fail get_users');                        // говорим, что всё плохо
                return;                                         // и выходим из функции
            }
            else {
                users = $.parseJSON(response);
                for (var key in users) {
                    $('#users select').append( $('<option value="' + key + '">' + users[key] + '</option>'));
                }

                window.wait_timer = setInterval(function() {
                    wait_opponent($('#users select :selected').val())
                }, 300);
            }
        });
}

// получает счёт игроков
function get_score() {
    $.post('/handler.php', {
        action: 'get_score'
    }).done(function(response) {
            if (!IsJsonString(response)) {                      // если нет данных из БД или есть проблемы при их получении
                alert('Fail get_score');                        // говорим, что всё плохо
                return;                                         // и выходим из функции
            }
            else {
                score = $.parseJSON(response);
                $('#score p').html(score);
            }
        });
}

// отправляет id игрока и выбранное оружие
function throw_hand(id_user, weapon) {
    $.post('/handler.php', {
        action: 'throw_hand',
        id_user: id_user,
        weapon: weapon
    }).done(function(response) {
            if (!IsJsonString(response)) {                      // если нет данных из БД или есть проблемы при их получении
                alert('Fail throw_hand');                       // говорим, что всё плохо
                return;                                         // и выходим из функции
            }
            else {
                result_fight = $.parseJSON(response);
                if (result_fight != '')
                    $('#game p').html(result_fight);
            }
        });
}

// получает результат игры (по таймеру)
function wait_opponent(user_id) {
    $.post('/handler.php', {
        action: 'wait_opponent',
        user_id: user_id
    }).done(function(response) {
            if (!IsJsonString(response)) {                      // если нет данных из БД или есть проблемы при их получении
                alert('Fail wait_opponent');                    // говорим, что всё плохо
                return;                                         // и выходим из функции
            }
            else {
                result_fight = $.parseJSON(response);
                if (result_fight) {
                    $('#game p').html(result_fight);
                    get_score();
                }
            }
        });
}

// очищает результаты всех игр чуть реже, чем опрашивают их игроки - нужно для сброса игры, если второй игрок отсутствует или не посмотрел результат
function clear_temp() {
    $.post('/handler.php', {
        action: 'clear_temp'
    });
}

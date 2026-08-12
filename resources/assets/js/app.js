/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('selectize');
window.Vue = require('vue');
Vue.component('customer-settings', require('./components/CustomerSettings.vue'));
Vue.component('edit-map', require('./components/edit/map.vue'));
window.vueEventBus = new Vue();
window.makeMarkerId = function (lat, lon) {
    let markerId = '' + lat + '' + lon;
    return markerId.replace(/\./g, '');
};
var prevScrollpos = window.pageYOffset;
window.onscroll = function () {
    var currentScrollPos = window.pageYOffset;
    if (prevScrollpos > currentScrollPos) {
        document.getElementById("navbar").style.top = "0";
    } else {
        document.getElementById("navbar").style.top = "-" + ($('#navbar').height() + 16) + "px";
    }
    prevScrollpos = currentScrollPos;
}
window.selectizeRemove = function (jqueryElement, value) {
    vueEventBus.$emit('selectizeRemove', {
        jqueryElement,
        value
    })
};
window.zoomToStreet = function (streetName) {
    vueEventBus.$emit('zoomToStreet', streetName);
};
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('example-component', require('./components/ExampleComponent.vue'));
//
// const app = new Vue({
//     el: '#app'
// });

$(document).ready(function () {
    $("#addClass").click(function () {
        $('#qnimate').addClass('popup-box-on');
    });

    $("#removeClass").click(function () {
        $('#qnimate').removeClass('popup-box-on');
    });

    $('.fff .btn').click(function () {
        subscribe('fff');
    });
    $('.xxxx .btn').click(function () {
        subscribe('xxxx');
    });
});

window.ajaxRequest = function (method, params) {
    console.log('Ajax request: ' + method, params);
    var deferred = $.Deferred();

    jsonRequest('ajax/' + method, params).done(function (result) {
        console.log('ajax result', result);
        deferred.resolve(result);
    });
    return deferred.promise();
};
window.jsonRequest = function (url, params) {
    var deferred = $.Deferred();
    //
    url = homelink + '/' + url;
    $.ajax({
        type: 'POST',
        url: url,
        data: params,
        headers: {
            'X-CSRF-TOKEN': Laravel.csrfToken,
        },
        dataType: 'json',
        success: function (msg) {
            if (!_.isEmpty(msg.error)) {
                if (msg.error.code === 2000) {
                    let messages = '';
                    for (var i in msg.error.message) {
                        messages += msg.error.message[i] + '\n';
                    }
                    alert(messages, 'Chyba');
                    return;
                }
                alert(msg.error.message);
                return;
            }
            deferred.resolve(msg.result);
        },
        error: function (msg) {
            if (msg.status === 0 || msg.readyState === 0) {
                // endloading();
                return;
            }
            alert(JSON.stringify(msg));
            // endloading();
        },
    });
    return deferred.promise();
};

window.toastTimeout = null;
window.toast = function (text, time, colorClass) {
    if (_.isEmpty(time)) {
        time = 2000;
    }
    if (_.isEmpty(colorClass)) {
        colorClass = 'alert-success';
    }
    clearTimeout(toastTimeout);
    $('body').append('<div class=\'toast alert ' + colorClass +
        ' alert-dismissable\'>' +
        text + '</div>');
    toastTimeout = setTimeout(function () {
        $('.toast').remove();
    }, time);
};
window.panTimeout = null;
window.addParcelNumber = function (number, typ, cadasterNumber) {
    number = number.replace(/[^0-9\/\-]+/g, "");
    console.log('addParcelNumberaddParcelNumber');
    number = typ + ' ' + number;
    vueEventBus.$emit('addParcelNumber', {number, cadasterNumber});
}
window.drawBorderAroundSelection = function () {
    var selection = window.getSelection(), // get the selection then
        range = selection.getRangeAt(0), // the range at first selection group
        rect = range.getBoundingClientRect(); // and convert this to useful data
    $('#selection-popup').remove();
    if (rect.width > 0) {
        let div = document.createElement('div'); // make box
        div.class = 'selection-popup';
        div.id = 'selection-popup';
        div.style.top = (rect.top + 15) + 'px'; // set coordinates
        div.style.left = rect.left + 'px';
        let buttonHtml = '<button class=\'btn btn-info\' onclick=\'addParcelNumber($("#select-parcel").val(), "C", $("#select-cadaster").val())\'>typ C</button>' +
            '<button class=\'btn btn-info\' onclick=\'addParcelNumber($("#select-parcel").val(), "E", $("#select-cadaster").val())\'>typ E</button>' +
            '<select id=\'select-cadaster\'><option value=""></option>';
        for (let number in cadasters) {
            buttonHtml += "<option value='" + number + "'>" + cadasters[number] + "</option>";
        }
        buttonHtml += '</select>' +
            '<input id=\'select-parcel\' class="selectize-input" value=\'' +
            selection.toString() + '\'>';
        div.innerHTML = buttonHtml;
        document.body.appendChild(div); // finally append
    }
};
window.loadStreetNumbersSelectize = function (street) {
    console.log('loadStreetNumbersSelectize');
    vueEventBus.$emit('loadStreetNumbersSelectize', street);
};
window.setMarkerFromAddressEdit = function () {
    let numberId = $('#number').val();
    console.log(numberId, $('#number' + numberId));
    let lat = $('#number' + numberId).attr('latitude');
    let lng = $('#number' + numberId).attr('longitude');
    console.log(lat, lng);
    addMarkerToMap(lat, lng);
    map.panTo(new L.LatLng(lat, lng));
};
window.panToBoard = function (boardId) {
    panToMarker(window['marker' + boardId]);
};
window.shareFb = function (url) {
    FB.ui({
        method: 'share',
        href: url,
    }, function (response) {
        console.log('FB response', response);
    });
};
window.loadMarkerPopup = function (marker) {
    let content = marker.getPopup().getContent();
    if (!content.match(/[a-zA-Z]/)) {
        marker.getPopup().setContent('načítavam...');
        ajaxRequest('LoadPopup', {id: content}).then(result => {
            marker.getPopup().setContent(result);
            if (!marker.isPopupOpen()) {
                marker.openPopup();
            }
        });
    }
}
window.panToMarker = function (marker) {
    map.flyTo(marker.getLatLng(), 17, {
        animate: true,
        duration: 0.5
    });
    setTimeout(() => {
        marker.openPopup()
    }, 700);
};

window.submitFeedback = function (feedback) {
    $('#feedback').val(feedback);
    $('#save-feedback-form').submit();
}

window.help = function (boardId) {
    let message = prompt('Zadajte správu');
    if (message === null) {
        return;
    }
    if (message === '') {
        alert('Zadajte správu');
        return;
    }
    ajaxRequest('Help', {
        board_id: boardId,
        message: message,
    }).done(function () {
        toast('Správa bola odoslaná');
    })
};

window.setCookie = function (cname, cvalue, exdays) {
    let d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
window.getCookie = function (cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
window.subscribe = function (el) {
    $('.must-agree').css('background', 'inherit');
    let t = $('.' + el + ' input[name=t]').val();
    let x = $('.' + el + ' input[name=x]').val();
    let e = $('.' + el + ' input[type=email]').val();
    let a = parseInt($('.' + el + ' input[name=agree]:checked').val());
    console.log(a);
    if (a !== 1) {
        $('.must-agree').css('background', 'salmon');
        return;
    }
    $.ajax({
        type: 'POST',
        url: '/sss',
        data: {
            t,
            x,
            email: e,
            agree: a
        },
        headers: {
            'X-CSRF-TOKEN': Laravel.csrfToken,
        },
        success: function (msg) {
            console.log('msg', msg);
            if (msg) {
                window.location = '/' + msg;
            }
        }
    });
}
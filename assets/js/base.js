// baseurl = 'https://zesttourcrm.in/stagging/api/';
// baseurl1 = 'https://zesttourcrm.in/stagging/';
// newbaseurl = 'https://zesttourcrm.in/stagging/newapi/index.php/';

// baseurl = 'https://zesttourcrm.in/stagging/api/';
// baseurl1 = 'https://zesttourcrm.in/stagging/';
// newbaseurl = 'https://zesttourcrm.in/stagging/newapi/index.php/';
baseurl = 'http://localhost/zest_tour_crm/stagging/api/';
baseurl1 = 'http://localhost/zest_tour_crm/stagging/';
newbaseurl = 'http://localhost/zest_tour_crm/stagging/newapi/index.php/';


// baseurl = 'http://localhost/zest_crm/stagging/api/';
// baseurl1 = 'http://localhost/zest_crm/stagging/';
// //newbaseurl = 'http://localhost/crm/stagging/';
// newbaseurl = 'http://localhost/zest_crm/stagging/newapi/index.php/';

//newbaseurl = 'http://localhost/myproject/zest/zestenquiry/newapi/index.php/';
//baseurl = 'http://localhost/myproject/zest/zestenquiry/api/';
//baseurl1 = 'http://localhost/myprojects/zest/zestenquiry/';



setTimeout(function(){$('#find').val('')},500)
var url = window.location.pathname;
var filename = url.substring(url.lastIndexOf('/')+1);
                
$(".goBack").click(function() {
    window.history.back();
});
$(function() {
        $('[data-toggle="tooltip"]').tooltip()
    })
$(".clear-input").click(function() {
    $(this).parent(".input-wrapper").find(".form-control").focus();
    $(this).parent(".input-wrapper").find(".form-control").val("");
    $(this).parent(".input-wrapper").removeClass("not-empty");
});
// active
$(".form-group .form-control").focus(function() {
        $(this).parent(".input-wrapper").addClass("active");
    }).blur(function() {
        $(this).parent(".input-wrapper").removeClass("active");
    })
    // empty check
$(".form-group .form-control").keyup(function() {
    var inputCheck = $(this).val().length;
    if (inputCheck > 0) {
        $(this).parent(".input-wrapper").addClass("not-empty");
    } else {
        $(this).parent(".input-wrapper").removeClass("not-empty");
    }
});
///////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////
// Searchbox Toggle
$(".toggle-searchbox").click(function() {
    $("#search").fadeToggle(200);
    $("#search .form-control").focus();
});
///////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////
// Owl Carousel
// $('.carousel-full').owlCarousel({
//     loop: true,
//     margin: 8,
//     nav: false,
//     items: 1,
//     dots: false,
// });
// $('.carousel-single').owlCarousel({
//     stagePadding: 30,
//     loop: true,
//     margin: 16,
//     nav: false,
//     items: 1,
//     dots: false,
// });
// $('.carousel-multiple').owlCarousel({
//     stagePadding: 32,
//     loop: true,
//     margin: 16,
//     nav: false,
//     items: 2,
//     dots: false,
// });
// $('.carousel-small').owlCarousel({
//     stagePadding: 32,
//     loop: true,
//     margin: 8,
//     nav: false,
//     items: 4,
//     dots: false,
// });
// $('.carousel-slider').owlCarousel({
//     loop: true,
//     margin: 8,
//     nav: false,
//     items: 1,
//     dots: true,
// });


$(document).on('show.bs.modal.show', '.modal', function(event) {
    if ($('.modal.show').length > 1) {
        var zIndex = 1060 + (10 * $('.modal.show').length);
        $(this).css('z-index', zIndex);
        setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    } else if ($('.modal.show').length == 1) {
        var zIndex = 1060 + (10 * $('.modal.show').length);
        $(this).css('z-index', zIndex);
        setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    } else {
        var zIndex = 1060 - (10 * $('.modal.show').length);

        $('.modal.show').css('z-index', zIndex);
        setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    }
});
///////////////////////////////////////////////////////////////////////////


function show_toast(message, type, not_type, pos = 'top', bg = 'inverse') {

    var random = Math.floor(Math.random() * 10);
    if (type == 'danger') {
        $text_type = 'name="close-circle-outline" class="text-danger"'
    } else {
        $text_type = 'name="checkmark-circle" class="text-success"';
    }
    if (not_type == 'big') {
        $('body').append('<div id="toast' + random + '" class="toast-box text-center toast-' + pos + ' show"><div class="in"><ion-icon ' + $text_type + '></ion-icon><div class="text">' + message + '</div></div></div>')
    } else {
        $('body').append('<div id="toast' + random + '" class="toast-box bg-' + bg + ' text-center toast-' + pos + ' show"><div class="in w-100 p-0"><div class="text">' + message + '</div></div></div>')
    }
    setTimeout(() => {
        $('#toast' + random).removeClass('show');
        $('#toast' + random).remove();
    }, 3000);
    setTimeout(() => {
        $('#toast' + random).remove();
    }, 3500);
}

function validate(form_id, fun, parameter = false) {
    var error = 0
    $(form_id + ' .input-danger').remove()
    $(form_id + ' input:visible,' + form_id + ' textarea:visible,' + form_id + ' select:visible').each(function() {
        $(this).parent().parent().find('.input-danger').remove()
        if($(this).parent().parent().attr('data-total-count') !== undefined)
        {
            counts=0
            $(this).parent().parent().find('input[type=text]').each(function(){
                if($(this).val()=='')
                {
                    vals=0
                }
                else
                {
                    vals=$(this).val()
                }
                counts+=parseFloat(vals)
            })
            if(counts>$(this).parent().parent().attr('data-total-count'))
            {
                $(this).parent().parent().append('<div class="input-danger">Total Calculation should be <= '+$(this).parent().parent().attr('data-total-count')+'</div>')
                $(this).parent().parent().addClass('text-danger')
                error++      
            }
        }
        else if ($(this).attr("data-required") == 'yes' && $(this).val() == null) {
            $(this).parent().parent().append('<div class="input-danger">' + $(this).attr('data-validation-required-message') + '</div>')
            $(this).parent().parent().addClass('text-danger')
            error++
        } else if ($(this).attr("data-required") == 'yes' && $(this).val() == '') {
            $(this).parent().parent().append('<div class="input-danger">' + $(this).attr('data-validation-required-message') + '</div>')
            $(this).parent().parent().addClass('text-danger')
            error++
        } else if ($(this).attr("data-pattern") !== undefined && !$(this).val().trim().match($(this).attr("data-pattern")) && $(this).val().trim() != '') {
            $(this).parent().parent().append('<div class="input-danger">' + $(this).attr('data-validation-message') + '</div>')
            $(this).parent().parent().addClass('text-danger')
            error++
        } else if ($(this).attr("data-match") !== undefined && $(this).val().trim() != $('#' + $(this).attr("data-match")).val().trim() && $(this).val().trim() != '') {
            $(this).parent().parent().append('<div class="input-danger">' + $(this).attr('data-validation-message') + '</div>')
            $(this).parent().parent().addClass('text-danger')
            error++
        } else if ($(this).attr("minlength") !== undefined) 
        {
            if($(this).attr('minlength') > $(this).val().length && $(this).val() != '')
            {
                $(this).parent().parent().append('<div class="input-danger"> Minimum length ' + $(this).attr('minlength') + '</div>')
                $(this).parent().parent().addClass('text-danger')
                error++
            }
        } else if ($(this).attr("data-unique-fun") !== undefined && $(this).val().trim() != '') {
            unique_fun = $(this).attr("data-unique-fun");
            request = $(this).attr("data-request");
            form = $(this).attr("data-form");
            $(this).parent().parent().parent().find('.error').html('')

            var fun_error = window[unique_fun]($(this), request, form, false);

            if (typeof fun_error != 'undefined' && fun_error != 'ok') {
                if ($(this).attr("data-show-toast") !== undefined) {
                    $(this).parent().parent().parent().find('.error').html('')
                    $(this).parent().parent().parent().find('.error').html(fun_error)
                } else {
                    $(this).parent().parent().parent().find('.error').html('')
                    $(this).parent().parent().append('<div class="input-danger">' + fun_error + '</div>')
                    $(this).parent().parent().addClass('text-danger')
                }
                error++
            } else {
                $(this).siblings('.help-block').remove()
                $(this).parent().parent().removeClass('text-danger')
            }

        } else {
            $(this).siblings('.help-block').remove()
            $(this).parent().parent().removeClass('text-danger')
        }
    })
    if (error == 0) {
        if (fun != '') {
            if (parameter == false) {
                window[fun]()
            } else {
                window[fun](parameter)
            }
        }
    }
}

validate_onupdate()


function validate_onupdate() {
    var error = 0;
    $('input,textarea').keyup(function() {
    
        $(this).parent().parent().find('.input-danger').remove()
        $(this).parent().parent().removeClass('text-danger')
        $(this).parent().parent().find('.input-danger').remove()
        $(this).parent().parent().removeClass('text-danger')      
        if($(this).parent().parent().attr('data-total-count') !== undefined)
        {
            counts=0
            $(this).parent().parent().find('input[type=text]').each(function(){
                if($(this).val()=='')
                {
                    vals=0
                }
                else
                {
                    vals=$(this).val()
                }
                counts+=parseFloat(vals)
            })
                
            if(counts>$(this).parent().parent().attr('data-total-count'))
            {
                $(this).parent().parent().append('<div class="input-danger">Total Calculation should be <= '+$(this).parent().parent().attr('data-total-count')+'</div>')
                $(this).parent().parent().addClass('text-danger')
                error++      
            }
        }
        else if ($(this).attr("data-required") == 'yes' && $(this).val().trim() == '') {
            $(this).parent().parent().append('<div class="input-danger">' + $(this).attr('data-validation-required-message') + '</div>')
            $(this).parent().parent().addClass('text-danger')
            error++
        } else if ($(this).attr("data-pattern") !== undefined && !$(this).val().trim().match($(this).attr("data-pattern")) && $(this).val().trim() != '') {
            $(this).parent().parent().append('<div class="input-danger">' + $(this).attr('data-validation-message') + '</div>')
            $(this).parent().parent().addClass('text-danger')
            error++
        } else if ($(this).attr("data-match") !== undefined && $(this).val().trim() != $('#' + $(this).attr("data-match")).val().trim() && $(this).val().trim() != '') {
            $(this).parent().parent().append('<div class="input-danger">' + $(this).attr('data-validation-message') + '</div>')
            $(this).parent().parent().addClass('text-danger')
            error++
        } else if ($(this).attr("data-unique-fun") !== undefined && $(this).val().trim() != '') {
            unique_fun = $(this).attr("data-unique-fun");
            request = $(this).attr("data-request");
            form = $(this).attr("data-form");
            var fun_error = window[unique_fun]($(this), request, form, true);

            $(this).parent().parent().parent().find('.error').html('')
            if (typeof fun_error != 'undefined' && fun_error != 'ok') {
                if ($(this).attr("data-show-toast") !== undefined) {
                    $(this).parent().parent().parent().find('.error').html('')
                    $(this).parent().parent().parent().find('.error').html(fun_error)
                } else {
                    $(this).parent().parent().parent().find('.error').html('')
                    $(this).parent().parent().append('<div class="input-danger">' + fun_error + '</div>')
                    $(this).parent().parent().addClass('text-danger')
                }
                error++
            } else {
                $(this).siblings('.input-danger').remove()
                $(this).parent().parent().removeClass('text-danger')
            }
        } else {
            $(this).siblings('.input-danger').remove()
            $(this).parent().parent().removeClass('text-danger')
        }
    })
    // $('select').change(function() {
    //     $(this).parent().parent().find('.input-danger').remove()
    //     $(this).parent().parent().removeClass('text-danger')
       
    //     if ($(this).attr("data-required") == 'yes' && $(this).val().trim() == '') {
    //         $(this).parent().parent().append('<div class="input-danger">' + $(this).attr('data-validation-required-message') + '</div>')
    //         $(this).parent().parent().addClass('text-danger')
    //         error++
    //     }  else {
    //         $(this).siblings('.input-danger').remove()
    //         $(this).parent().parent().removeClass('text-danger')
    //     }
    // })
}

// email exist ends
function check_email_exist(event, req, form) {

    id = $(form+' .id').val()
    var returnval;
    $.ajax({
        type: "POST",
        url: baseurl + 'login.php',
        data: $(form).find('select, textarea, input').serialize() + '&req=' + req + '&id=' + id,
        async: false,
        success: function(html) 
        {
            if (html.trim() == 'ok') {
                $(event).parent().parent().removeClass('has-error')
            } else {
                $(this).parent().parent().parent().find('.error').html('')
                $(this).parent().parent().append('<div class="input-danger">' + html + '</div>')
                $(this).parent().parent().addClass('text-danger')
            }
            returnval = html.trim();
        }
    });
    return returnval;
}

let timeout;
    const maxIdleTime = 60 * 60 * 1000; // 60 minutes of inactivity
    const maxSessionTime = 60 * 60 * 1000; // 1 hours total session time
 
    // Check if session has expired based on login timestamp
    function checkSessionExpiration() {
        const loginTimestamp = localStorage.getItem("login_timestamp_enquiry");
        if (loginTimestamp) {
            const currentTime = Date.now();
            const sessionAge = currentTime - parseInt(loginTimestamp);
            
            if (sessionAge > maxSessionTime) {
                // Session expired - log out user
                logout2("Your session has expired. Please log in again.");
                return false;
            }
        }
        return true;
    }

    // Reset the timeout on any user interaction (mouse move, keypress, etc.)
    function resetTimer() {
        clearTimeout(timeout);
        timeout = setTimeout(logout2, maxIdleTime);
         // Refresh login timestamp on activity for rolling session expiry
        localStorage.setItem("login_timestamp_enquiry", Date.now().toString());
        // const maxIdleTime = 30 * 60 * 1000; // 30 minutes in milliseconds
    }

    function logout2(message = "You have been inactive for 60 minutes. Logging you out...") {
        localStorage.removeItem("user_id_enquiry");
        localStorage.removeItem("user_email_enquiry");
        localStorage.removeItem("user_type_enquiry");
        localStorage.removeItem("user_name_enquiry");
        localStorage.removeItem("user_type_customer_enquiry");
        localStorage.removeItem("user_designation");
        localStorage.removeItem("login_timestamp_enquiry");
        alert(message);
        window.location.href = "login.html"
    }

    // Listen for user interactions
    window.onload = () => {
        // First check if session has expired due to time passed
        if (!checkSessionExpiration()) {
            return; // User will be logged out, don't set up activity listeners
        }
        
        document.body.addEventListener('mousemove', resetTimer);
        document.body.addEventListener('keypress', resetTimer);
        resetTimer(); // Start the timer when the page loads
    };


function logout() {
    localStorage.removeItem("user_id_enquiry");
    localStorage.removeItem("user_email_enquiry");
    localStorage.removeItem("user_type_enquiry");
    localStorage.removeItem("user_name_enquiry");
    localStorage.removeItem("user_type_customer_enquiry");
    localStorage.removeItem("user_designation");
    localStorage.removeItem("login_timestamp_enquiry");

    window.location.href = "login.html"
}

function set_delete_id(value) {

    $('#delete_pop #delete').val(value)

}

function set_id(value, id) {
    $(id).val(value)
}
function search_function(input, target, parents) {
    var input = document.getElementById(input);
    var filter = input.value.toLowerCase();
    if (filter.length > 0) {
        $(target).each(function() {
            if ($(this).text().toLowerCase().indexOf(filter) != -1) {
                $(this).removeClass('not-matching');
            } else {
                $(this).addClass('not-matching');
            }
            setTimeout(function() {
                if ($(parents).hasClass('.not-matching')) {
                    $(parents).addClass('hidden')
                }
            }, 200)
        })
    }
    if (filter.length <= 0) {
        $(target).removeClass('not-matching');
    }
}

function check_activity_id(text_id, id_id) {
    if ($(id_id).val() == '') {
        $(text_id).val('')
    }
}
$('.dropdown-menu.multiple').on('click', function(event) {
    event.stopPropagation();
});

function select_value(name, value, name_id, value_id, type = "single") {
    $(name_id).val(name)
    name_ids = value_id.replace('#', '.');
    name_ids = name_ids.replace('(', '')
    name_ids = name_ids.replace(')', '')
    name_ids = name_ids.split('_');
    //name_idss = name_ids.replace('language', '');
    if (type != 'multiple') {
        $(name_ids + 's_id .selected').removeClass('selected')
    }
    value = value.replace(/ /g, '_')
    if (type == 'multiple' && $(name_ids[0] + 's_' + name_ids[1] + ' ' + name_ids[0] + value).hasClass('selected')) {
        $(name_ids[0] + 's_' + name_ids[1] + ' ' + name_ids[0] + value).removeClass('selected')
    } else {
        //alert(name_ids[0] + 's_' + name_ids[1] + ' ' + name_ids[0] + value)
        $(name_ids[0] + 's_' + name_ids[1] + ' ' + name_ids[0] + value).addClass('selected ')
    }

    if (type != 'multiple') {
        $(name_ids + 's_id ' + name_idss + value).addClass('selected')
    }
    $(name_id).parent().parent().find('.input-danger').remove()
    $(name_id).parent().parent().removeClass('text-danger')
    $(name_id).parent().parent().removeClass('input-info');
    if (type == 'multiple') {
        value = name = '';
        $(name_ids[0] + 's_' + name_ids[1] + ' .selected').each(function() {
            if (value == '') {
                value = $(this).attr('data-text');
                name = $(this).attr('data-id');
            } else {
                value += ',' + $(this).attr('data-text');
                name += ',' + $(this).attr('data-id');
            }
        })
        $(name_id).val(name)
        $(value_id).val(value)
    } else {
        $(value_id).val(value)
    }
}

function cancel_clr(area) {
    areas=area.split(' ')
    $(areas[0]).find('.input-danger').remove()
    $(areas[0]).find('.text-danger').removeClass('text-danger')
    $(area).each(function(){
        if($(this).attr('data-type') !== undefined)
        {
            if($(this).attr('data-type')=='number')
            {
                $(this).val(0);            
            }
            else
            {
                $(this).val('');
            }
        }
        else
        {
            $(this).val('');      
        }
    })    
}


array_country=array_university = new Array();
    function search_data(e, classs, hiddenclass,array) 
    {
        arrayOfStr = eval(array);
        $(classs).html('')
        var filter = $(e).val()
        var filter = filter.toLowerCase();
        $j = 0;
        if (hiddenclass.indexOf(',') != -1) {
            hiddenclass = hiddenclass.split(',');
        } else {
            hiddenclass[0] = hiddenclass
        }

        if (filter.length > 1) {
            for (var $i in arrayOfStr) {
                if (typeof arrayOfStr[$i] !== 'function') {
                    if ($j <= 8) {
                        if (arrayOfStr[$i]['search_name'].toLowerCase().indexOf(filter) != -1) {
                            $location_name = arrayOfStr[$i]['search_name'];
                            funs='';
                            if(arrayOfStr[$i]['funs'] !== undefined)
                            {
                                funs=','+arrayOfStr[$i]['funs']
                            }
                            change = '';
                            for ($cc = 0; $cc < hiddenclass.length; $cc++) {
                                value = arrayOfStr[$i][hiddenclass[$cc].replace('#', '')];
                                if (change == '') {
                                    change += ",changetext('','" + value + "','" + hiddenclass[$cc] + "')"
                                    if (hiddenclass.length > 1) {
                                        change += ",";
                                    }

                                } else {
                                    change += "changetext('','" + value + "','" + hiddenclass[$cc] + "')"
                                    if (hiddenclass.length <= $cc) {
                                        change += ",";
                                    }

                                }

                            }
                            $(classs).append("<button class='dropdown-item text-capitalize' onClick=\"changetext('','" + $location_name + "','#" + $(e).attr('id') + "')" +funs+ change + "\" type='button'>" + boldString($location_name, filter) + "</button>")
                            $j++;
                        }
                    }
                }
            }
        } else {
            for (var $i in arrayOfStr) {
                if (typeof arrayOfStr[$i] !== 'function') {
                    if ($j <= 8) {
                        $location_name = arrayOfStr[$i]['search_name'];
                        funs='';
                        if(arrayOfStr[$i]['funs'] !== undefined)
                        {
                            funs=','+arrayOfStr[$i]['funs']
                        }
                        change = '';
                        for ($cc = 0; $cc < hiddenclass.length; $cc++) {
                            value = arrayOfStr[$i][hiddenclass[$cc].replace('#', '')];
                            if (change == '') {
                                change += ",changetext('','" + value + "','" + hiddenclass[$cc] + "')"
                                if (hiddenclass.length > 1) {
                                    change += ",";
                                }

                            } else {
                                change += "changetext('','" + value + "','" + hiddenclass[$cc] + "')"
                                if (hiddenclass.length <= $cc) {
                                    change += ",";
                                }

                            }

                        }

                        $(classs).append("<button class='dropdown-item text-capitalize' onClick=\"changetext('','" + $location_name + "','#" + $(e).attr('id') + "')" +funs+ change + "\" type='button'>" + boldString($location_name, filter) + "</button>")
                        $j++;
                    }
                }
            }
        }
    }

    function changetext(img, product, id) {
        $(id).val(product)
    }

    function boldString(str, substr) {
        var strRegExp = new RegExp(substr, 'ig');
        return str.replaceAll(strRegExp, '<b>' + substr + '</b>');
    }
if($('.datepickernomax').length>0)
{
    $('.datepickernomax').daterangepicker({
        singleDatePicker: true,
        timePicker: false, 
        showDropdowns: true,
        autoUpdateInput: true,    
        locale: {
            format: 'DD-MM-YYYY'
        }
    }).on('show.daterangepicker', function (ev, picker) {
            picker.container.find(".calendar-time").hide();
    }).on('change.daterangepicker', function (ev, picker) {
            $('.datepickernomax').parent().parent().removeClass('text-danger')
            $('.datepickernomax').parent().parent().find('.input-danger').remove()
    });
}
if($('.datepicker').length>0)
{
    $('.datepicker').daterangepicker({
        singleDatePicker: true,
        timePicker: false, 
        showDropdowns: true,
        autoUpdateInput: true,    
        minDate: moment(),    
        locale: {
            format: 'DD-MM-YYYY'
        }
    }).on('show.daterangepicker', function (ev, picker) {
            picker.container.find(".calendar-time").hide();
    }).on('change.daterangepicker', function (ev, picker) {
         $('.datepicker').parent().parent().removeClass('text-danger')
         $('.datepicker').parent().parent().find('.input-danger').remove()
    });
}
if($('.datepickeryearmonth').length>0)
{
$('.datepickeryearmonth').daterangepicker({
    singleDatePicker: true,
    timePicker: false, 
    showDropdowns: true,
    autoUpdateInput: true,    
    minDate: moment(),    
    locale: {
        format: 'YYYY/MM'
    }
}).on('show.daterangepicker', function (ev, picker) {
        picker.container.find(".calendar-time").hide();
}).on('change.daterangepicker', function (ev, picker) {
    $('.datepickeryearmonth').parent().parent().find('.input-danger').remove()
    $('.datepickeryearmonth').parent().parent().removeClass('text-danger')
});
}    
if($('.datepickermax').length>0)
{
    $('.datepickermax').daterangepicker({
        singleDatePicker: true,
        timePicker: false, 
        showDropdowns: true,
        maxDate: moment(),    
        locale: {
            format: 'DD-MM-YYYY'
        }
    }).on('show.daterangepicker', function (ev, picker) {
            picker.container.find(".calendar-time").hide();
    }).on('change.daterangepicker', function (ev, picker) {
        $('.datepickermax').parent().parent().find('.input-danger').remove()
        $('.datepickermax').parent().parent().removeClass('text-danger')
    });
}
if($('.datetimepicker').length>0)
{

    $('.datetimepicker').daterangepicker({
    singleDatePicker: true,
    timePicker: true, 
    showDropdowns: true,
    autoApply: true,    
    autoUpdateInput: true, 
    showButtonPanel: false,   
    minDate: moment(),    
    locale: {
        format: 'DD-MM-YYYY h:mm a'
    }
    }).on('change.daterangepicker', function (ev, picker) {
        $('.datetimepicker').parent().parent().find('.input-danger').remove()
        $('.datetimepicker').parent().parent().removeClass('text-danger')
    });
}
if($('.timepicker').length>0)
{
    $('.timepicker').daterangepicker({
        singleDatePicker: true,
        timePicker: true, 
        showDropdowns: true,
        autoUpdateInput: true,
        locale: {
                format: 'h:mm a'
            }
    }).on('show.daterangepicker', function (ev, picker) {
            picker.container.find(".calendar-table").hide();
    }).on('change.daterangepicker', function (ev, picker) {
        $('.timepicker').parent().parent().find('.input-danger').remove()
        $('.timepicker').parent().parent().removeClass('text-danger')
    });
}

function datepicker(date = '',type='hotel') {
    date = decodeURIComponent(date);
    from = to = '';
    if (date != '') {
      date = date.split('-');
      from = date[0].trim();
      to = date[1].trim();
    }
    if (from == '') {
      var currentDate = new Date();
    }
    else {
      var currentDate = new Date(from);
    }
  
    if (to == '') {
      var nextDay = new Date(new Date().getTime() + 24 * 60 * 60 * 1000);
    }
    else {
      var nextDay = new Date(to);
    }
    var day = nextDay.getDate()
    var month = nextDay.getMonth() + 1
    var year = nextDay.getFullYear()
    next_date = day + '-' + month + '-' + year;
    
    
    var day = currentDate.getDate()
    var monthpluseone = currentDate.getMonth() + 2
    var year = currentDate.getFullYear()
    max_date = day + '-' + monthpluseone + '-' + year;
       
    $('#'+type+'from_date').daterangepicker({
      singleDatePicker: true,
      minDate: moment(),
      showDropdowns: true,
      autoUpdateInput: true,
      startDate: currentDate,
      endDate: false,
      linkedCalendars: true ,
      locale: {
        format: 'DD-MM-YYYY'
        }   
    },
    function (start, end, label) {
      setTimeout(function () 
      {
        var nextDay = new Date(new Date(start).getTime() + 24 * 60 * 60 * 1000);
        var day = nextDay.getDate()
        var month = nextDay.getMonth() + 1
        
        var year = nextDay.getFullYear()
        next_date = day + '-' + month + '-' + year;
       
        var star_date = new Date(start);
        var day = star_date.getDate()
        var month = star_date.getMonth() + 1
        var monthpluseone = star_date.getMonth() + 2
        var year = star_date.getFullYear()
        max_date = monthpluseone + '/' + day + '/' + year;
        star_date = day + '-' + month + '-' + year;
        if($('#'+type+'to_date').length>0)
        {
          $('#'+type+'to_date').daterangepicker({
            singleDatePicker: true,
            minDate:next_date,
            showDropdowns: true,
            autoUpdateInput: true,
            startDate: next_date,
            endDate: false,
            linkedCalendars: true  ,
            locale: {
                format: 'DD-MM-YYYY'
            }
          },
          function (start, end, label) {          
            var star_date = new Date(start);
            var day = star_date.getDate()
            var month = star_date.getMonth() + 1
            var year = star_date.getFullYear()
            star_date = month + '/' + day + '/' + year;
            
            $('#'+type+'to_date_hidden').val(star_date)
  
            
          });
        }
      }, 500)
    });
    
    if($('#'+type+'to_date').length>0)
    {
      $('#'+type+'to_date').daterangepicker({
        singleDatePicker: true,
        minDate: next_date,
        showDropdowns: true,
        autoUpdateInput: true,
        startDate: next_date,
        endDate: false,
        linkedCalendars: true,
        locale: {
            format: 'DD-MM-YYYY'
        }
      },
      function (start, end, label) {
      });
    }  
  }
  
function add_customer()
{
    if($('#add_customer_modal input[type=text][name=number]').val().trim()=='')
    {
        show_toast('Add contact number', "danger", '', "top", "danger")
        if($('#add_customer_modal input[type=text][name=number]').val().trim()=='')
        {   
            $('#add_customer_modal input[type=text][name=email]').parent().addClass('text-danger')            
        }
        $('#add_customer_modal input[type=text][name=number]').parent().addClass('text-danger')            
    }
    else if($('#add_customer_modal input[type=radio][name=etype]:checked').length<=0)
    {
        show_toast('Please select type of Enquiry', "danger", '', "top", "danger")
        $('#add_customer_modal input[type=radio][name=etype]').siblings('.form-check-label').addClass('text-danger')            
        $('#add_customer_modal input[type=text][name=number]').parent().removeClass('text-danger')                    
        $('#add_customer_modal input[type=text][name=email]').parent().removeClass('text-danger')                    
        
    }
    else if($('#add_customer_modal input[type=radio][name=etype_customer]:checked').length<=0 && $('#add_customer_modal input[type=radio][name=etype]:checked').val()!='domestic')
    {
        $('#add_customer_modal input[type=radio][name=etype]').siblings('.form-check-label').removeClass('text-danger')                    
        $('#add_customer_modal input[type=radio][name=etype_customer]').siblings('.form-check-label').addClass('text-danger')            
        $('#add_customer_modal input[type=text][name=number]').parent().removeClass('text-danger')                    
        $('#add_customer_modal input[type=text][name=email]').parent().removeClass('text-danger')                    
        show_toast('Please select type of customer', "danger", '', "top", "danger")
    }
    else
    {
        $('#add_customer_modal button:first').attr('disabled',true)
        $('#add_customer_modal input[type=text][name=number]').parent().removeClass('text-danger')                    
        $('#add_customer_modal input[type=text][name=email]').parent().removeClass('text-danger')                    
        $('#add_customer_modal input[type=radio][name=etype]').siblings('.form-check-label').removeClass('text-danger')                    
        $('#add_customer_modal input[type=radio][name=etype_customer]').siblings('.form-check-label').removeClass('text-danger')                    
        type=$('#add_customer_modal input[type=radio][name=etype]:checked').val()
        type_customer=localStorage.getItem("user_type_customer_enquiry").trim()
        corporate=$('#add_customer_modal .corporates:visible').val()
        console.log(baseurl + 'customer.php?'+$('#add_customer_modal').find('select, textarea, input').serialize() + '&req=1&type='+type+'&assigned_id='+localStorage.getItem("user_id_enquiry")+'&type_customer='+type_customer+"&user_type_enquiry="+localStorage.getItem("user_type_enquiry").trim()+'&corporate='+corporate)
        $.ajax({
            type: "POST",
            url: baseurl + 'customer.php',
            data: $('#add_customer_modal').find('select, textarea, input').serialize() + '&req=1&type='+type+'&assigned_id='+localStorage.getItem("user_id_enquiry")+'&type_customer='+type_customer+"&user_type_enquiry="+localStorage.getItem("user_type_enquiry").trim()+'&corporate='+corporate,
            async: false,
            // success: function(html) 
            // {
            //     $('#add_customer_modal button:first').attr('disabled',false)
            //     if($("#add_customer_modal input[name=id]").val()=='')
            //     {
            //         show_toast("Customer Details Added", "success", '', "top", "success");
            //     }
            //     else
            //     {
            //         show_toast("Customer Details Updated", "success", '', "top", "success");
            //         window.location.href = 'customer_detail.html';
            //     }
            success: function(html) {
                $('#add_customer_modal button:first').attr('disabled', false);
            
                // Show success toast
                if ($("#add_customer_modal input[name=id]").val() == '') {
                    show_toast("Customer Details Added", "success", '', "top", "success");
                } else {
                    show_toast("Customer Details Updated", "success", '', "top", "success");
            
                    // Get the ID from the form and append it to the URL
                    var customerId = $("#add_customer_modal input[name=id]").val();
            
                    // Redirect to customer_detail.html with the customer id as a query parameter
                    window.location.href = 'customer_detail.html?id=' + customerId;
                }
            
                if(filename=='dashboard.html')
                {
                    get_customer_detail();
                    get_customer_itinerary();
                }
                if(filename=='index.html' || filename=='')
                {
                    apply_search()
                }
                $('#add_customer_modal').modal('hide')
                if(window.location.hash!='')
                {
                    window.location.replace('index.html');
                }
            }
        });
    }
}
function click_btn(id)
{
    $(id).click()
}
function active_tab(id)
{
    $('.nav-tabs a[href="'+id+'"]').tab('show')
}
function add_enquiry()
{

    type=$('#add_enquiry_modal input[type=radio]:checked').val()
    $.ajax({
        type: "POST",
        url: baseurl + 'customer.php',
        data: $('#add_enquiry_modal').find('select, textarea, input').serialize() + '&req=3&type='+type+'&assigned_id='+localStorage.getItem("user_id_enquiry")+"&user_type_enquiry="+localStorage.getItem("user_type_enquiry").trim(),
        async: false,
        success: function(html) 
        {
            console.log(html)
            $('#add_enquiry_modal').modal('hide')
            if($('#add_enquiry_modal .id')=='')
            {
                show_toast('Enquiry Added Successfully', "success", '', "top", "success");
            }
            else
            {
                show_toast('Enquiry Updated Successfully', "success", '', "top", "success");
            }
            var url = window.location.pathname;
            var filename = url.substring(url.lastIndexOf('/')+1);
            if(filename=='dashboard.html')
            {
                get_customer_itinerary();     
            }
        }
    });
}

function removeClas(id,cls,child='')
{
    if(child=='')
    {
        $(id).removeClass(cls)
    }
    else
    {
        $(id).find(child).removeClass(cls)
    }
}
function addClas(id,cls,child='')
{
    if(child=='')
    {
        $(id).addClass(cls)
    }
    else
    {
        $(id).find(child).addClass(cls)
    }
}
names=localStorage.getItem("user_name_enquiry");
if(names!== undefined)
{
    names=names.split(' ');
    namess=''
    for($i=0;$i<names.length;$i++)
    {
        if($i<names.length-1)
        {
            namess+=names[$i][0]+'. '
        }
        else
        {
            namess+=names[$i]
        }
        console.log(namess)
    }
    $('#welcome').html(namess)
}
function clear_btn_hide(fun) {

    if ($('#find').val().length > 0) {
        $('#clear_in').removeClass('hidden')
    } else {
        if ($('#filter_inner').hasClass('hidden')) {
            window[fun]()
        } else {
            apply_search()
        }
        $('#clear_in').addClass('hidden')        
    }
}
function clear_input() {

    $('#find').val('');
    $('#clear_in').addClass('hidden')
    if ($('#filter_inner').hasClass('hidden')) {
        apply_search()
    } else {
        apply_search()
    }
}
function add_status()
{

    status=$('#add_status_modal input[type=radio]:checked').val()
    $.ajax({
        type: "POST",
        url: baseurl + 'customer.php',
        data: $('#add_status_modal').find('select, textarea, input').serialize() + '&req=4&status='+status+'&assigned_id='+localStorage.getItem("user_id_enquiry"),
        async: false,
        success: function(html) 
        {
            console.log(html)
            $('#add_status_modal').modal('hide');
            if($('#add_status_modal .id')=='')
            {
                show_toast('Status Added Successfully', "success", '', "top", "success");
            }
            else
            {
                show_toast('Status Updated Successfully', "success", '', "top", "success");
            }
            $('#add_status_modal input[type=text],#add_status_modal input[type=hidden]').val('')
            $('#add_status_modal input[type=radio][name=status][value=Did not Connect]').prop('checked',true)
        }
    });
    
}
$('#add_status_modal input[type=radio]').click(function(){
    if($('#add_status_modal input[type=radio]:checked').val()=='Call later')
    {
        $('.showdatetime').removeClass('hidden')
    }
    else
    {
        $('.showdatetime').addClass('hidden')
    }
});

// function show_tooltip(event,data)
// {
//     if(data.trim()!='')
//     {
//         var x = event.clientX;
//         var y = event.clientY;
//         $('body').append('<div class="tooltips" style="width:auto">'+data+'</div>')
//         $('body .tooltips').css({'left':x,'top':y,'position':'fixed'})
//     }
// }

function show_tooltip(event, data) {
    if (data.trim() !== '') {
        var x = event.clientX;
        var y = event.clientY;
        var ws = window.innerWidth;

        // Remove existing tooltip if any
        $('.tooltips').remove();

        // Append tooltip to body
        $('body').append('<div class="tooltips" style="position: fixed; background: #333; color: #fff; padding: 5px 10px; border-radius: 4px; font-size: 12px; white-space: nowrap; z-index: 1000;">' + data + '</div>');

        var $tooltip = $('.tooltips');

        // Get tooltip dimensions
        var tooltipWidth = $tooltip.outerWidth();
        var tooltipHeight = $tooltip.outerHeight();

        // Adjust x if tooltip would overflow the right edge
        if ((x + tooltipWidth / 2) > ws) {
            x = ws - tooltipWidth - 10;
        } else if ((x - tooltipWidth / 2) < 0) {
            x = 10;
        } else {
            x = x - tooltipWidth / 2;
        }

        // Position tooltip above the mouse cursor with a small gap
        y = y - tooltipHeight - 10;

        // Apply the final position
        $tooltip.css({ left: x + 'px', top: y + 'px' });
    }
}

function hide_tooltip(event)
{
    $('body .tooltips').remove()
}
function set_balance(id, name, type, balance, price) {
    const input = document.querySelector(id + ' input[name=' + name + ']');
    let val = parseInt(input.value) || 0;
    let max_value = parseInt(input.getAttribute('max-value')) || 99;
    let min_value = parseInt(input.getAttribute('min-value')) || 0;

    if (type === 'plus') {
        // If the price is a float, increment by 1. Otherwise, increment by 1 for integer values.
        if (price === 'float') {
            val = parseFloat(val) + 1;
        } else {
            if (val < max_value) {
                val += 1; // Increment the value by 1 (ensure it doesn't exceed max_value)
            }
        }
    } else if (type === 'minus') {
        // Decrease the value by 1 but not less than the min value.
        if (price === 'float') {
            val = parseFloat(val) - 1;
        } else {
            if (val > min_value) {
                val -= 1;
            }
        }
        if (val < 0) {
            val = 0; // Ensure the value doesn't go below 0
        }
    }

    // Update the input field with the new value
    input.value = val;

    // Update the maximum value of hotel children/adults based on the current value of the opposite field
    if (name === 'hotel_adult') {
        document.querySelector('.hotel_children').setAttribute('max-value', 6 - parseInt(document.querySelector('.hotel_adult').value));
    }
    if (name === 'hotel_children') {
        document.querySelector('.hotel_adult').setAttribute('max-value', 6 - parseInt(document.querySelector('.hotel_children').value));
    }
}

function set_balance_old(id,name,type,balance,price,input)
{
    
	if(type=='plus')
	{
        val=$(id+' input[name='+name+']').val();
        if($(id+' input[name='+name+']').attr('max-value')!== undefined)
        {
            max_value=$(id+' input[name='+name+']').attr('max-value');
            if(price=='float' && $(id+' input[name='+name+']').val()<max_value)
            {
                val=parseFloat($(id+' input[name='+name+']').val())+1;
            }
            else
            {
                if($(id+' input[name='+name+']').val()<max_value)
                {
                    val=parseInt($(id+' input[name='+name+']').val())+1;
                }
            }
        }
        else
        {
            if(price=='float')
            {
                val=parseFloat($(id+' input[name='+name+']').val())+1;
            }
            else
            {
                val=parseInt($(id+' input[name='+name+']').val())+1;
            }
        }
		$(id+' input[name='+name+']').val(val);
        if(name=='hotel_adult')
        {
            $('.hotel_children').attr('max-value',6-parseInt($('.hotel_adult').val()));
        }
        if(name=='hotel_children')
        {
            $('.hotel_adult').attr('max-value',6-parseInt($('.hotel_children').val()));
        }
    }
	else
	{
        val=$(id+' input[name='+name+']').val();
        if($(id+' input[name='+name+']').attr('min-value')!== undefined)
        {
            min_value=$(id+' input[name='+name+']').attr('min-value');
            if(price=='float' && $(id+' input[name='+name+']').val()>min_value)
            {
                val=parseFloat($(id+' input[name='+name+']').val())-1;
            }
            else
            {
                if($(id+' input[name='+name+']').val()>min_value)
                {
                val=parseInt($(id+' input[name='+name+']').val())-1;
                }
            }

        }
        else
        {
            if(price=='float')
            {
                val=parseFloat($(id+' input[name='+name+']').val())-1;
            }
            else
            {
                val=parseInt($(id+' input[name='+name+']').val())-1;
            }
        }
        if(val>=0)
        {
            $(id+' input[name='+name+']').val(val);
        }
        else
        {
            $(id+' input[name='+name+']').val(0);
        }
        if(name=='hotel_adult')
        {
            $('.hotel_children').attr('max-value',6-parseInt($('.hotel_adult').val()));
        }
        if(name=='hotel_children')
        {
            $('.hotel_adult').attr('max-value',6-parseInt($('.hotel_children').val()));
        }
	}
}
function set_zero(name,id)
{
	if(parseFloat($(id+' input[name='+name+']').val())<=0 || $(id+' input[name='+name+']').val()=='' )
	{
		$(id+' input[name='+name+']').val(0);
	}
	else
	{
		s = $(id+' input[name='+name+']').val().replace(/^0+/, '');
		$('input[name='+name+']').val(s);
	}
}
function check_type(id,name,hideid,hidename)
{
    i=0;
    $('#add_customer_modal input[type=radio][name='+name+']').siblings('.form-check-label').removeClass('text-danger')                    
        
    type=$(id+' input[name='+name+']:checked').map(function(){
        if($(this).val()=='domestic')
        {
            i++;
        }
    })
    if(i>0)
    {
        
        $(hideid).addClass('d-none');
        $(hideid+' input[name='+hidename+']:checked').prop('checked',false);
    }
    else
    {
        
        $(hideid).removeClass('d-none');
        $(hideid+' input[name='+hidename+']:first').prop('checked',false);
    }
}

// $(document).ready(function() {
//     // Load countries on page load
//     $.ajax({
//         type: "GET",
//         url: newbaseurl + 'Enquiry/get_countrieslist',
//         dataType: "json",
//         success: function(data) {
//             $.each(data, function(i, country) {
//                 $('#countryDropdown').append('<option value="' + country.country_id + '">' + country.country_name + '</option>');
//             });
//         },
//         error: function(xhr) {
//             console.error("Country Load Error:", xhr.responseText);
//         }
//     });
// })




function change_text(val,id)
{
    $(id).html(val)
}
function get_staff_list() 
{
    var user_type_enquiry = localStorage.getItem("user_type_enquiry");
    type_customer ='';
    if(user_type_enquiry=='international')
    {
        type_customer = localStorage.getItem("user_type_customer_enquiry");
    }
  $.ajax({
      url: baseurl + 'login.php',
      type: "GET",
      async: true,
      data: "req=3&type="+user_type_enquiry+"&type_customer="+type_customer,
      success: function (data) 
      {
        console.log(data)
        $('#staff_list').html(data);        
        // Create a temporary DOM element to parse HTML
        const temp = $('<div>').html(data);

        const staffOptions = {};

        temp.find('optgroup').each(function () {
        const groupLabel = $(this).attr('label').toLowerCase();
        staffOptions[groupLabel] = [];

        $(this).find('option').each(function () {
            const value = $(this).attr('value');
            const label = $(this).text().trim();
            const style = $(this).attr('style') || '';

            staffOptions[groupLabel].push({
            value: value,
            label: label,
            bold: /font-weight\s*:\s*bold/i.test(style)
            });
        });
        });

        console.log('Parsed staffOptions:', staffOptions);

        // Optional: use staffOptions to render dropdown
        renderStaffDropdown(staffOptions);
      },
      error:function()
      {
      }

  });
}

function renderStaffDropdown(staffOptions) {
    const dropdown = document.getElementById('staff_dropdown');
    dropdown.innerHTML = ''; // Clear any old content

    // Add 'All Staff' at the top
    const allStaffOption = document.createElement('div');
    allStaffOption.className = 'form-check customer-type';
    allStaffOption.innerHTML = `<label><span class="icon"></span> All Staff</label>`;
    allStaffOption.onclick = function () {
        document.getElementById('selected_staff').textContent = 'All Staff';
        document.getElementById('selected_staff').dataset.value = ''; // Important: Clear value
        $('#staff_dropdown').addClass('d-none');
        apply_search(); // Re-trigger search with all staff
    };
    dropdown.appendChild(allStaffOption);

    for (const [groupLabel, options] of Object.entries(staffOptions)) {
      // Group label
      const groupHeader = document.createElement('div');
      groupHeader.className = 'form-check customer-type active';
      groupHeader.innerHTML = `<label><span class="icon"></span> ${groupLabel.charAt(0).toUpperCase() + groupLabel.slice(1)}</label>`;
      dropdown.appendChild(groupHeader);

      // Options
      options.forEach(option => {
        const optionDiv = document.createElement('div');
        optionDiv.className = 'form-check customer-type';
        optionDiv.dataset.value = option.value;

        const labelEl = document.createElement('label');
        labelEl.innerHTML = `<span class="icon"></span> ${option.label}`;
        if (option.bold) {
          labelEl.style.fontWeight = 'bold';
          optionDiv.style.background = '#ededf5';
        }

        optionDiv.appendChild(labelEl);
        optionDiv.onclick = function () {
          // Update toggle label
          document.getElementById('selected_staff').textContent = option.label;
          // Store selected value
          document.getElementById('selected_staff').dataset.value = option.value;
          // Hide dropdown
          $('#staff_dropdown').addClass('d-none');

          apply_search();

        };

        dropdown.appendChild(optionDiv);
      });
    }
  }
function change_password() 
{
   
    $.ajax({
      url: baseurl + 'login.php',
      type: "POST",
      async: true,
      data: "req=4&id="+localStorage.getItem("user_id_enquiry").trim()+"&"+decodeURIComponent($("#change_password").find('select, textarea, input').serialize()),
      success: function (data) 
      {
            show_toast('Password Updated Successfully', "success", '', "top", "success");  
            $('#change_password').modal('hide')
      },
      error:function()
      {
      }

  });
}
function onenter(fun='',parameter='')
{
	var key=event.keyCode|| event.which;
	if(key == 13)
	{
        if(fun!='')
        {
            if (parameter == false) {
                window[fun]()
            } else {
                window[fun](parameter)
            }
        }
	}

}
function get_pageination($total_pages,$limit,$adjacents,$page,$fun,filters='')
{
   if ($page == 0 || $page=='') 
    {
    $page = 1;					
    }
    $prev = $page - 1;							
    $next = $page + 1;							
    $lastpage = Math.ceil($total_pages/$limit);		
    $lpm1 = $lastpage - 1;						
    $pagination = "";
    filters=filters
    if($lastpage > 1)
    {	
        $pagination += "<div class=\"pagination justify-content-end width100 display-block text-right\">";
        if ($page > 1) 
            $pagination+= "<a href=\"#\" onclick=\""+$fun+"('"+$prev+"',filters)\"><button type='button' class='btn  btn-outline-primary btn-default waves-effect  m-l-5 m-r-5' style='border-radius: 10px 0px 0px 10px;'>Previous</button></a>";
        else
            $pagination+= "<span class=\"disabled\"><button type='button' class='btn  btn-outline-primary btn-default disabled waves-effect  m-l-5 m-r-5' style='border-radius: 10px 0px 0px 10px;'>Previous</button></span>";	
        if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
        {	
            for ($counter = 1; $counter <= $lastpage; $counter++)
            {
                if ($counter == $page)
                    $pagination+= "<span data-onclick=\""+$fun+"('"+$counter+"',filters)\" class=\"current\"><button type='button' class='btn btn-square btn-outline-primary btn-primary waves-effect  m-l-5 m-r-5'>"+$counter+"</button></span>";
                else
                    $pagination+= "<a href=\"#\" onclick=\""+$fun+"('"+$counter+"',filters)\"><button type='button' class='btn btn-square btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>"+$counter+"</button></a>";					
            }
        }
        else if($lastpage > 5 + ($adjacents))	
        {
            if($page < 1 + ($adjacents * 2))		
            {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                {
                    if ($counter == $page)
                        $pagination+= "<span data-onclick=\""+$fun+"('"+$counter+"',filters)\" class=\"current\"><button type='button' class='btn btn-square btn-outline-primary btn-primary  waves-effect  m-l-5 m-r-5'>"+$counter+"</button></span>";
                    else
                        $pagination+= "<a href=\"#\" onclick=\""+$fun+"('"+$counter+"',filters)\"><button type='button' class='btn  btn-square btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>"+$counter+"</button></a>";					
                }
                $pagination+= "<button type='button' class='btn btn-outline-primary btn-square btn-square btn-default waves-effect  m-l-5 m-r-5'>...</button>";
                $pagination+= "<a href=\"#\" onclick=\""+$fun+"('"+$lpm1+"',filters)\"><button type='button' class='btn btn-square btn-outline-primary btn-square btn-default waves-effect  m-l-5 m-r-5'>"+$lpm1+"</button></a>";
                $pagination+= "<a href=\"#\" onclick=\""+$fun+"('"+$lastpage+"',filters)\"><button type='button' class='btn btn-square btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>"+$lastpage+"</button></a>";		
            }
            else if($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
            {
                $pagination+= "<a href=\"#\" onclick=\""+$fun+"('1',filters)\"><button type='button' class='btn btn-square btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>1</button></a>";
                $pagination+= "<a href=\"#\" onclick=\""+$fun+"('2',filters)\"><button type='button' class='btn btn-square btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>2</button></a>";
                $pagination+= "<button type='button' class='btn btn-square btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>...</button>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                {
                    if ($counter == $page)
                        $pagination+= "<span data-onclick=\""+$fun+"('"+$counter+"',filters)\" class=\"current\"><button type='button' class='btn btn-square btn-outline-primary btn-primary waves-effect  m-l-5 m-r-5'>"+$counter+"</button></span>";
                    else
                        $pagination+= "<a href=\"#\" onclick=\""+$fun+"('"+$counter+"',filters)\"><button type='button' class='btn btn-square btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>"+$counter+"</button></a>";					
                }
                $pagination+= "<button type='button' class='btn btn-square btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>...</button>";
                $pagination+= "<a href=\"#\" onclick=\""+$fun+"('"+$lpm1+"',filters)\"><button type='button' class='btn btn-square btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>"+$lpm1+"</button></a>";
                $pagination+= "<a href=\"#\" onclick=\""+$fun+"('"+$lastpage+"',filters)\"><button type='button' class='btn btn-square btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>"+$lastpage+"</button></a>";		
            }
            else
            {
                $pagination+= "<a href=\"#\" onclick=\""+$fun+"('1',filters)\"><button type='button' class='btn btn-square btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>1</button></a>";
                $pagination+= "<a href=\"#\" onclick=\""+$fun+"('2',filters)\"><button type='button' class='btn btn-square btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>2</button></a>";
                $pagination+= "<button type='button' class='btn btn-square btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>...</button>";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
                {
                    if ($counter == $page)
                        $pagination+= "<span class=\"current\"><button type='button' class='btn btn-square btn-outline-primary btn-primary waves-effect m-l-5 m-r-5'>"+$counter+"</button></span>";
                    else
                        $pagination+= "<a href=\"#\" onclick=\""+$fun+"('"+$counter+"',filters)\"><button type='button' class='btn btn-square btn-outline-primary btn-default waves-effect  m-l-5 m-r-5'>"+$counter+"</button></a>";					
                }
            }
        }
        
        if ($page < $counter - 1) 
            $pagination+= "<a href=\"#\" onclick=\""+$fun+"('"+$next+"',filters)\"><button type='button' class='btn btn-outline-primary btn-default waves-effect  m-l-5 m-r-5' style='border-radius: 0px 10px 10px 0px;'>Next</button></a>";
        else
            $pagination+= "<span class=\"disabled\"><button type='button' class='btn btn-outline-primary btn-primary disabled waves-effect  m-l-5 m-r-5' style='border-radius: 0px 10px 10px 0px;'>Next</button></span>";
        $pagination+= "</div>\n";		
    }

    return $pagination;
}

function get_agent_list(id='')
{
    $.ajax({
        type: "GET",
        url: newbaseurl + 'Agent',
        async: true,
        success: function(html) 
        {
            $('#agent_id').select2();
            html=JSON.parse(html)
            option=''
            option+='<option value="">Select Agent</option>'
            $(html).each(function (i, val) 
            {
                selected=""
                if(id>0 && val['id']==id)
                {
                    selected="selected"
                }
                option+='<option '+selected+' value="'+val['name']+'" data-number="'+val['number']+'" data-email="'+val['email']+'" data-id="'+val['id']+'">'+val['name']+'</option>'
            })
            $('#agent_id').html(option)
            $('#agent_id').select2({
                dropdownParent: $("#add_customer_modal .agent")
              })
        }
    })
}
function set_agent_detail(e)
{
    set_id($('#agent_id option:selected').attr('data-id'),'#add_customer_modal input[name=agent_id]');
    if($('#add_customer_modal input[name=email]').val()=='')
    {
        set_id($('#agent_id option:selected').attr('data-email'),'#add_customer_modal input[name=email]');
        // Clear validation errors for email field after populating
        var $emailField = $('#add_customer_modal input[name=email]');
        var emailValue = $emailField.val();
        if(emailValue && emailValue.trim() !== '') {
            // Check if email is valid
            var emailPattern = $emailField.data('pattern');
            if(!emailPattern || (emailPattern && new RegExp(emailPattern).test(emailValue.trim()))) {
                // Clear validation errors
                $emailField.parent().parent().removeClass('text-danger');
                $emailField.parent().parent().find('.input-danger').remove();
                $emailField.siblings('.error-msg').hide();
                $emailField.css('border', '2px solid #283477');
                // Trigger change event to update form validation state
                $emailField.trigger('change');
                // Check form validity for customer_detail.html modal
                if (typeof checkCustomerFormValidity === 'function') {
                    checkCustomerFormValidity();
                }
                // Check form validity for index.html modal
                if (typeof checkFormValidity === 'function') {
                    checkFormValidity(false);
                }
            }
        }
    }
    if($('#add_customer_modal input[name=name]').val()=='')
    {
        set_id($('#agent_id option:selected').val(),'#add_customer_modal input[name=name]');
    }
    if($('#add_customer_modal input[name=number]').val()=='')
    {
        set_id($('#agent_id option:selected').attr('data-number'),'#add_customer_modal input[name=number]')
        // Clear validation errors for contact number field after populating
        var $numberField = $('#add_customer_modal input[name=number]');
        var numberValue = $numberField.val();
        if(numberValue && numberValue.trim() !== '') {
            // Clear validation errors
            $numberField.parent().parent().removeClass('text-danger');
            $numberField.parent().parent().find('.input-danger').remove();
            $numberField.siblings('.error-msg').hide();
            $numberField.css('border', '2px solid #283477');
            // Trigger change event to update form validation state
            $numberField.trigger('change');
            // Check form validity for customer_detail.html modal
            if (typeof checkCustomerFormValidity === 'function') {
                checkCustomerFormValidity();
            }
            // Check form validity for index.html modal
            if (typeof checkFormValidity === 'function') {
                checkFormValidity(false);
            }
        }
    }
}
function redirect(id, value) {
    window.open(value + '.html?id=' + id);
}
$('#switch').click(function(){
    if($(this).prop('checked')==true)
    {
        window.location.href='https://travelreport.zesttourcrm.in/staging/'
    }
})
$user_type = localStorage.getItem("user_type_enquiry")
if ($user_type == 'admin') {
    $('.remove_admin').removeClass('d-none')
}


// function add_supplier()
// {
//     $.ajax({
//         type: "GET",
//         url: newbaseurl + 'Supplier/add_Supplier',
//         data: $('#add_supplier_modal').find('select, textarea, input').serialize() + '&assigned_id='+localStorage.getItem("user_id_enquiry"),
//         async: true,
//         success: function(html) 
//         {
//             html=JSON.parse(html)
//             if(html['success']==true)
//             {
//                 $('#add_supplier_modal').modal('hide')
//                 show_toast(html['message'], "success", '', "top", "success");
//             }
//             if(filename.includes('supplier.html'))
//             {
//                 apply_search()
//             }
//         }
//     })
// }
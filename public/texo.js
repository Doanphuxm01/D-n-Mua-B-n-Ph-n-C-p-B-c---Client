/**
 * Created by ngankt2@gmail.com
 * Website: https://techhandle.net
 */

function _AUTO_COMPLETE_INIT(selector, link_api, text, multi, options) {
    if (typeof  multi === 'undefined') {
        multi = true;
    }
    let selected = $(selector).attr('data-selected');
    let disabled = $(selector).attr('disabled');
    let opt = {
        placeholder: text,
        multiple: multi,
        disabled : disabled,
        ajax: {
            url: link_api,
            dataType: 'json',
            data: function (term, page) {
                return {
                    q: term,
                };
            },
            results: function (data, page) {
                return {results: data.data};
            },
        },

    }
    if (typeof options === 'object') {
        if (options.data) {
            opt.data = options.data

        }

    }
    let a = $(selector).select2(opt);
    try {
        a.select2('data', eval(selected));
        console.log(selected)
    } catch (e) {
        console.warn(e)
    }
}
function _AUTO_COMPLETE_INIT_FILTER(selector, link_api, text, multi, options) {
    if (typeof  multi === 'undefined') {
        multi = true;
    }
    let selected = $(selector).attr('data-selected');
    let opt = {
        placeholder: text,
        multiple: multi,
        ajax: {
            url: link_api,
            dataType: 'json',
            data: function (term, page) {
                return {
                    q: term,
                };
            },
            results: function (data, page) {
                return {results: data.data};
            },
        },
        initSelection: function (element, callback) {
            // the input tag has a value attribute preloaded that points to a preselected repository's id
            // this function resolves that id attribute to an object that select2 can render
            // using its formatResult renderer - that way the repository name is shown preselected
            var id = $(element).val();
            if (id !== "") {
                $.ajax(link_api, {
                    data: {
                        q: id
                    },
                    dataType: "json"
                }).done(function (data) {
                    callback(data.data);
                });
            }
        },
    }
    if (typeof options === 'object') {
        if (options.data) {
            opt.data = options.data

        }

    }
    let a = $(selector).select2(opt);
    try {
        a.select2('data', eval(selected));
        console.log(selected)
    } catch (e) {
        console.warn(e)
    }
}



var APPLICATION = {
    getListCity(callBack) {
        _POST(public_link('public-api/location/get-all-city'), [], callBack);
    }, getLocationSub(parent_code, callBack) {
        _POST(public_link('public-api/location/get-sub-location?parent_key=' + parent_code), [], callBack);
    }, getPositionByDep(department_id, callBack) {
        _POST(public_link('public-api/staff/get-position-list?department_id=' + department_id), [], callBack);
    },
    /**
     * Sử dụng điều hướng phần tỉnh thành quận huyện
     * tham khảo phần input khách hàng
     * @param parent_code
     * @param select_element
     * @param string_null
     * @returns {*}
     * @private
     */
    _changeCity(parent_code, select_element, string_null, _id) {
        jQuery(select_element).attr('readonly', true);
        if (select_element == '#location-district') {
            console.log(_id)
            if(_id) {
                jQuery('#location-town').html('<option value="">Chọn xã phường</option>').select2().val(_id).trigger('change');
            }else {
                jQuery('#location-town').html('<option value="">Chọn xã phường</option>').select2();
            }

        }
        return APPLICATION.getLocationSub(parent_code, function (json) {
            let html = '<option value="">' + string_null + '</option>';
            if (typeof json.data !== 'undefined') {
                for (let i in json.data) {
                    let location = json.data[i];
                    let selected = '';
                    if (typeof location.name !== 'undefined') {
                        html += '<option value="' + location.slug + '" ' + selected + '>' + location.name + '</option>';
                    }
                }
            }
            if(_id) {
                jQuery(select_element).attr('readonly', false).html(html).select2().val(_id).trigger('change');
            }else {
                jQuery(select_element).attr('readonly', false).html(html).select2();
            }
        });
    },
    _changeDepartment(department_id, select_element, string_null, _code) {
        jQuery(select_element).attr('readonly', true);

        return APPLICATION.getPositionByDep(department_id, function (json) {
            let html = '<option value="">' + string_null + '</option>';
            if (typeof json.data !== 'undefined') {
                for (let i in json.data) {
                    let position = json.data[i];
                    let selected = '';
                    if (_code && _code == position._id) {
                        selected = ' selected';
                    }
                    if (typeof position.name !== 'undefined') {
                        html += '<option value="' + position._id + '" ' + selected + '>' + position.name + '</option>';
                    }
                    console.log(position)
                }
            }
            jQuery(select_element).attr('readonly', false).html(html).select2();
        });
    }
};


"use strict";

$(window).on('load',function () {
    $('.js-select2-custom').siblings('.select2-container').addClass('border-0');
    $('.js-select2-custom').siblings('.select2-container').find('.border-0').removeClass('border-0').addClass('border');
});


try {
    let inputElement = document.querySelector('input[name="deliveryman_charge"]');
    if (inputElement) {
        inputElement.addEventListener('wheel', function(event) {
            event.preventDefault();
        });
    }
} catch (e) {
}

$("#filter").change(function() {
    let val = $(this).val();
    if(val === 'admin'){
        $('#seller_id_area').fadeOut();
        $('#seller_id').val('all');
    }else{
        $('#seller_id_area').fadeIn();
        $('#seller_id').val('all');
    }
    if(val === 'seller'){
        $('#seller_id_inhouse').fadeOut();
    }else{
        $('#seller_id_inhouse').fadeIn();
    }
});

function handleCustomDateVisibility() {
    let dateType = $("#date_type").val();
    let subType = $("#custom_date_type").val();

    if (dateType === 'custom_date') {
        $('#custom_date_type_div').show();

        if (subType === 'month') {
            $('#from_div, #to_div').hide();
            $('#month_div, #year_div').show();
            $('#from_date, #to_date').removeAttr('required');
            $('#custom_month, #custom_year').attr('required', 'required');
        } else if (subType === 'year') {
            $('#from_div, #to_div, #month_div').hide();
            $('#year_div').show();
            $('#from_date, #to_date, #custom_month').removeAttr('required');
            $('#custom_year').attr('required', 'required');
        } else {
            $('#from_div, #to_div').show();
            $('#month_div, #year_div').hide();
            $('#from_date, #to_date').attr('required', 'required');
            $('#custom_month, #custom_year').removeAttr('required');
        }
    } else {
        $('#custom_date_type_div, #from_div, #to_div, #month_div, #year_div').hide();
        $('#from_date, #to_date, #custom_month, #custom_year').removeAttr('required');
    }
}

$("#date_type, #custom_date_type").change(function() {
    handleCustomDateVisibility();
});
handleCustomDateVisibility();

$('#from_date,#to_date').change(function () {
    let fr = $('#from_date').val();
    let to = $('#to_date').val();
    if(fr != ''){
        $('#to_date').attr('required','required');
    }
    if(to != ''){
        $('#from_date').attr('required','required');
    }
    if (fr != '' && to != '') {
        if (fr > to) {
            $('#from_date').val('');
            $('#to_date').val('');
            toastMagic.error($('#get-date-range-message').data('error'));
        }
    }

})

$("#customer_id_value").on('change', function () {
    $('#customer_id').empty().val($(this).val());
});

var data_example_url = $('#js-data-example-ajax-url').data('url');

$('.js-data-example-ajax').select2({
    placeholder: 'Select your option',
    dropdownParent: $('#offcanvasOrderFilter'),
    ajax: {
        url: data_example_url,
        data: function (params) {
            return {
                q: params.term,
                page: params.page || 1
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        transport: function (params, success, failure) {
            let $request = $.ajax(params);
            $request.then(function (data) {
                success(data);
            });
            $request.fail(function (jqXHR, textStatus, errorThrown) {
                failure();
            });

            return $request;
        }
    }
});


$(document).ready(function () {
    let delivery_type = $("#delivery-type").data('type');
    if (delivery_type === 'self_delivery') {
        $('.choose_delivery_man').show();
        $('#by_third_party_delivery_service_info').hide();
    } else if (delivery_type === 'third_party_delivery') {
        $('.choose_delivery_man').hide();
        $('#by_third_party_delivery_service_info').show();
    } else {
        $('.choose_delivery_man').hide();
        $('#by_third_party_delivery_service_info').hide();
    }
});

$('.payment-status-alert').on('click',function (){
    toastMagic.info($('#payment-status-alert-message').data('message'));
})

$(".payment-status").on('click', function (e) {
    e.preventDefault();
    let id = $(this).data('id');
    let value = $(this).val();
    Swal.fire({
        title: $("#payment-status-message").data('title'),
        text: $("#payment-status-message").data('message'),
        showCancelButton: true,
        confirmButtonColor: '#377dff',
        cancelButtonColor: '#dd3333',
        confirmButtonText: $("#message-status-confirm-text").data('text'),
        cancelButtonText: $("#message-status-cancel-text").data('text'),
    }).then((result) => {
        if (value == 'paid') {
            value = 'unpaid'
        } else {
            value = 'paid'
        }
        if (result.value) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: $("#payment-status-url").data('url'),
                method: 'POST',
                data: {
                    "id": id,
                    "payment_status": value
                },
                success: function (response) {
                    if (response?.status) {
                        toastMagic.success(response?.message);
                    } else {
                        toastMagic.error(response?.message);
                    }

                    setTimeout(() => {
                        location.reload();
                    }, 2500)
                }
            });
        }
    })
});

$("#order_status").on('change', function (e) {
    let value = $(this).val();
    Swal.fire({
        title: $("#message-status-title-text").data('text'),
        text: $("#message-status-subtitle-text").data('text'),
        showCancelButton: true,
        icon: "warning",
        confirmButtonColor: '#377dff',
        cancelButtonColor: '#dd3333',
        confirmButtonText: $("#message-status-confirm-text").data('text'),
        cancelButtonText: $("#message-status-cancel-text").data('text'),
    }).then((result) => {
        if (result.value) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: $("#order-status-url").data('url'),
                method: 'POST',
                data: {
                    "id": $(this).data('id'),
                    "order_status": value
                },
                success: function (response) {
                    if (response?.status) {
                        toastMagic.success(response?.message);
                    } else {
                        toastMagic.error(response?.message);
                    }

                    setTimeout(() => {
                        location.reload();
                    }, 2500)
                }
            });
        }
    })
});

$("#choose_delivery_type").on('change', function () {
    let value = $(this).val();
    if (value === 'self_delivery') {
        $('.choose_delivery_man').show();
        $('#by_third_party_delivery_service_info').hide();
    } else if (value === 'third_party_delivery') {
        $('.choose_delivery_man').hide();
        $('#by_third_party_delivery_service_info').show();
        $('#third_party_delivery_service_modal').modal("show");
    } else {
        $('.choose_delivery_man').hide();
        $('#by_third_party_delivery_service_info').hide();
    }

});

$("#addDeliveryMan").on('change', function () {
    let id = $(this).val();
    $.ajax({
        type: "GET",
        url: $("#add-delivery-man-url").data('url') + id,
        data: {
            'order_id': $(this).data('order-id'),
            'delivery_man_id': id
        },
        success: function (data) {
            if (data.status == true) {
                toastMagic.success($("#message-deliveryman-add-success-text").data('text'));
                location.reload();
            } else {
                toastMagic.error($("#message-deliveryman-add-error-text").data('text'));
            }
        },
        error: function () {
            toastMagic.error($("#message-deliveryman-add-invalid-text").data('text'));
        }
    });
});
$('input[name=deliveryman_charge]').on('keyup',function(event) {
    if (event.which === 13) {
        let value = $(this);
        amountDateUpdate(value);
    }
});

$(".deliveryman-charge").on('click', function () {
    let value = $('input[name=deliveryman_charge]');
    amountDateUpdate(value);
});
$('.deliveryman-charge-alert').on('click',function (){
    toastMagic.info($('#deliveryman-charge-alert-message').data('message'))
})

$("#expected_delivery_date").on('change', function () {
    amountDateUpdate(this);
});

function amountDateUpdate(t){
    let field_name = $(t).attr('name');
    let field_val = $(t).val();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    $.ajax({
        url: $("#add-date-update-url").data('url'),
        method: 'POST',
        data: {
            'order_id': $(t).data('order-id'),
            'field_name': field_name,
            'field_val': field_val
        },
        success: function (data) {
            if (data.status == true) {
                toastMagic.success(data.message);
                location.reload();
            } else {
                toastMagic.error($("#message-deliveryman-charge-error-text").data('text'));
            }
        },
        error: function () {
            toastMagic.error($("#message-deliveryman-charge-invalid-text").data('text'));
        }
    });
}

/** shipping address  map */
async function shippingAddressMap() {
    let latitude = $("#shipping-latitude").data('latitude');
    let longitude = $("#shipping-longitude").data('longitude');
    let myLatLng = {
        lat: latitude,
        lng: longitude
    };
    const { Map } = await google.maps.importLibrary("maps");
    const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
    const map = new google.maps.Map(document.getElementById("location_map_canvas_shipping"), {
        center: {
            lat: latitude,
            lng: longitude
        },
        zoom: 13,
        mapId: 'roadmap'
    });

    var marker = new AdvancedMarkerElement({
        map,
        position: myLatLng,

    });

    marker.setMap(map);
    var geocoder = geocoder = new google.maps.Geocoder();
    google.maps.event.addListener(map, 'click', function (mapsMouseEvent) {
        var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2);
        var coordinates = JSON.parse(coordinates);
        var latlng = new google.maps.LatLng(coordinates['lat'], coordinates['lng']);
        marker.position={lat:coordinates['lat'], lng:coordinates['lng']};
        map.panTo(latlng);

        document.getElementById('latitude').value = coordinates['lat'];
        document.getElementById('longitude').value = coordinates['lng'];

        geocoder.geocode({'latLng': latlng}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[1]) {
                    document.getElementById('address').value = results[1].formatted_address;
                    console.log(results[1].formatted_address);
                }
            }
        });
    });

    const input = document.getElementById("pac-input");
    const searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
    map.addListener("bounds_changed", () => {
        searchBox.setBounds(map.getBounds());
    });
    let markers = [];
    searchBox.addListener("places_changed", () => {
        const places = searchBox.getPlaces();

        if (places.length == 0) {
            return;
        }
        markers.forEach((marker) => {
            marker.setMap(null);
        });
        markers = [];
        const bounds = new google.maps.LatLngBounds();
        places.forEach((place) => {
            if (!place.geometry || !place.geometry.location) {
                return;
            }
            var mrkr = new AdvancedMarkerElement({
                map,
                title: place.name,
                position: place.geometry.location,
            });

            google.maps.event.addListener(mrkr, "click", function (event) {
                document.getElementById('latitude').value = this.position.lat();
                document.getElementById('longitude').value = this.position.lng();

            });

            markers.push(mrkr);

            if (place.geometry.viewport) {
                bounds.union(place.geometry.viewport);
            } else {
                bounds.extend(place.geometry.location);
            }
        });
        map.fitBounds(bounds);
    });
};

$(document).on("keydown", "input", function (e) {
    if (e.which == 13) e.preventDefault();
});

/** billing address  map */
async function billingAddressMap() {
    let latitude = $("#billing-latitude").data('latitude');
    let longitude = $("#billing-longitude").data('longitude');
    var myLatLng = {
        lat: latitude,
        lng: longitude
    };
    const { Map } = await google.maps.importLibrary("maps");
    const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
    const map = new google.maps.Map(document.getElementById("location_map_canvas_billing"), {
        center: {lat: latitude, lng: longitude},
        zoom: 13,
        mapId: 'roadmap'
    });

    var marker = new AdvancedMarkerElement({
        map,
        position: myLatLng,
    });

    marker.setMap(map);
    var geocoder = geocoder = new google.maps.Geocoder();
    google.maps.event.addListener(map, 'click', function (mapsMouseEvent) {
        var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2);
        var coordinates = JSON.parse(coordinates);
        var latlng = new google.maps.LatLng(coordinates['lat'], coordinates['lng']);
        marker.position = {lat:coordinates['lat'], lng:coordinates['lng']};
        map.panTo(latlng);

        document.getElementById('billing_latitude').value = coordinates['lat'];
        document.getElementById('billing_longitude').value = coordinates['lng'];

        geocoder.geocode({'latLng': latlng}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[1]) {
                    document.getElementById('billing_address').value = results[1].formatted_address;
                }
            }
        });
    });

    const input = document.getElementById("billing-pac-input");
    const searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
    map.addListener("bounds_changed", () => {
        searchBox.setBounds(map.getBounds());
    });
    let markers = [];
    searchBox.addListener("places_changed", () => {
        const places = searchBox.getPlaces();

        if (places.length == 0) {
            return;
        }
        markers.forEach((marker) => {
            marker.setMap(null);
        });
        markers = [];
        const bounds = new google.maps.LatLngBounds();
        places.forEach((place) => {
            if (!place.geometry || !place.geometry.location) {
                return;
            }
            var mrkr = new AdvancedMarkerElement({
                map,
                title: place.name,
                position: place.geometry.location,
            });

            google.maps.event.addListener(mrkr, "click", function (event) {
                document.getElementById('latitude').value = this.position.lat();
                document.getElementById('longitude').value = this.position.lng();

            });

            markers.push(mrkr);

            if (place.geometry.viewport) {
                // Only geocodes have viewport.
                bounds.union(place.geometry.viewport);
            } else {
                bounds.extend(place.geometry.location);
            }
        });
        map.fitBounds(bounds);
    });
};

$(document).on("keydown", "input", function (e) {
    if (e.which == 13) e.preventDefault();
});

async function locationShowingMap() {
    let latitude = $("#shipping-latitude").data('latitude');
    let longitude = $("#shipping-longitude").data('longitude');
    let myLatLng = {
        lat: latitude,
        lng: longitude
    };
    const { Map } = await google.maps.importLibrary("maps");
    const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
        const map = new google.maps.Map(document.getElementById("location_map_canvas"), {
            center: {
                lat: latitude,
                lng: longitude
            },
            zoom: 13,
            mapId: 'roadmap'
        });

        var marker = new AdvancedMarkerElement({
            position: myLatLng,
            map: map,
        });

    marker.setMap(map);
    var geocoder = geocoder = new google.maps.Geocoder();
    google.maps.event.addListener(map, 'click', function (mapsMouseEvent) {
        var latlng = new google.maps.LatLng(latitude, longitude);
        marker.position = {lat:latitude,lng:longitude};
        map.panTo(latlng);
    });
}

/*End Show location on map*/

async function mapCallBackFunction() {
    shippingAddressMap();
    billingAddressMap();
    locationShowingMap();
}


$(".readUrl").on('change', function () {
    let input = $(this).val();
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = (e) => {
            let imgData = e.target.result;
            let imgName = input.files[0].name;
            input.setAttribute("data-title", imgName);
        }
        reader.readAsDataURL(input.files[0]);
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const vendorRadio = document.getElementById("inlineRadioVendor");
    const allRadio = document.getElementById("inlineRadioAll");
    const adminRadio = document.getElementById("inlineRadioInhouse");
    const vendorSection = document.querySelector(".vendor-list-dropdown-section");

    if (!vendorRadio || !allRadio || !adminRadio || !vendorSection) {
        return;
    }

    function toggleOrderListVendorSection() {
        if (vendorRadio.checked) {
            vendorSection.classList.remove("d--none");
        } else {
            vendorSection.classList.add("d--none");
        }
    }
    toggleOrderListVendorSection();
    vendorRadio.addEventListener("change", toggleOrderListVendorSection);
    allRadio.addEventListener("change", toggleOrderListVendorSection);
    adminRadio.addEventListener("change", toggleOrderListVendorSection);
});

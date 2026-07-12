(function () {
    var region = document.querySelector('[data-address-region]');
    var province = document.querySelector('[data-address-province]');
    var city = document.querySelector('[data-address-city]');

    if (!region || !province || !city || !window.rhymioAddressOptions) {
        return;
    }

    function fillSelect(select, values, placeholder, selectedValue) {
        select.innerHTML = '';
        var placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = placeholder;
        placeholderOption.disabled = true;
        placeholderOption.selected = !selectedValue;
        select.appendChild(placeholderOption);

        values.forEach(function (value) {
            var option = document.createElement('option');
            option.value = value;
            option.textContent = value;
            option.selected = value === selectedValue;
            select.appendChild(option);
        });
        select.disabled = values.length === 0;
    }

    function loadCities(selectedValue) {
        var provinces = window.rhymioAddressOptions[region.value] || {};
        fillSelect(city, provinces[province.value] || [], 'Select city or municipality', selectedValue || '');
    }

    function loadProvinces(selectedProvince, selectedCity) {
        var provinces = window.rhymioAddressOptions[region.value] || {};
        fillSelect(province, Object.keys(provinces), 'Select province', selectedProvince || '');
        loadCities(selectedCity || '');
    }

    region.addEventListener('change', function () {
        loadProvinces('', '');
    });
    province.addEventListener('change', function () {
        loadCities('');
    });

    loadProvinces(province.getAttribute('data-selected'), city.getAttribute('data-selected'));
}());

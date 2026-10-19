let currentStep = 1;
let itemIndex = 0;
let messageCounts = {};

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const visionRoute = document.querySelector('meta[name="vision-route"]')?.getAttribute('content') || '/api/vision/extract-colors';

// Admin closed/off dates configuration (YYYY-MM-DD format)
window.closedDates = window.closedDates || [];

// OpenFreeMap & MapLibre GL JS instances
let map;
let userMarker;
let mainShopMarker;
let sanJoseShopMarker;

// Store Branch Coordinates
const MAIN_SHOP_COORDS = { lat: 15.186235193924075, lng: 120.95964131143174 };
const SAN_JOSE_COORDS = { lat: 15.147108923134585, lng: 120.97994960855327 };

let selectedDateVal = null;
let currCalDate = new Date();

let selHour = "08";
let selMin = "00";
let selAmpm = "AM";
let currentClockMode = 'hour'; // 'hour' or 'minute'

document.addEventListener("DOMContentLoaded", () => {
    initZoomModal();
    initOpenFreeMap();

    const ticketForm = document.getElementById('ticketForm');
    if (ticketForm) {
        ticketForm.addEventListener('submit', (e) => {
            document.querySelectorAll('.input-error-highlight').forEach(el => el.classList.remove('input-error-highlight'));
            document.querySelectorAll('.inline-field-error').forEach(el => el.remove());

            let firstErrorElement = null;

            // Validate Customer Location (Step 1)
            const locationInput = document.getElementById('customerLocationInput');
            if (!locationInput || !locationInput.value.trim()) {
                e.preventDefault();
                goToStep(1);
                if (locationInput) {
                    locationInput.classList.add('input-error-highlight');
                    const err = document.createElement('span');
                    err.className = 'inline-field-error';
                    err.innerText = '⚠️ Please enter or select your location on the map.';
                    locationInput.parentElement.appendChild(err);
                    firstErrorElement = locationInput;
                }
            }

            // Validate Items Container (Step 3)
            const itemsContainer = document.getElementById('itemsContainer');
            if (!firstErrorElement && itemsContainer && itemsContainer.children.length === 0) {
                e.preventDefault();
                goToStep(2);
                alert("Please select at least one item before submitting.");
                return;
            }

            // Validate Pickup Date (Step 4)
            const pickupDateInput = document.getElementById('hiddenDate');
            const wrapPickupDate = document.getElementById('wrap_pickup_date');
            if (!pickupDateInput || !pickupDateInput.value.trim()) {
                e.preventDefault();
                if (wrapPickupDate) {
                    wrapPickupDate.classList.add('input-error-highlight');
                    const err = document.createElement('span');
                    err.className = 'inline-field-error';
                    err.innerText = '⚠️ Please select a valid pickup date.';
                    wrapPickupDate.parentElement.appendChild(err);
                    if (!firstErrorElement) firstErrorElement = wrapPickupDate;
                }
            }

            if (firstErrorElement) {
                firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }

    const locInput = document.getElementById('customerLocationInput');
    if (locInput) {
        let debounceTimer;
        locInput.addEventListener('input', (e) => {
            clearTimeout(debounceTimer);
            const query = e.target.value.trim();
            if (query.length > 2) {
                debounceTimer = setTimeout(() => searchAddressNominatim(query), 400);
            } else {
                hideSearchPopover();
            }
        });
    }

    const btnGeo = document.getElementById('btnUseCurrentLocation');
    if (btnGeo) {
        btnGeo.onclick = useCurrentLocation;
    }

    const triggerDate = document.getElementById('trigger_pickup_date');
    const triggerTime = document.getElementById('trigger_pickup_time');
    const btnPrevMonth = document.getElementById('btnPrevMonth');
    const btnNextMonth = document.getElementById('btnNextMonth');

    if (triggerDate) triggerDate.onclick = () => toggleDropdown('wrap_pickup_date');
    if (triggerTime) {
        triggerTime.onclick = () => {
            currentClockMode = 'hour';
            renderTimeSelectors();
            toggleDropdown('wrap_pickup_time');
        };
    }
    if (btnPrevMonth) btnPrevMonth.onclick = () => changeMonth(-1);
    if (btnNextMonth) btnNextMonth.onclick = () => changeMonth(1);

    const btnCake = document.getElementById('btnAddCake');
    const btnCup = document.getElementById('btnAddCupcakes');
    const btnLoaf = document.getElementById('btnAddLoaf');
    const btnRound = document.getElementById('btnAddRound');

    if (btnCake) btnCake.onclick = () => addItemCard('custom_cake');
    if (btnCup) btnCup.onclick = () => addItemCard('cupcakes');
    if (btnLoaf) btnLoaf.onclick = () => addItemCard('giveaway_loaf');
    if (btnRound) btnRound.onclick = () => addItemCard('giveaway_round');

    renderCalendar();
    renderTimeSelectors();

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown-wrapper') && !e.target.closest('.duo-dropdown-wrapper')) {
            closeAllDropdowns();
        }
        if (!e.target.closest('.search-input-wrapper')) {
            hideSearchPopover();
        }
    });
});

// =========================================================================
// OPENFREEMAP (MAPLIBRE GL JS) INTEGRATION
// =========================================================================
function initOpenFreeMap() {
    const mapContainer = document.getElementById('openFreeMap');
    if (!mapContainer) return;

    const defaultCenter = [(MAIN_SHOP_COORDS.lng + SAN_JOSE_COORDS.lng) / 2, (MAIN_SHOP_COORDS.lat + SAN_JOSE_COORDS.lat) / 2];

    map = new maplibregl.Map({
        container: 'openFreeMap',
        style: 'https://tiles.openfreemap.org/styles/liberty',
        center: defaultCenter,
        zoom: 13
    });

    map.addControl(new maplibregl.NavigationControl(), 'top-right');

    const elUser = document.createElement('div');
    elUser.className = 'map-marker-user';
    elUser.title = 'Drag to your location pin';

    userMarker = new maplibregl.Marker({ element: elUser, draggable: true })
        .setLngLat(defaultCenter)
        .addTo(map);

    userMarker.on('dragend', () => {
        const lngLat = userMarker.getLngLat();
        updateCoordinates(lngLat.lat, lngLat.lng);
        reverseGeocodeOpenStreetMap(lngLat.lat, lngLat.lng);
        calculateNearestBranch(lngLat.lat, lngLat.lng);
    });

    const elMain = document.createElement('div');
    elMain.className = 'map-marker-shop';
    elMain.innerHTML = '🏬';
    elMain.title = 'Main Shop';
    mainShopMarker = new maplibregl.Marker({ element: elMain })
        .setLngLat([MAIN_SHOP_COORDS.lng, MAIN_SHOP_COORDS.lat])
        .setPopup(new maplibregl.Popup().setHTML('<strong>Main Shop</strong><br>San Miguel, Bulacan'))
        .addTo(map);

    const elSanJose = document.createElement('div');
    elSanJose.className = 'map-marker-shop';
    elSanJose.innerHTML = '🏪';
    elSanJose.title = 'San Jose Branch';
    sanJoseShopMarker = new maplibregl.Marker({ element: elSanJose })
        .setLngLat([SAN_JOSE_COORDS.lng, SAN_JOSE_COORDS.lat])
        .setPopup(new maplibregl.Popup().setHTML('<strong>San Jose Branch</strong><br>Norberto St., San Miguel'))
        .addTo(map);

    map.on('click', (e) => {
        const { lng, lat } = e.lngLat;
        userMarker.setLngLat([lng, lat]);
        updateCoordinates(lat, lng);
        reverseGeocodeOpenStreetMap(lat, lng);
        calculateNearestBranch(lat, lng);
    });

    updateCoordinates(defaultCenter[1], defaultCenter[0]);
    calculateNearestBranch(defaultCenter[1], defaultCenter[0]);
}

function updateCoordinates(lat, lng) {
    const latInput = document.getElementById('latInput');
    const lngInput = document.getElementById('lngInput');
    if (latInput) latInput.value = lat;
    if (lngInput) lngInput.value = lng;
}

function calculateDistanceKm(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

function calculateNearestBranch(userLat, userLng) {
    const distMain = calculateDistanceKm(userLat, userLng, MAIN_SHOP_COORDS.lat, MAIN_SHOP_COORDS.lng);
    const distSanJose = calculateDistanceKm(userLat, userLng, SAN_JOSE_COORDS.lat, SAN_JOSE_COORDS.lng);

    const tagMain = document.getElementById('distTag_main');
    const tagSanJose = document.getElementById('distTag_sanjose');
    const badgeMain = document.getElementById('recBadge_main');
    const badgeSanJose = document.getElementById('recBadge_sanjose');

    if (tagMain) tagMain.textContent = `📍 Approx. ${distMain.toFixed(2)} km from your pin`;
    if (tagSanJose) tagSanJose.textContent = `📍 Approx. ${distSanJose.toFixed(2)} km from your pin`;

    if (distMain <= distSanJose) {
        if (badgeMain) badgeMain.classList.remove('hidden-element');
        if (badgeSanJose) badgeSanJose.classList.add('hidden-element');
        selectShop('main_shop', false);
    } else {
        if (badgeSanJose) badgeSanJose.classList.remove('hidden-element');
        if (badgeMain) badgeMain.classList.add('hidden-element');
        selectShop('san_jose', false);
    }
}

function selectShop(branchKey, panMap = true) {
    const input = document.getElementById('pickupBranchInput');
    if (input) input.value = branchKey;

    const cardMain = document.getElementById('shopCard_main');
    const cardSanJose = document.getElementById('shopCard_sanjose');

    if (branchKey === 'main_shop') {
        if (cardMain) cardMain.classList.add('active');
        if (cardSanJose) cardSanJose.classList.remove('active');
        if (panMap && map) map.flyTo({ center: [MAIN_SHOP_COORDS.lng, MAIN_SHOP_COORDS.lat], zoom: 16 });
    } else {
        if (cardSanJose) cardSanJose.classList.add('active');
        if (cardMain) cardMain.classList.remove('active');
        if (panMap && map) map.flyTo({ center: [SAN_JOSE_COORDS.lng, SAN_JOSE_COORDS.lat], zoom: 16 });
    }
}

async function searchAddressNominatim(query) {
    const popover = document.getElementById('searchResultsPopover');
    if (!popover) return;

    try {
        const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query + ', Bulacan, Philippines')}&limit=5`);
        const data = await res.json();

        popover.textContent = '';
        if (data && data.length > 0) {
            data.forEach(item => {
                const div = document.createElement('div');
                div.className = 'search-result-item';
                div.textContent = item.display_name;
                div.onclick = () => {
                    const lat = parseFloat(item.lat);
                    const lon = parseFloat(item.lon);
                    
                    const input = document.getElementById('customerLocationInput');
                    if (input) input.value = item.display_name;

                    if (map && userMarker) {
                        map.flyTo({ center: [lon, lat], zoom: 16 });
                        userMarker.setLngLat([lon, lat]);
                    }

                    updateCoordinates(lat, lon);
                    calculateNearestBranch(lat, lon);
                    hideSearchPopover();
                };
                popover.appendChild(div);
            });
            popover.classList.add('is-active');
        } else {
            hideSearchPopover();
        }
    } catch (err) {
        hideSearchPopover();
    }
}

function hideSearchPopover() {
    const popover = document.getElementById('searchResultsPopover');
    if (popover) {
        popover.classList.remove('is-active');
        popover.textContent = '';
    }
}

async function reverseGeocodeOpenStreetMap(lat, lng) {
    try {
        const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`);
        const data = await res.json();
        const input = document.getElementById('customerLocationInput');
        if (input && data && data.display_name) {
            input.value = data.display_name;
        }
    } catch (e) {
        console.error("Geocoding failed", e);
    }
}

function useCurrentLocation() {
    const locInput = document.getElementById('customerLocationInput');
    const btnGeo = document.getElementById('btnUseCurrentLocation');

    if (!navigator.geolocation) {
        alert("Geolocation is not supported by your browser.");
        return;
    }

    if (btnGeo) {
        btnGeo.disabled = true;
        btnGeo.textContent = "📍 Locating...";
    }

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            if (map && userMarker) {
                map.flyTo({ center: [lng, lat], zoom: 16 });
                userMarker.setLngLat([lng, lat]);
            }

            updateCoordinates(lat, lng);
            reverseGeocodeOpenStreetMap(lat, lng);
            calculateNearestBranch(lat, lng);

            if (btnGeo) {
                btnGeo.disabled = false;
                btnGeo.textContent = "📍 Use Current Location";
            }
        },
        (error) => {
            if (btnGeo) {
                btnGeo.disabled = false;
                btnGeo.textContent = "📍 Use Current Location";
            }
            alert("Unable to retrieve location. Please search manually in the field above.");
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}

function selectInitialCategory(type) {
    addItemCard(type);
    goToStep(3);
}

function goToStep(stepNum) {
    if (stepNum === 2 || stepNum === 3 || stepNum === 4) {
        const locInput = document.getElementById('customerLocationInput');
        if (!locInput || !locInput.value.trim()) {
            alert("Please provide your location in Step 1 first.");
            goToStep(1);
            return;
        }
    }

    if (stepNum === 3 || stepNum === 4) {
        const itemsContainer = document.getElementById('itemsContainer');
        if (!itemsContainer || itemsContainer.children.length === 0) {
            alert("Please pick an initial product category first.");
            goToStep(2);
            return;
        }
    }

    currentStep = stepNum;

    document.querySelectorAll('.wizard-step-panel').forEach(panel => {
        panel.classList.remove('active-step');
    });
    const targetPanel = document.getElementById('wizardStep' + stepNum);
    if (targetPanel) targetPanel.classList.add('active-step');

    for (let i = 1; i <= 4; i++) {
        const stepIndicator = document.getElementById('indicatorStep' + i);
        if (stepIndicator) {
            stepIndicator.classList.remove('active', 'completed');
            if (i === stepNum) {
                stepIndicator.classList.add('active');
            } else if (i < stepNum) {
                stepIndicator.classList.add('completed');
            }
        }
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function initZoomModal() {
    if (document.getElementById('imageZoomModal')) return;

    const modal = document.createElement('div');
    modal.id = 'imageZoomModal';
    modal.className = 'image-zoom-modal';
    modal.onclick = (e) => {
        if (e.target === modal) closeZoomModal();
    };

    const container = document.createElement('div');
    container.className = 'modal-img-container';

    const closeBtn = document.createElement('button');
    closeBtn.type = 'button';
    closeBtn.className = 'btn-modal-close';
    closeBtn.title = 'Close Preview';
    closeBtn.onclick = closeZoomModal;
    closeBtn.innerHTML = `<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`;

    const img = document.createElement('img');
    img.id = 'zoomModalImg';
    img.className = 'modal-img-preview';
    img.alt = 'Reference Image Zoomed Preview';

    container.appendChild(closeBtn);
    container.appendChild(img);
    modal.appendChild(container);
    document.body.appendChild(modal);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeZoomModal();
    });
}

function openZoomModal(imgSrc) {
    const modal = document.getElementById('imageZoomModal');
    const modalImg = document.getElementById('zoomModalImg');
    if (modal && modalImg && imgSrc) {
        modalImg.src = imgSrc;
        modal.classList.add('open');
    }
}

function closeZoomModal() {
    const modal = document.getElementById('imageZoomModal');
    if (modal) {
        modal.classList.remove('open');
    }
}

function toggleDropdown(wrapperId) {
    const wrapper = document.getElementById(wrapperId);
    if (!wrapper) return;
    const isOpen = wrapper.classList.contains('open');
    closeAllDropdowns();
    if (!isOpen) wrapper.classList.add('open');
}

function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-wrapper, .duo-dropdown-wrapper').forEach(el => el.classList.remove('open'));
    document.querySelectorAll('.duo-dropdown-popover').forEach(p => p.classList.remove('show'));
}

function createCustomDropdownNode(wrapperId, hiddenName, options, defaultValue, onSelectCallback) {
    const wrapper = document.createElement('div');
    wrapper.className = 'dropdown-wrapper';
    wrapper.id = wrapperId;

    const trigger = document.createElement('div');
    trigger.className = 'duo-picker-trigger';
    trigger.onclick = () => toggleDropdown(wrapperId);

    const labelSpan = document.createElement('span');
    labelSpan.id = 'label_' + wrapperId;
    let defaultLabel = defaultValue;
    options.forEach(o => { if (o.value === defaultValue) defaultLabel = o.label; });
    labelSpan.textContent = defaultLabel;

    const caret = document.createElement('div');
    caret.className = 'dropdown-caret';

    trigger.appendChild(labelSpan);
    trigger.appendChild(caret);

    const popover = document.createElement('div');
    popover.className = 'duo-dropdown-popover';

    options.forEach(opt => {
        const optItem = document.createElement('div');
        optItem.className = 'duo-option-item ' + (opt.value === defaultValue ? 'selected' : '');
        
        const itemSpan = document.createElement('span');
        itemSpan.textContent = opt.label;
        optItem.appendChild(itemSpan);

        optItem.onclick = (e) => {
            selectCustomOption(wrapperId, hiddenName, opt.value, opt.label, e.currentTarget);
            if (onSelectCallback) onSelectCallback(opt.value);
        };

        popover.appendChild(optItem);
    });

    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = hiddenName;
    hiddenInput.id = 'hidden_' + wrapperId;
    hiddenInput.value = defaultValue;

    wrapper.appendChild(trigger);
    wrapper.appendChild(popover);
    wrapper.appendChild(hiddenInput);

    return wrapper;
}

function selectCustomOption(wrapperId, hiddenName, value, label, targetElement) {
    const labelElem = document.getElementById('label_' + wrapperId);
    const hiddenElem = document.getElementById('hidden_' + wrapperId);
    if (labelElem) labelElem.textContent = label;
    if (hiddenElem) hiddenElem.value = value;
    
    const wrapper = document.getElementById(wrapperId);
    if (wrapper) {
        wrapper.querySelectorAll('.duo-option-item').forEach(el => el.classList.remove('selected'));
        if (targetElement) {
            targetElement.classList.add('selected');
        }
    }
    closeAllDropdowns();
}

function renderCalendar() {
    const grid = document.getElementById('calDatesGrid');
    if (!grid) return;
    grid.textContent = '';
    
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const titleElem = document.getElementById('calMonthYearTitle');
    if (titleElem) {
        titleElem.textContent = monthNames[currCalDate.getMonth()] + ' ' + currCalDate.getFullYear();
    }

    const minLeadDate = new Date();
    minLeadDate.setDate(minLeadDate.getDate() + 2);
    minLeadDate.setHours(0, 0, 0, 0);

    const firstDay = new Date(currCalDate.getFullYear(), currCalDate.getMonth(), 1).getDay();
    const daysInMonth = new Date(currCalDate.getFullYear(), currCalDate.getMonth() + 1, 0).getDate();

    for (let i = 0; i < firstDay; i++) grid.appendChild(document.createElement('div'));

    for (let day = 1; day <= daysInMonth; day++) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'cal-date-btn';
        btn.textContent = day;

        const iterDate = new Date(currCalDate.getFullYear(), currCalDate.getMonth(), day);
        iterDate.setHours(0, 0, 0, 0);

        const mStr = String(currCalDate.getMonth() + 1).padStart(2, '0');
        const dStrPad = String(day).padStart(2, '0');
        const dStr = `${currCalDate.getFullYear()}-${mStr}-${dStrPad}`;

        const isClosedByAdmin = window.closedDates && window.closedDates.includes(dStr);

        if (iterDate < minLeadDate || isClosedByAdmin) {
            btn.disabled = true;
            btn.style.opacity = '0.3';
            btn.style.cursor = 'not-allowed';
            if (isClosedByAdmin) {
                btn.title = "Date disabled by admin (Fully booked or closed)";
                btn.style.background = "#fee2e2";
                btn.style.color = "#991b1b";
            }
        } else {
            if (selectedDateVal === dStr) btn.classList.add('selected');

            btn.onclick = () => {
                document.querySelectorAll('.cal-date-btn').forEach(b => b.classList.remove('selected'));
                btn.classList.add('selected');
                selectedDateVal = dStr;
                
                const hiddenDate = document.getElementById('hiddenDate');
                const dateTriggerText = document.getElementById('dateTriggerText');
                if (hiddenDate) hiddenDate.value = selectedDateVal;
                if (dateTriggerText) dateTriggerText.textContent = selectedDateVal;

                renderTimeSelectors();
                closeAllDropdowns();
            };
        }

        grid.appendChild(btn);
    }
}

function changeMonth(delta) {
    currCalDate.setMonth(currCalDate.getMonth() + delta);
    renderCalendar();
}

// =========================================================================
// ANALOG CLOCK PICKER & DYNAMIC TIME RULES
// =========================================================================
function renderTimeSelectors() {
    const popover = document.querySelector("#wrap_pickup_time .duo-dropdown-popover");
    if (!popover) return;

    popover.textContent = "";
    popover.className = "duo-dropdown-popover clock-picker-popover";

    // Determine if selected pickup date is Sunday
    let isSunday = false;
    if (selectedDateVal) {
        const parts = selectedDateVal.split("-");
        const selDateObj = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        if (selDateObj.getDay() === 0) {
            isSunday = true;
        }
    }

    // Rules:
    // Sunday: 07, 08, 09, 11 (Strictly AM)
    // Mon-Sat: 07, 08, 09, 10, 11 (AM) & 12, 01, 02, 03, 04, 05 (PM)
    let allowedHours = isSunday 
        ? ["07", "08", "09", "11"] 
        : ["07", "08", "09", "10", "11", "12", "01", "02", "03", "04", "05"];

    if (isSunday) {
        selAmpm = "AM";
        if (!allowedHours.includes(selHour)) {
            selHour = "07";
        }
    } else {
        if (["01", "02", "03", "04", "05"].includes(selHour)) selAmpm = "PM";
        else if (["07", "08", "09", "10", "11"].includes(selHour)) selAmpm = "AM";
        else if (selHour === "12") selAmpm = "PM";
    }

    // Minute restrictions
    let allowedMins = ["00", "30"];
    if (isSunday && selHour === "11") allowedMins = ["00"];
    if (!isSunday && selHour === "07" && selAmpm === "AM") allowedMins = ["30"];
    if (!isSunday && selHour === "05" && selAmpm === "PM") allowedMins = ["00"];

    if (!allowedMins.includes(selMin)) {
        selMin = allowedMins[0];
    }

    // 1. Digital Display Header
    const header = document.createElement("div");
    header.className = "clock-header";

    const displayBox = document.createElement("div");
    displayBox.className = "clock-digital-display";

    const hourBox = document.createElement("button");
    hourBox.type = "button";
    hourBox.className = `clock-digit-box ${currentClockMode === "hour" ? "active" : ""}`;
    hourBox.textContent = selHour;
    hourBox.onclick = (e) => {
        e.stopPropagation();
        currentClockMode = "hour";
        renderTimeSelectors();
    };

    const colon = document.createElement("span");
    colon.className = "clock-colon";
    colon.textContent = ":";

    const minBox = document.createElement("button");
    minBox.type = "button";
    minBox.className = `clock-digit-box ${currentClockMode === "minute" ? "active" : ""}`;
    minBox.textContent = selMin;
    minBox.onclick = (e) => {
        e.stopPropagation();
        currentClockMode = "minute";
        renderTimeSelectors();
    };

    displayBox.appendChild(hourBox);
    displayBox.appendChild(colon);
    displayBox.appendChild(minBox);

    const ampmToggle = document.createElement("div");
    ampmToggle.className = "clock-ampm-toggle";

    const amBtn = document.createElement("button");
    amBtn.type = "button";
    amBtn.className = `ampm-btn ${selAmpm === "AM" ? "active" : ""}`;
    amBtn.textContent = "AM";
    amBtn.disabled = isSunday;
    amBtn.onclick = (e) => {
        e.stopPropagation();
        if (isSunday) return;
        selAmpm = "AM";
        if (["01", "02", "03", "04", "05"].includes(selHour)) selHour = "08";
        renderTimeSelectors();
    };

    const pmBtn = document.createElement("button");
    pmBtn.type = "button";
    pmBtn.className = `ampm-btn ${selAmpm === "PM" ? "active" : ""}`;
    pmBtn.textContent = "PM";
    pmBtn.disabled = isSunday;
    pmBtn.onclick = (e) => {
        e.stopPropagation();
        if (isSunday) return;
        selAmpm = "PM";
        if (["07", "08", "09", "10", "11"].includes(selHour)) selHour = "01";
        renderTimeSelectors();
    };

    ampmToggle.appendChild(amBtn);
    ampmToggle.appendChild(pmBtn);

    header.appendChild(displayBox);
    header.appendChild(ampmToggle);
    popover.appendChild(header);

    // 2. Analog Dial Container
    const dialContainer = document.createElement("div");
    dialContainer.className = "clock-dial-container";

    const dialCircle = document.createElement("div");
    dialCircle.className = "clock-dial-circle";

    const pivot = document.createElement("div");
    pivot.className = "clock-pivot";
    dialCircle.appendChild(pivot);

    if (currentClockMode === "hour") {
        const hoursList = [12, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];
        const selectedInt = parseInt(selHour, 10);
        const angleDeg = (selectedInt % 12) * 30;

        const hand = document.createElement("div");
        hand.className = "clock-hand";
        hand.style.transform = `rotate(${angleDeg}deg)`;
        dialCircle.appendChild(hand);

        hoursList.forEach((h) => {
            const hStr = String(h).padStart(2, "0");
            const hNum = document.createElement("button");
            hNum.type = "button";

            const isAllowed = allowedHours.includes(hStr) &&
                ((selAmpm === "AM" && ["07", "08", "09", "10", "11"].includes(hStr)) ||
                 (selAmpm === "PM" && ["12", "01", "02", "03", "04", "05"].includes(hStr)));

            hNum.className = `clock-num ${selHour === hStr ? "selected" : ""} ${!isAllowed ? "disabled" : ""}`;
            hNum.textContent = h;

            const angle = (h % 12) * 30 * (Math.PI / 180);
            const radius = 95;
            const x = Math.round(radius * Math.sin(angle));
            const y = Math.round(-radius * Math.cos(angle));

            hNum.style.transform = `translate(${x}px, ${y}px)`;

            if (isAllowed) {
                hNum.onclick = (e) => {
                    e.stopPropagation();
                    selHour = hStr;
                    currentClockMode = "minute";
                    renderTimeSelectors();
                };
            } else {
                hNum.disabled = true;
            }

            dialCircle.appendChild(hNum);
        });

    } else {
        const minsList = ["00", "30"];
        const angleDeg = selMin === "00" ? 0 : 180;

        const hand = document.createElement("div");
        hand.className = "clock-hand";
        hand.style.transform = `rotate(${angleDeg}deg)`;
        dialCircle.appendChild(hand);

        minsList.forEach((m) => {
            const mNum = document.createElement("button");
            mNum.type = "button";

            const isAllowed = allowedMins.includes(m);
            mNum.className = `clock-num ${selMin === m ? "selected" : ""} ${!isAllowed ? "disabled" : ""}`;
            mNum.textContent = m;

            const angle = (m === "00" ? 0 : 180) * (Math.PI / 180);
            const radius = 95;
            const x = Math.round(radius * Math.sin(angle));
            const y = Math.round(-radius * Math.cos(angle));

            mNum.style.transform = `translate(${x}px, ${y}px)`;

            if (isAllowed) {
                mNum.onclick = (e) => {
                    e.stopPropagation();
                    selMin = m;
                    renderTimeSelectors();
                };
            } else {
                mNum.disabled = true;
            }

            dialCircle.appendChild(mNum);
        });
    }

    dialContainer.appendChild(dialCircle);
    popover.appendChild(dialContainer);

    // 3. Action Footer Buttons
    const footer = document.createElement("div");
    footer.className = "clock-footer";

    const btnCancel = document.createElement("button");
    btnCancel.type = "button";
    btnCancel.className = "clock-action-btn cancel";
    btnCancel.textContent = "Cancel";
    btnCancel.onclick = (e) => {
        e.stopPropagation();
        closeAllDropdowns();
    };

    const btnOk = document.createElement("button");
    btnOk.type = "button";
    btnOk.className = "clock-action-btn ok";
    btnOk.textContent = "OK";
    btnOk.onclick = (e) => {
        e.stopPropagation();
        updateTimeDisplay();
        closeAllDropdowns();
    };

    footer.appendChild(btnCancel);
    footer.appendChild(btnOk);
    popover.appendChild(footer);

    updateTimeDisplay();
}

function updateTimeDisplay() {
    const formatted = selHour + ':' + selMin + ' ' + selAmpm;
    const hiddenTime = document.getElementById('hiddenTime');
    const timeTriggerText = document.getElementById('timeTriggerText');
    if (hiddenTime) hiddenTime.value = formatted;
    if (timeTriggerText) timeTriggerText.textContent = formatted;
}

function selectCakeShape(idx, shapeVal) {
    const shapeHidden = document.getElementById('hidden_shape_' + idx);
    if (shapeHidden) shapeHidden.value = shapeVal;
    
    const shpGroup = document.getElementById('shapeGroup_' + idx);
    if (shpGroup) {
        shpGroup.querySelectorAll('.btn-duo-outline').forEach(btn => {
            if (btn.dataset.shape === shapeVal) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    updateCakeUI(idx);
}

function selectCakeTier(idx, tierVal) {
    const tierHidden = document.getElementById('hidden_tier_' + idx);
    if (tierHidden) tierHidden.value = tierVal;
    
    const tierGroup = document.getElementById('tiersWrapper_' + idx);
    if (tierGroup) {
        tierGroup.querySelectorAll('.btn-duo-outline').forEach(btn => {
            if (btn.dataset.tier === tierVal) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    updateCakeUI(idx);
}

function createIsometricCakeSVG(tiers, shape) {
    const ns = 'http://www.w3.org/2000/svg';
    const svg = document.createElementNS(ns, 'svg');

    if (shape === 'square' || shape === 'rect') {
        const cuboidSpecs = { 
            '6':     { w: 75,  depthX: 30, depthY: 18, h: 32 },
            '8':     { w: 95,  depthX: 36, depthY: 20, h: 36 },
            '10':    { w: 115, depthX: 42, depthY: 22, h: 40 },
            '12':    { w: 135, depthX: 48, depthY: 24, h: 44 },
            '8x12':  { w: 125, depthX: 40, depthY: 22, h: 36 },
            '10x14': { w: 155, depthX: 48, depthY: 26, h: 40 }
        };

        const sizeKey = tiers[0].size;
        const spec = cuboidSpecs[sizeKey] || { w: 100, depthX: 36, depthY: 20, h: 36 };

        const w = spec.w;
        const h = spec.h;
        const depthX = spec.depthX;
        const depthY = spec.depthY;

        const padX = 14;
        const padY = 16;

        const totalW = w + depthX + (padX * 2);
        const totalH = h + depthY + (padY * 2) + 6;

        svg.setAttribute('viewBox', `0 0 ${totalW} ${totalH}`);
        svg.setAttribute('width', totalW);
        svg.setAttribute('height', totalH);

        const g = document.createElementNS(ns, 'g');

        const boardMargin = 10;
        const bx = padX - boardMargin;
        const by = totalH - padY - 2;
        const bw = w + (boardMargin * 2);
        const bDepthX = depthX + boardMargin + 4;
        const bDepthY = depthY + 4;

        const bBoardTop = document.createElementNS(ns, 'polygon');
        bBoardTop.setAttribute('points', `${bx},${by - 4} ${bx + bw},${by - 4} ${bx + bw + bDepthX},${by - 4 - bDepthY} ${bx + bDepthX},${by - 4 - bDepthY}`);
        bBoardTop.setAttribute('class', 'svg-board-top');

        const bBoardFront = document.createElementNS(ns, 'polygon');
        bBoardFront.setAttribute('points', `${bx},${by - 4} ${bx + bw},${by - 4} ${bx + bw},${by} ${bx},${by}`);
        bBoardFront.setAttribute('class', 'svg-board-front');

        const bBoardRight = document.createElementNS(ns, 'polygon');
        bBoardRight.setAttribute('points', `${bx + bw},${by - 4} ${bx + bw + bDepthX},${by - 4 - bDepthY} ${bx + bw + bDepthX},${by - bDepthY} ${bx + bw},${by}`);
        bBoardRight.setAttribute('class', 'svg-board-side');

        g.appendChild(bBoardTop);
        g.appendChild(bBoardFront);
        g.appendChild(bBoardRight);

        const cx = padX;
        const cy = by - 4;

        const topFace = document.createElementNS(ns, 'polygon');
        topFace.setAttribute('points', `${cx},${cy - h} ${cx + w},${cy - h} ${cx + w + depthX},${cy - h - depthY} ${cx + depthX},${cy - h - depthY}`);
        topFace.setAttribute('class', 'svg-cake-top');

        const frontFace = document.createElementNS(ns, 'polygon');
        frontFace.setAttribute('points', `${cx},${cy - h} ${cx + w},${cy - h} ${cx + w},${cy} ${cx},${cy}`);
        frontFace.setAttribute('class', 'svg-cake-front');

        const rightFace = document.createElementNS(ns, 'polygon');
        rightFace.setAttribute('points', `${cx + w},${cy - h} ${cx + w + depthX},${cy - h - depthY} ${cx + w + depthX},${cy - depthY} ${cx + w},${cy}`);
        rightFace.setAttribute('class', 'svg-cake-side');

        const text = document.createElementNS(ns, 'text');
        text.setAttribute('x', cx + (w / 2));
        text.setAttribute('y', cy - (h / 2) + 12);
        text.setAttribute('text-anchor', 'middle');
        text.setAttribute('class', 'svg-cake-text');
        text.textContent = tiers[0].label || (tiers[0].size + '"');

        g.appendChild(topFace);
        g.appendChild(frontFace);
        g.appendChild(rightFace);
        g.appendChild(text);

        svg.appendChild(g);
        return svg;
    }

    const tierWidths = { '6': 64, '8': 86, '10': 108, '12': 128, '14': 148 };
    const tierHeight = 48;
    const ellipseYFactor = 0.38;

    const bottomSize = tiers[0].size;
    const bottomTierWidth = tierWidths[bottomSize] || 86;
    const boardRx = (bottomTierWidth + 18) / 2;
    const boardRy = boardRx * ellipseYFactor;

    const baseWidth = (boardRx * 2) + 20;
    const topMargin = boardRy + 16;
    const totalHeight = (tiers.length * tierHeight) + boardRy + topMargin + 8;

    svg.setAttribute('viewBox', `0 0 ${baseWidth} ${totalHeight}`);
    svg.setAttribute('width', baseWidth);
    svg.setAttribute('height', totalHeight);

    const g = document.createElementNS(ns, 'g');
    const centerX = baseWidth / 2;
    const boardY = totalHeight - boardRy - 6;

    const boardPath = document.createElementNS(ns, 'path');
    boardPath.setAttribute('d', `M ${centerX - boardRx} ${boardY} A ${boardRx} ${boardRy} 0 0 0 ${centerX + boardRx} ${boardY} v 5 A ${boardRx} ${boardRy} 0 0 1 ${centerX - boardRx} ${boardY + 5} Z`);
    boardPath.setAttribute('class', 'svg-board-side');

    const boardTop = document.createElementNS(ns, 'ellipse');
    boardTop.setAttribute('cx', centerX);
    boardTop.setAttribute('cy', boardY);
    boardTop.setAttribute('rx', boardRx);
    boardTop.setAttribute('ry', boardRy);
    boardTop.setAttribute('class', 'svg-board-top');

    g.appendChild(boardPath);
    g.appendChild(boardTop);

    let currentY = boardY;

    for (let i = 0; i < tiers.length; i++) {
        const tier = tiers[i];
        const width = tierWidths[tier.size] || 76;
        const rx = width / 2;
        const ry = rx * ellipseYFactor;
        const topY = currentY - tierHeight;

        const bodyPath = document.createElementNS(ns, 'path');
        bodyPath.setAttribute('d', `M ${centerX - rx} ${topY} A ${rx} ${ry} 0 0 0 ${centerX + rx} ${topY} v ${tierHeight} A ${rx} ${ry} 0 0 1 ${centerX - rx} ${currentY} Z`);
        bodyPath.setAttribute('class', 'svg-cake-front');

        const topCap = document.createElementNS(ns, 'ellipse');
        topCap.setAttribute('cx', centerX);
        topCap.setAttribute('cy', topY);
        topCap.setAttribute('rx', rx);
        topCap.setAttribute('ry', ry);
        topCap.setAttribute('class', 'svg-cake-top');

        const text = document.createElementNS(ns, 'text');
        text.setAttribute('x', centerX);
        text.setAttribute('y', topY + (tierHeight / 2) + 14);
        text.setAttribute('text-anchor', 'middle');
        text.setAttribute('class', 'svg-cake-text');
        text.textContent = tier.label || (tier.size + '"');

        g.appendChild(bodyPath);
        g.appendChild(topCap);
        g.appendChild(text);

        currentY = topY;
    }

    svg.appendChild(g);
    return svg;
}

function updateCakeUI(idx) {
    const shapeElem = document.getElementById('hidden_shape_' + idx);
    const tierElem = document.getElementById('hidden_tier_' + idx);
    const shapeSelected = shapeElem ? shapeElem.value : 'Round';
    const activeTier = tierElem ? tierElem.value : '1 Tier';
    const sizeContainer = document.getElementById('sizeContainer_' + idx);
    const tiersWrapper = document.getElementById('tiersWrapper_' + idx);

    if (tiersWrapper) {
        if (shapeSelected === 'Rectangle' || shapeSelected === 'Square') {
            tiersWrapper.style.opacity = '0.4'; 
            tiersWrapper.style.pointerEvents = 'none';
            if (tierElem) tierElem.value = '1 Tier';
        } else {
            tiersWrapper.style.opacity = '1'; 
            tiersWrapper.style.pointerEvents = 'auto';
        }
    }

    let optionList = [];
    let currentShapeType = 'round';

    if (shapeSelected === 'Rectangle') {
        currentShapeType = 'rect';
        optionList = [
            { val: '8x12', tiers: [{ size: '8x12', label: '8x12"' }] },
            { val: '10x14', tiers: [{ size: '10x14', label: '10x14"' }] }
        ];
    } else if (shapeSelected === 'Square') {
        currentShapeType = 'square';
        optionList = [
            { val: '6', tiers: [{ size: '6', label: '6"' }] },
            { val: '8', tiers: [{ size: '8', label: '8"' }] },
            { val: '10', tiers: [{ size: '10', label: '10"' }] },
            { val: '12', tiers: [{ size: '12', label: '12"' }] }
        ];
    } else {
        currentShapeType = 'round';
        if (activeTier === '1 Tier') {
            optionList = [
                { val: '6', tiers: [{ size: '6', label: '6"' }] },
                { val: '8', tiers: [{ size: '8', label: '8"' }] },
                { val: '10', tiers: [{ size: '10', label: '10"' }] },
                { val: '12', tiers: [{ size: '12', label: '12"' }] },
                { val: '14', tiers: [{ size: '14', label: '14"' }] }
            ];
        } else if (activeTier === '2 Tiers') {
            optionList = [
                { val: '8', tiers: [{ size: '8', label: '8"' }, { size: '6', label: '6"' }] },
                { val: '10', tiers: [{ size: '10', label: '10"' }, { size: '8', label: '8"' }] },
                { val: '12', tiers: [{ size: '12', label: '12"' }, { size: '10', label: '10"' }] },
                { val: '14', tiers: [{ size: '14', label: '14"' }, { size: '12', label: '12"' }] }
            ];
        } else {
            optionList = [
                { val: '10', tiers: [{ size: '10', label: '10"' }, { size: '8', label: '8"' }, { size: '6', label: '6"' }] },
                { val: '12', tiers: [{ size: '12', label: '12"' }, { size: '10', label: '10"' }, { size: '8', label: '8"' }] },
                { val: '14', tiers: [{ size: '14', label: '14"' }, { size: '10', label: '10"' }, { size: '8', label: '8"' }] }
            ];
        }
    }

    if (sizeContainer) {
        sizeContainer.textContent = '';
        optionList.forEach((opt, i) => {
            const label = document.createElement('label');
            label.className = 'iso-cake-radio';

            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.name = 'items[' + idx + '][size]';
            radio.value = opt.val;
            if (i === 0) radio.checked = true;

            const wrapper = document.createElement('div');
            wrapper.className = 'iso-cake-card';

            const svg = createIsometricCakeSVG(opt.tiers, currentShapeType);
            wrapper.appendChild(svg);

            label.appendChild(radio);
            label.appendChild(wrapper);
            sizeContainer.appendChild(label);
        });
    }
}

function addMessageRow(itemIdx, defaultText, defaultPlacement, defaultColor) {
    if (!messageCounts[itemIdx]) messageCounts[itemIdx] = 0;
    let mIdx = messageCounts[itemIdx]++;

    const container = document.getElementById('messagesContainer_' + itemIdx);
    if (!container) return;

    const row = document.createElement('div');
    row.className = 'message-row';
    row.id = 'msgRow_' + itemIdx + '_' + mIdx;

    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'items[' + itemIdx + '][messages][' + mIdx + '][text]';
    input.className = 'clean-input msg-input';
    input.value = defaultText || '';
    input.placeholder = 'e.g. Happy 18th Birthday Sarah!';

    const select = document.createElement('select');
    select.name = 'items[' + itemIdx + '][messages][' + mIdx + '][placement]';
    select.className = 'clean-input msg-select';
    ['Top', 'Front'].forEach(p => {
        const opt = document.createElement('option');
        opt.value = p;
        opt.textContent = p;
        if ((defaultPlacement || 'Top') === p) opt.selected = true;
        select.appendChild(opt);
    });

    const colorWrapper = document.createElement('div');
    colorWrapper.className = 'color-picker-wrapper msg-color';

    const colorInput = document.createElement('input');
    colorInput.type = 'color';
    colorInput.name = 'items[' + itemIdx + '][messages][' + mIdx + '][color]';
    colorInput.value = defaultColor || '#8A38F5';
    colorInput.className = 'color-picker-input';
    colorWrapper.appendChild(colorInput);

    const deleteBtn = document.createElement('button');
    deleteBtn.type = 'button';
    deleteBtn.className = 'btn-delete';
    deleteBtn.title = 'Remove';
    deleteBtn.onclick = () => removeMessageRow(itemIdx, mIdx);

    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('width', '20'); svg.setAttribute('height', '20'); svg.setAttribute('fill', 'none');
    svg.setAttribute('stroke', 'currentColor'); svg.setAttribute('viewBox', '0 0 24 24'); svg.setAttribute('stroke-width', '2');
    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    path.setAttribute('stroke-linecap', 'round'); path.setAttribute('stroke-linejoin', 'round');
    path.setAttribute('d', 'M6 18L18 6M6 6l12 12');
    svg.appendChild(path);
    deleteBtn.appendChild(svg);

    row.appendChild(input);
    row.appendChild(select);
    row.appendChild(colorWrapper);
    row.appendChild(deleteBtn);

    container.appendChild(row);
}

function removeMessageRow(itemIdx, mIdx) {
    const row = document.getElementById('msgRow_' + itemIdx + '_' + mIdx);
    if (row) row.remove();
}

function createColorPickerItem(name, labelText, defaultValue, id) {
    const item = document.createElement('div');
    item.className = 'color-item';

    const wrapper = document.createElement('div');
    wrapper.className = 'color-picker-wrapper';

    const input = document.createElement('input');
    input.type = 'color';
    input.name = name;
    if (id) input.id = id;
    input.value = defaultValue;
    input.className = 'color-picker-input';

    const label = document.createElement('span');
    label.className = 'color-label';
    label.textContent = labelText;

    wrapper.appendChild(input);
    item.appendChild(wrapper);
    item.appendChild(label);

    return item;
}

function updateCupcakeIcingUI(itemIdx, styleVal) {
    const container = document.getElementById('cupcakeColorGrid_' + itemIdx);
    if (!container) return;
    container.textContent = '';

    if (styleVal === 'single') {
        container.appendChild(createColorPickerItem('items[' + itemIdx + '][icing_color_1]', 'Color', '#8A38F5'));
    } else if (styleVal === 'duo') {
        container.appendChild(createColorPickerItem('items[' + itemIdx + '][icing_color_1]', 'Color 1', '#8A38F5'));
        container.appendChild(createColorPickerItem('items[' + itemIdx + '][icing_color_2]', 'Color 2', '#ec4899'));
    } else if (styleVal === 'rainbow') {
        const badge = document.createElement('div');
        badge.className = 'rainbow-preset-badge';
        badge.textContent = '🌈 Rainbow Swirl frosting preset applied!';
        container.appendChild(badge);
    }
}

function removeItemCard(index) { 
    const el = document.getElementById('itemCard_' + index);
    if (el) el.remove(); 
}

function openCakeAlbumModal() {
    alert("Opening Business Cake Album (Album view coming soon!).");
}

function setupDragAndDrop(idx) {
    const dropZone = document.getElementById('dropZone_' + idx);
    const fileInput = document.getElementById('fileInput_' + idx);
    if (!dropZone || !fileInput) return;

    dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('dragover'); });
    dropZone.addEventListener('dragleave', () => { dropZone.classList.remove('dragover'); });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault(); dropZone.classList.remove('dragover');
        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            processImage(fileInput.files[0], idx);
        }
    });
}

function handleFileSelect(e, idx) {
    if (e.target.files.length > 0) processImage(e.target.files[0], idx);
}

function clearImage(e, idx) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    const fileInput = document.getElementById('fileInput_' + idx);
    const preview = document.getElementById('preview_' + idx);
    const actionsOverlay = document.getElementById('actionsOverlay_' + idx);
    const placeholder = document.getElementById('placeholder_' + idx);
    const dropZone = document.getElementById('dropZone_' + idx);
    const aiResult = document.getElementById('aiResult_' + idx);

    if (fileInput) fileInput.value = ''; 
    if (preview) { preview.src = ''; preview.classList.add('hidden-element'); preview.classList.remove('is-active'); }
    if (actionsOverlay) actionsOverlay.classList.add('hidden-element'); 
    if (placeholder) placeholder.classList.remove('hidden-element'); 
    if (dropZone) dropZone.classList.remove('has-image');
    if (aiResult) aiResult.classList.add('hidden-element');
}

async function processImage(file, idx) {
    const preview = document.getElementById('preview_' + idx);
    const placeholder = document.getElementById('placeholder_' + idx);
    const dropZone = document.getElementById('dropZone_' + idx);
    const actionsOverlay = document.getElementById('actionsOverlay_' + idx);
    const loader = document.getElementById('loader_' + idx);

    const imageObjectURL = URL.createObjectURL(file);

    if (preview) {
        preview.src = imageObjectURL; 
        preview.classList.remove('hidden-element');
        preview.classList.add('is-active');
    }
    if (placeholder) placeholder.classList.add('hidden-element'); 
    if (dropZone) dropZone.classList.add('has-image');
    if (actionsOverlay) actionsOverlay.classList.remove('hidden-element'); 
    if (loader) loader.classList.add('visible');

    let formData = new FormData();
    formData.append('image', file);
    formData.append('_token', csrfToken);

    try {
        const response = await fetch(visionRoute, { 
            method: 'POST', 
            body: formData, 
            headers: { 'Accept': 'application/json' } 
        });
        
        const data = await response.json();
        if (loader) loader.classList.remove('visible');

        if (response.ok && data.success) {
            if (data.theme) {
                const aiResult = document.getElementById('aiResult_' + idx);
                const themeInput = document.getElementById('themeInput_' + idx);
                if (aiResult) {
                    aiResult.classList.remove('hidden-element');
                    aiResult.classList.add('is-active');
                }
                if (themeInput) themeInput.value = data.theme;
            }
            if (data.description) {
                const aiResult = document.getElementById('aiResult_' + idx);
                const descInput = document.getElementById('descInput_' + idx);
                if (aiResult) {
                    aiResult.classList.remove('hidden-element');
                    aiResult.classList.add('is-active');
                }
                if (descInput) descInput.value = data.description;
            }

            if (data.colors) {
                const colBase = document.getElementById('colBase_' + idx);
                const colTop = document.getElementById('colTop_' + idx);
                const colBorder = document.getElementById('colBorder_' + idx);
                const colSprink = document.getElementById('colSprink_' + idx);

                if (colBase) colBase.value = data.colors.base || '#ffffff';
                if (colTop) colTop.value = data.colors.top || '#ffffff';
                if (colBorder) colBorder.value = data.colors.border || '#ffffff';
                if (colSprink) colSprink.value = data.colors.sprinkles || '#ffffff';
            }

            if (data.shape) {
                const targetShape = data.shape.charAt(0).toUpperCase() + data.shape.slice(1).toLowerCase();
                selectCakeShape(idx, targetShape);
            }

            if (data.height) {
                selectCustomOption('height_wrap_' + idx, 'items[' + idx + '][height]', data.height, data.height, null);
            }

            if (data.messages && data.messages.length > 0) {
                const msgContainer = document.getElementById('messagesContainer_' + idx);
                if (msgContainer) msgContainer.textContent = '';
                data.messages.forEach(m => {
                    addMessageRow(idx, m.text, m.placement, m.color);
                });
            }
        } else {
            alert("AI Error: " + (data.error || "Could not process image."));
            clearImage(null, idx);
        }
    } catch (error) {
        if (loader) loader.classList.remove('visible');
        console.error("Network / Extraction Error:", error);
        alert("Network connection failed or server error occurred. Please try uploading again.");
        clearImage(null, idx);
    }
}

function addItemCard(type) {
    const container = document.getElementById('itemsContainer');
    if (!container) return;

    const card = document.createElement('div');
    card.className = 'duo-3d-card';
    card.id = 'itemCard_' + itemIndex;

    const headerRow = document.createElement('div');
    headerRow.className = 'card-header-row';

    const titleSpan = document.createElement('span');
    titleSpan.className = 'card-title-text';

    if (type === 'custom_cake') titleSpan.textContent = '🎂 Custom Cake Design';
    else if (type === 'cupcakes') titleSpan.textContent = '🧁 Cupcakes';
    else if (type === 'giveaway_loaf') titleSpan.textContent = '🍞 Cheesy Taisan Loaf Giveaways';
    else if (type === 'giveaway_round') titleSpan.textContent = '🍰 Round Giveaways';

    headerRow.appendChild(titleSpan);

    if (itemIndex > 0) {
        const currentIdx = itemIndex;
        const rmBtn = document.createElement('button');
        rmBtn.type = 'button';
        rmBtn.className = 'btn-duo-outline btn-remove-card';
        rmBtn.textContent = 'Remove';
        rmBtn.onclick = () => removeItemCard(currentIdx);
        headerRow.appendChild(rmBtn);
    }
    card.appendChild(headerRow);

    const typeInput = document.createElement('input');
    typeInput.type = 'hidden';
    typeInput.name = 'items[' + itemIndex + '][item_type]';
    typeInput.value = type;
    card.appendChild(typeInput);

    if (type === 'custom_cake') {
        const qtyInput = document.createElement('input');
        qtyInput.type = 'hidden';
        qtyInput.name = 'items[' + itemIndex + '][quantity]';
        qtyInput.value = '1';
        card.appendChild(qtyInput);

        const grid = document.createElement('div');
        grid.className = 'card-grid-2';

        const uploadCol = document.createElement('div');
        uploadCol.className = 'upload-column';

        const dropZone = createUploadDropZoneNode(itemIndex);

        const albumBtn = document.createElement('button');
        albumBtn.type = 'button'; albumBtn.className = 'btn-duo-outline'; albumBtn.textContent = '📖 Pick from Business Cake Album'; albumBtn.onclick = openCakeAlbumModal;

        uploadCol.appendChild(dropZone); uploadCol.appendChild(albumBtn); grid.appendChild(uploadCol);

        const fieldsCol = document.createElement('div');
        fieldsCol.className = 'fields-column';

        const aiRes = document.createElement('div');
        aiRes.id = 'aiResult_' + itemIndex; aiRes.className = 'ai-result-block hidden-element';
        const aiInner = document.createElement('div');
        aiInner.className = 'ai-inner-box';
        
        const tHead = document.createElement('div'); tHead.className = 'ai-field-head'; tHead.textContent = '✨ Identified Theme';
        const tInput = document.createElement('input'); tInput.type = 'text'; tInput.name = 'items[' + itemIndex + '][ai_theme]'; tInput.id = 'themeInput_' + itemIndex; tInput.className = 'clean-input ai-theme-input'; tInput.placeholder = 'e.g. Minimalist Floral, Spider-Man...';
        
        const dHead = document.createElement('div'); dHead.className = 'ai-field-head'; dHead.textContent = '📝 Design Specs';
        const dArea = document.createElement('textarea'); dArea.name = 'items[' + itemIndex + '][ai_description]'; dArea.id = 'descInput_' + itemIndex; dArea.className = 'clean-input'; dArea.rows = 2; dArea.placeholder = 'e.g. Pastel pink frosting with white piping details...';

        aiInner.appendChild(tHead); aiInner.appendChild(tInput); aiInner.appendChild(dHead); aiInner.appendChild(dArea); aiRes.appendChild(aiInner); fieldsCol.appendChild(aiRes);

        const colGroup = document.createElement('div'); colGroup.className = 'input-group input-group-mb20';
        const colLabel = document.createElement('label'); colLabel.className = 'input-label'; colLabel.textContent = 'Extracted Colors (Adjustable)';
        const colGrid = document.createElement('div'); colGrid.className = 'color-grid';
        colGrid.appendChild(createColorPickerItem('items[' + itemIndex + '][color_base]', 'Base', '#ffffff', 'colBase_' + itemIndex));
        colGrid.appendChild(createColorPickerItem('items[' + itemIndex + '][color_top]', 'Top', '#ffffff', 'colTop_' + itemIndex));
        colGrid.appendChild(createColorPickerItem('items[' + itemIndex + '][color_border]', 'Border', '#ffffff', 'colBorder_' + itemIndex));
        colGrid.appendChild(createColorPickerItem('items[' + itemIndex + '][color_sprinkles]', 'Details', '#ffffff', 'colSprink_' + itemIndex));
        colGroup.appendChild(colLabel); colGroup.appendChild(colGrid); fieldsCol.appendChild(colGroup);

        const msgGroup = document.createElement('div'); msgGroup.className = 'input-group input-group-mb20';
        const msgLabel = document.createElement('label'); msgLabel.className = 'input-label'; msgLabel.textContent = 'Cake Messages & Toppers (Placement & Colors)';
        const msgContainer = document.createElement('div'); msgContainer.id = 'messagesContainer_' + itemIndex;
        const addMsgBtn = document.createElement('button'); addMsgBtn.type = 'button'; addMsgBtn.className = 'btn-add btn-add-message'; addMsgBtn.textContent = '+ Add Message / Topper Text';
        const cIdx = itemIndex;
        addMsgBtn.onclick = () => addMessageRow(cIdx);
        msgGroup.appendChild(msgLabel); msgGroup.appendChild(msgContainer); msgGroup.appendChild(addMsgBtn); fieldsCol.appendChild(msgGroup);

        const flvGroup = document.createElement('div'); flvGroup.className = 'input-group input-group-mb16';
        const flvLabel = document.createElement('label'); flvLabel.className = 'input-label'; flvLabel.textContent = 'Flavor';
        const flvDropdown = createCustomDropdownNode('flavor_wrap_' + itemIndex, 'items[' + itemIndex + '][flavor]', [
            {label: 'Chocolate', value: 'Chocolate'},
            {label: 'Vanilla', value: 'Vanilla'},
            {label: 'Ube', value: 'Ube'},
            {label: 'Mocha Chiffon', value: 'Mocha Chiffon'},
            {label: 'Regular Chiffon', value: 'Regular Chiffon'}
        ], 'Chocolate');
        flvGroup.appendChild(flvLabel); flvGroup.appendChild(flvDropdown); fieldsCol.appendChild(flvGroup);

        const shpGroup = document.createElement('div'); 
        shpGroup.className = 'input-group input-group-mb16'; 
        
        const shpLabel = document.createElement('label'); 
        shpLabel.className = 'input-label'; 
        shpLabel.textContent = 'Shape';
        
        const shpPillGrid = document.createElement('div'); 
        shpPillGrid.className = 'pill-grid'; 
        shpPillGrid.id = 'shapeGroup_' + itemIndex;
        
        ['Round', 'Square', 'Rectangle'].forEach(s => {
            const btn = document.createElement('button'); 
            btn.type = 'button'; 
            btn.className = 'btn-duo-outline btn-pill-flex' + (s === 'Round' ? ' active' : ''); 
            btn.textContent = s === 'Rectangle' ? 'Rect' : s;
            btn.dataset.shape = s;
            btn.onclick = () => selectCakeShape(cIdx, s); 
            shpPillGrid.appendChild(btn);
        });
        
        const hiddenShape = document.createElement('input'); 
        hiddenShape.type = 'hidden'; 
        hiddenShape.name = 'items[' + itemIndex + '][shape]'; 
        hiddenShape.id = 'hidden_shape_' + itemIndex; 
        hiddenShape.value = 'Round';
        
        shpGroup.appendChild(shpLabel); 
        shpGroup.appendChild(shpPillGrid); 
        shpGroup.appendChild(hiddenShape); 
        fieldsCol.appendChild(shpGroup);

        const tierGroup = document.createElement('div'); 
        tierGroup.className = 'input-group input-group-mb16'; 
        tierGroup.id = 'tiersWrapper_' + itemIndex; 
        
        const tierLabel = document.createElement('label'); 
        tierLabel.className = 'input-label'; 
        tierLabel.textContent = 'Tiers';
        
        const tierPillGrid = document.createElement('div'); 
        tierPillGrid.className = 'pill-grid'; 
        
        ['1 Tier', '2 Tiers', '3 Tiers'].forEach(t => {
            const btn = document.createElement('button'); 
            btn.type = 'button'; 
            btn.className = 'btn-duo-outline btn-pill-flex' + (t === '1 Tier' ? ' active' : ''); 
            btn.textContent = t;
            btn.dataset.tier = t;
            btn.onclick = () => selectCakeTier(cIdx, t); 
            tierPillGrid.appendChild(btn);
        });
        
        const hiddenTier = document.createElement('input'); 
        hiddenTier.type = 'hidden'; 
        hiddenTier.name = 'items[' + itemIndex + '][cake_type]'; 
        hiddenTier.id = 'hidden_tier_' + itemIndex; 
        hiddenTier.value = '1 Tier';
        
        tierGroup.appendChild(tierLabel); 
        tierGroup.appendChild(tierPillGrid); 
        tierGroup.appendChild(hiddenTier); 
        fieldsCol.appendChild(tierGroup);

        const szGroup = document.createElement('div'); szGroup.className = 'input-group input-group-mb16';
        const szLabel = document.createElement('label'); szLabel.className = 'size-tray-label'; szLabel.textContent = 'Size Tray (Centered & Ascending)';
        const szTray = document.createElement('div'); szTray.className = 'size-tray-container'; szTray.id = 'sizeContainer_' + itemIndex;
        szGroup.appendChild(szLabel); szGroup.appendChild(szTray); fieldsCol.appendChild(szGroup);

        const hgtGroup = document.createElement('div'); hgtGroup.className = 'input-group input-group-mb16';
        const hgtLabel = document.createElement('label'); hgtLabel.className = 'input-label'; hgtLabel.textContent = 'Height (Per Tier)';
        const hgtDropdown = createCustomDropdownNode('height_wrap_' + itemIndex, 'items[' + itemIndex + '][height]', [
            {label: '4 inches (Standard)', value: '4 inches'},
            {label: '5 inches (Tall)', value: '5 inches'},
            {label: '6 inches (Extra Tall)', value: '6 inches'}
        ], '4 inches');
        hgtGroup.appendChild(hgtLabel); hgtGroup.appendChild(hgtDropdown); fieldsCol.appendChild(hgtGroup);

        grid.appendChild(fieldsCol);
        card.appendChild(grid);

        setTimeout(() => { 
            updateCakeUI(cIdx); 
            setupDragAndDrop(cIdx);
            addMessageRow(cIdx, '', 'Top', '#8A38F5');
        }, 50);

    } else if (type === 'cupcakes') {
        const cIdx = itemIndex;
        const grid1 = document.createElement('div'); grid1.className = 'card-grid-2';

        const pkgGroup = document.createElement('div'); pkgGroup.className = 'input-group';
        const pkgLabel = document.createElement('label'); pkgLabel.className = 'input-label'; pkgLabel.textContent = 'Packaging Option';
        const pkgDropdown = createCustomDropdownNode('cup_pkg_wrap_' + itemIndex, 'items[' + itemIndex + '][cupcake_packaging]', [
            {label: 'Regular Boxed Set', value: 'boxed_set'},
            {label: 'Single Giveaway Boxes', value: 'single_giveaway_box'}
        ], 'boxed_set', (val) => updateCupcakeQtyStep(cIdx, val));
        pkgGroup.appendChild(pkgLabel); pkgGroup.appendChild(pkgDropdown); grid1.appendChild(pkgGroup);

        const qtyGroup = document.createElement('div'); qtyGroup.className = 'input-group';
        const qtyLabel = document.createElement('label'); qtyLabel.className = 'input-label'; qtyLabel.textContent = 'Quantity (Pieces)';
        const qtyInput = document.createElement('input'); qtyInput.type = 'number'; qtyInput.name = 'items[' + itemIndex + '][quantity]'; qtyInput.id = 'cupQtyInput_' + itemIndex; qtyInput.value = '12'; qtyInput.step = '6'; qtyInput.min = '6'; qtyInput.className = 'clean-input'; qtyInput.required = true;
        const qtyHelp = document.createElement('small'); qtyHelp.id = 'cupQtyHelp_' + itemIndex; qtyHelp.className = 'qty-help-text'; qtyHelp.textContent = 'Increments of 6 pieces';
        qtyGroup.appendChild(qtyLabel); qtyGroup.appendChild(qtyInput); qtyGroup.appendChild(qtyHelp); grid1.appendChild(qtyGroup);

        card.appendChild(grid1);

        const grid2 = document.createElement('div'); grid2.className = 'card-grid-2 card-grid-2-mt16';

        const icgGroup = document.createElement('div'); icgGroup.className = 'input-group';
        const icgLabel = document.createElement('label'); icgLabel.className = 'input-label'; icgLabel.textContent = 'Icing Color Style';
        const icgDropdown = createCustomDropdownNode('cup_icg_wrap_' + itemIndex, 'items[' + itemIndex + '][icing_style]', [
            {label: 'Single Color', value: 'single'},
            {label: 'Duo-Color Swirl', value: 'duo'},
            {label: 'Rainbow Swirl 🌈', value: 'rainbow'}
        ], 'single', (val) => updateCupcakeIcingUI(cIdx, val));
        icgGroup.appendChild(icgLabel); icgGroup.appendChild(icgDropdown); grid2.appendChild(icgGroup);

        const clrGroup = document.createElement('div'); clrGroup.className = 'input-group';
        const clrLabel = document.createElement('label'); clrLabel.className = 'input-label'; clrLabel.textContent = 'Icing Colors';
        const clrGrid = document.createElement('div'); clrGrid.className = 'color-grid'; clrGrid.id = 'cupcakeColorGrid_' + itemIndex;
        clrGroup.appendChild(clrLabel); clrGroup.appendChild(clrGrid); grid2.appendChild(clrGroup);

        card.appendChild(grid2);

        const topSec = document.createElement('div'); topSec.id = 'cupcakeTopperSec_' + itemIndex; topSec.className = 'topper-section';
        const topLbl = document.createElement('label'); topLbl.className = 'checkbox-label-flex';
        const topCb = document.createElement('input'); topCb.type = 'checkbox'; topCb.name = 'items[' + itemIndex + '][enable_toppers]'; topCb.className = 'checkbox-custom-size';
        topCb.onchange = () => { 
            const topperSub = document.getElementById('topperSub_' + cIdx);
            if (topperSub) {
                if (topCb.checked) {
                    topperSub.classList.remove('hidden-element');
                    topperSub.classList.add('is-active');
                } else {
                    topperSub.classList.add('hidden-element');
                    topperSub.classList.remove('is-active');
                }
            } 
        };
        const topTxt = document.createTextNode(' Request Custom Cupcake Toppers');
        topLbl.appendChild(topCb); topLbl.appendChild(topTxt); topSec.appendChild(topLbl);

        const topSub = document.createElement('div'); topSub.id = 'topperSub_' + itemIndex; topSub.className = 'topper-sub-container hidden-element';
        const topArea = document.createElement('textarea'); topArea.name = 'items[' + itemIndex + '][topper_instructions]'; topArea.className = 'clean-input'; topArea.rows = 2; topArea.placeholder = 'Describe toppers (e.g. Printed 2D character pics or molded fondant numbers)...';
        topSub.appendChild(topArea); topSec.appendChild(topSub); card.appendChild(topSec);

        card.appendChild(createThankYouTagNode(itemIndex));

        setTimeout(() => { updateCupcakeIcingUI(cIdx, 'single'); }, 50);

    } else if (type === 'giveaway_loaf') {
        const grid = document.createElement('div'); grid.className = 'card-grid-2';

        const qtyGroup = document.createElement('div'); qtyGroup.className = 'input-group';
        const qtyLabel = document.createElement('label'); qtyLabel.className = 'input-label'; qtyLabel.textContent = 'Quantity (Pieces)';
        const qtyInput = document.createElement('input'); qtyInput.type = 'number'; qtyInput.name = 'items[' + itemIndex + '][quantity]'; qtyInput.value = '10'; qtyInput.min = '1'; qtyInput.className = 'clean-input'; qtyInput.required = true;
        qtyGroup.appendChild(qtyLabel); qtyGroup.appendChild(qtyInput); grid.appendChild(qtyGroup);

        const pkgGroup = document.createElement('div'); pkgGroup.className = 'input-group';
        const pkgLabel = document.createElement('label'); pkgLabel.className = 'input-label'; pkgLabel.textContent = 'Packaging Add-ons';
        const pkgWrap = document.createElement('div'); pkgWrap.className = 'checkbox-flex-group';
        
        const lBox = document.createElement('label'); lBox.className = 'checkbox-pointer';
        const cbBox = document.createElement('input'); cbBox.type = 'checkbox'; cbBox.name = 'items[' + itemIndex + '][box]'; cbBox.value = '1'; cbBox.className = 'checkbox-small-size';
        lBox.appendChild(cbBox); lBox.appendChild(document.createTextNode(' Include Box'));

        const lRib = document.createElement('label'); lRib.className = 'checkbox-pointer';
        const cbRib = document.createElement('input'); cbRib.type = 'checkbox'; cbRib.name = 'items[' + itemIndex + '][ribbon]'; cbRib.value = '1'; cbRib.className = 'checkbox-small-size';
        lRib.appendChild(cbRib); lRib.appendChild(document.createTextNode(' Include Ribbon'));

        pkgWrap.appendChild(lBox); pkgWrap.appendChild(lRib); pkgGroup.appendChild(pkgLabel); pkgGroup.appendChild(pkgWrap); grid.appendChild(pkgGroup);

        card.appendChild(grid);
        card.appendChild(createThankYouTagNode(itemIndex));

    } else if (type === 'giveaway_round') {
        const grid = document.createElement('div'); grid.className = 'card-grid-2';

        const szGroup = document.createElement('div'); szGroup.className = 'input-group';
        const szLabel = document.createElement('label'); szLabel.className = 'input-label'; szLabel.textContent = 'Size';
        const szDropdown = createCustomDropdownNode('rnd_sz_wrap_' + itemIndex, 'items[' + itemIndex + '][size]', [
            {label: '6 inch', value: '6 inch'},
            {label: '8 inch', value: '8 inch'}
        ], '6 inch');
        szGroup.appendChild(szLabel); szGroup.appendChild(szDropdown); grid.appendChild(szGroup);

        const flvGroup = document.createElement('div'); flvGroup.className = 'input-group';
        const flvLabel = document.createElement('label'); flvLabel.className = 'input-label'; flvLabel.textContent = 'Flavor Selection';
        const flvDropdown = createCustomDropdownNode('rnd_flv_wrap_' + itemIndex, 'items[' + itemIndex + '][flavor]', [
            {label: 'Classic Yema Cheese Cake', value: 'Classic Yema Cheese Cake'},
            {label: 'Triple Choco', value: 'Triple Choco'}
        ], 'Classic Yema Cheese Cake');
        flvGroup.appendChild(flvLabel); flvGroup.appendChild(flvDropdown); grid.appendChild(flvGroup);

        const qtyGroup = document.createElement('div'); qtyGroup.className = 'input-group';
        const qtyLabel = document.createElement('label'); qtyLabel.className = 'input-label'; qtyLabel.textContent = 'Quantity (Pieces)';
        const qtyInput = document.createElement('input'); qtyInput.type = 'number'; qtyInput.name = 'items[' + itemIndex + '][quantity]'; qtyInput.value = '5'; qtyInput.min = '1'; qtyInput.className = 'clean-input'; qtyInput.required = true;
        qtyGroup.appendChild(qtyLabel); qtyGroup.appendChild(qtyInput); grid.appendChild(qtyGroup);

        const pkgGroup = document.createElement('div'); pkgGroup.className = 'input-group';
        const pkgLabel = document.createElement('label'); pkgLabel.className = 'input-label'; pkgLabel.textContent = 'Packaging Style';
        const pkgDropdown = createCustomDropdownNode('rnd_pkg_wrap_' + itemIndex, 'items[' + itemIndex + '][packaging_style]', [
            {label: 'No Special Packaging', value: 'none'},
            {label: 'Ribbon Only', value: 'ribbon_only'},
            {label: 'Box with Ribbon', value: 'box_with_ribbon'}
        ], 'none');
        pkgGroup.appendChild(pkgLabel); pkgGroup.appendChild(pkgDropdown); grid.appendChild(pkgGroup);

        card.appendChild(grid);
        card.appendChild(createThankYouTagNode(itemIndex));
    }

    container.appendChild(card);
    itemIndex++;
}

function updateCupcakeQtyStep(itemIdx, pkgValue) {
    const qtyInput = document.getElementById('cupQtyInput_' + itemIdx);
    const qtyHelp = document.getElementById('cupQtyHelp_' + itemIdx);
    const topperSec = document.getElementById('cupcakeTopperSec_' + itemIdx);

    if (pkgValue === 'single_giveaway_box') {
        if (qtyInput) {
            qtyInput.step = "1";
            qtyInput.min = "1";
            qtyInput.value = "1";
        }
        if (qtyHelp) qtyHelp.textContent = "Order exact single piece quantity (Increments of 1)";
        if (topperSec) topperSec.style.display = "none";
    } else {
        if (qtyInput) {
            qtyInput.step = "6";
            qtyInput.min = "6";
            qtyInput.value = "12";
        }
        if (qtyHelp) qtyHelp.textContent = "Increments of 6 pieces";
        if (topperSec) topperSec.style.display = "block";
    }
}

function createUploadDropZoneNode(itemIndex) {
    const cIdx = itemIndex;
    const dropZone = document.createElement('label');
    dropZone.id = 'dropZone_' + itemIndex;
    dropZone.className = 'upload-zone upload-dropzone-styled';

    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.name = 'items[' + itemIndex + '][reference_image]';
    fileInput.id = 'fileInput_' + itemIndex;
    fileInput.accept = 'image/*';
    fileInput.className = 'hidden-element';
    fileInput.required = true;
    fileInput.onchange = (e) => handleFileSelect(e, cIdx);

    const placeholder = document.createElement('div');
    placeholder.id = 'placeholder_' + itemIndex;
    placeholder.className = 'upload-placeholder';
    
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('fill', 'none'); svg.setAttribute('stroke', '#94a3b8'); svg.setAttribute('viewBox', '0 0 24 24'); svg.setAttribute('width', '48'); svg.setAttribute('height', '48');
    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    path.setAttribute('stroke-linecap', 'round'); path.setAttribute('stroke-linejoin', 'round'); path.setAttribute('stroke-width', '2');
    path.setAttribute('d', 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12');
    svg.appendChild(path);

    const strong = document.createElement('strong');
    strong.className = 'placeholder-text-strong';
    strong.textContent = 'Drag & drop reference image or click to browse (Required) *';

    placeholder.appendChild(svg);
    placeholder.appendChild(strong);

    const preview = document.createElement('img');
    preview.id = 'preview_' + itemIndex;
    preview.className = 'preview-img preview-img-styled hidden-element';

    const actionsOverlay = document.createElement('div');
    actionsOverlay.id = 'actionsOverlay_' + itemIndex;
    actionsOverlay.className = 'image-actions-overlay hidden-element';

    const zoomBtn = document.createElement('button');
    zoomBtn.type = 'button';
    zoomBtn.className = 'btn-img-action btn-img-zoom';
    zoomBtn.title = 'Zoom Image';
    zoomBtn.onclick = (e) => {
        e.preventDefault();
        e.stopPropagation();
        const prv = document.getElementById('preview_' + cIdx);
        if (prv && prv.src) openZoomModal(prv.src);
    };
    zoomBtn.innerHTML = `<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>`;

    const clearBtn = document.createElement('button');
    clearBtn.type = 'button'; 
    clearBtn.id = 'clearBtn_' + itemIndex; 
    clearBtn.className = 'btn-img-action btn-clear';
    clearBtn.title = 'Remove Image';
    clearBtn.onclick = (e) => clearImage(e, cIdx);
    clearBtn.innerHTML = `<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`;

    actionsOverlay.appendChild(zoomBtn);
    actionsOverlay.appendChild(clearBtn);

    const loader = document.createElement('div');
    loader.id = 'loader_' + itemIndex; 
    loader.className = 'loading-overlay';
    const lSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    lSvg.setAttribute('class', 'spinner'); lSvg.setAttribute('viewBox', '0 0 24 24'); lSvg.setAttribute('fill', 'none');
    const lCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
    lCircle.setAttribute('opacity', '0.25'); lCircle.setAttribute('cx', '12'); lCircle.setAttribute('cy', '12'); lCircle.setAttribute('r', '10'); lCircle.setAttribute('stroke', 'currentColor'); lCircle.setAttribute('stroke-width', '4');
    const lPath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    lPath.setAttribute('opacity', '0.75'); lPath.setAttribute('fill', 'currentColor'); lPath.setAttribute('d', 'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z');
    lSvg.appendChild(lCircle); lSvg.appendChild(lPath);
    const lSpan = document.createElement('span');
    lSpan.className = 'loading-text-span';
    lSpan.textContent = 'Extracting Specs...';
    loader.appendChild(lSvg); loader.appendChild(lSpan);

    dropZone.appendChild(fileInput); 
    dropZone.appendChild(placeholder); 
    dropZone.appendChild(preview); 
    dropZone.appendChild(actionsOverlay); 
    dropZone.appendChild(loader);

    return dropZone;
}

function createThankYouTagNode(idx) {
    const wrapper = document.createElement('div');
    wrapper.className = 'tag-wrapper-card';

    const label = document.createElement('label');
    label.className = 'checkbox-label-flex';

    const cb = document.createElement('input');
    cb.type = 'checkbox';
    cb.name = 'items[' + idx + '][enable_tag]';
    cb.className = 'checkbox-custom-size';
    cb.onchange = () => {
        const tagSub = document.getElementById('tagSub_' + idx);
        if (tagSub) {
            if (cb.checked) {
                tagSub.classList.remove('hidden-element');
                tagSub.classList.add('is-active');
            } else {
                tagSub.classList.add('hidden-element');
                tagSub.classList.remove('is-active');
            }
        }
    };

    label.appendChild(cb);
    label.appendChild(document.createTextNode(' Include Optional Thank You Tag'));

    const sub = document.createElement('div');
    sub.id = 'tagSub_' + idx;
    sub.className = 'tag-sub-container hidden-element';

    const grid = document.createElement('div');
    grid.className = 'card-grid-2';

    const txtGrp = document.createElement('div');
    txtGrp.className = 'input-group input-group-no-margin';
    const txtLbl = document.createElement('label');
    txtLbl.className = 'tag-label-sub';
    txtLbl.textContent = 'Tag Message Text';
    const txtInput = document.createElement('input');
    txtInput.type = 'text';
    txtInput.name = 'items[' + idx + '][tag_message]';
    txtInput.className = 'clean-input';
    txtInput.placeholder = 'e.g. Thank you for celebrating!';
    txtGrp.appendChild(txtLbl); txtGrp.appendChild(txtInput);

    const imgGrp = document.createElement('div');
    imgGrp.className = 'input-group input-group-no-margin';
    const imgLbl = document.createElement('label');
    imgLbl.className = 'tag-label-sub';
    imgLbl.textContent = 'Upload Custom Tag Artwork';
    const imgInput = document.createElement('input');
    imgInput.type = 'file';
    imgInput.name = 'items[' + idx + '][tag_custom_image]';
    imgInput.accept = 'image/*';
    imgInput.className = 'clean-input tag-file-input';
    imgGrp.appendChild(imgLbl); imgGrp.appendChild(imgInput);

    grid.appendChild(txtGrp);
    grid.appendChild(imgGrp);
    sub.appendChild(grid);

    wrapper.appendChild(label);
    wrapper.appendChild(sub);

    return wrapper;
}
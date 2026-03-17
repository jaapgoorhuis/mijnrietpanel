import './bootstrap';
import 'livewire-sortable';
import 'flowbite-datepicker';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin, { Draggable } from '@fullcalendar/interaction';
import { initFlowbite } from 'flowbite';
import nlLocale from '@fullcalendar/core/locales/nl';

// --- Flowbite init bij Livewire navigatie ---
Livewire.hook('commit', ({ succeed }) => {
    succeed(() => queueMicrotask(() => initFlowbite()));
});

document.addEventListener('livewire:navigated', () => initFlowbite());

// --- Flags om dubbele inits te voorkomen ---
let calendarInitialized = false;
let draggablesInitialized = false;

// --- Controleer of een dag orders heeft (exclusief het gesleepte event) ---
function dayHasOrders(calendar, dateStr, excludeEventId = null) {
    return calendar.getEvents().some(ev => {
        if (ev.id === excludeEventId) return false;
        if (ev.extendedProps?.type === 'manual-block') return false;

        const eventDate = ev.startStr; // FullCalendar geeft altijd ISO string
        return eventDate === dateStr;
    });
}

function getDayLoad(calendar, dateStr) {
    let total = 0;

    calendar.getEvents().forEach(ev => {
        if (ev.extendedProps?.type === 'manual-block') return;

        const evDate = ev.start; // Date object
        const evDateStr = evDate.getFullYear() + '-' +
            String(evDate.getMonth()+1).padStart(2,'0') + '-' +
            String(evDate.getDate()).padStart(2,'0');

        if (evDateStr === dateStr) {
            const m2 = parseFloat(ev.extendedProps?.planned_m2);
            total += isNaN(m2) ? 0 : m2;
        }
    });

    return total;
}


function getContrastColor(color) {
    if (!color) return 'black';

    let r, g, b;

    // rgb(a) string
    if (color.startsWith('rgb')) {
        const rgb = color.match(/\d+/g);
        r = parseInt(rgb[0], 10);
        g = parseInt(rgb[1], 10);
        b = parseInt(rgb[2], 10);
    } else {
        // probeer hex eerst
        try {
            if (color[0] === '#') color = color.slice(1);
            if (color.length === 3) {
                r = parseInt(color[0]+color[0], 16);
                g = parseInt(color[1]+color[1], 16);
                b = parseInt(color[2]+color[2], 16);
            } else if (color.length === 6) {
                r = parseInt(color.substring(0,2), 16);
                g = parseInt(color.substring(2,4), 16);
                b = parseInt(color.substring(4,6), 16);
            } else {
                throw 'invalid hex';
            }
        } catch {
            // fallback: gebruik browser om hex van CSS-naam te krijgen
            const temp = document.createElement('div');
            temp.style.color = color;
            document.body.appendChild(temp);
            const cs = window.getComputedStyle(temp).color;
            document.body.removeChild(temp);
            const rgb = cs.match(/\d+/g);
            r = parseInt(rgb[0],10);
            g = parseInt(rgb[1],10);
            b = parseInt(rgb[2],10);
        }
    }

    const brightness = (r*299 + g*587 + b*114) / 1000;
    return brightness > 155 ? 'black' : 'white';
}



function getBgColor(el) {
    const style = window.getComputedStyle(el);
    let bg = style.backgroundColor;

    // fallback naar wit als leeg of transparent
    if (!bg || bg === 'transparent' || bg === 'rgba(0, 0, 0, 0)') {
        bg = '#ffffff';
    }

    const rgb = bg.match(/\d+/g);
    if (rgb && rgb.length >= 3) {
        const r = parseInt(rgb[0]).toString(16).padStart(2, '0');
        const g = parseInt(rgb[1]).toString(16).padStart(2, '0');
        const b = parseInt(rgb[2]).toString(16).padStart(2, '0');
        return `#${r}${g}${b}`;
    }

    return bg; // fallback
}


document.addEventListener('DOMContentLoaded', () => {
    const calendarEl = document.getElementById('calendar');
    const maxM2PerDay = parseFloat(calendarEl.dataset.maxM2 || 50);

    if (!calendarEl || calendarInitialized) return;

    let manualBlockedDates = [];
    let initialEvents = JSON.parse(calendarEl.dataset.events || '[]');

    // --- Manual blocked dates init (geen duplicaten)
    initialEvents = initialEvents.filter(ev => {
        if (ev.type === 'manual-block') {
            if (!manualBlockedDates.includes(ev.start)) {
                manualBlockedDates.push(ev.start);
                return true;
            }
            return false;
        }
        return true;
    });

    // Voeg losse blockedDates toe
    if (window.blockedDates) {
        window.blockedDates.forEach(b => {
            const blockId = 'block-' + b.date;
            if (!initialEvents.some(ev => ev.id === blockId)) {
                manualBlockedDates.push(b.date);
                initialEvents.push({
                    id: blockId,
                    title: b.title,
                    start: b.date,
                    allDay: true,
                    backgroundColor: '#dc3545',
                    borderColor: '#dc3545',
                    textColor: 'white',
                    extendedProps: { type: 'manual-block', originalId: blockId }
                });
            }
        });
    }

    const blockedWeekDays = JSON.parse(calendarEl.dataset.blockedWeekDays || '["zaterdag","zondag"]');


    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        editable: true,
        eventDurationEditable: false,
        droppable: true,
        locale: nlLocale,
        events: initialEvents,


        // --- Voeg dit toe ---
        dayCellContent: function(info) {
            const day = String(info.date.getDate()).padStart(2, '0');
            const month = String(info.date.getMonth() + 1).padStart(2, '0'); // Maand is 0-indexed
            info.dayNumberText = `${day}-${month}`;
        },
        // --- Drag & drop binnen kalender ---

        eventDrop: info => {
            const type = info.event.extendedProps?.type || 'order';

            if (type === 'manual-block') {
                const oldDate = info.event.extendedProps.oldDate || info.oldEvent?.startStr || info.event.startStr;
                const newDate = info.event.startStr;

                if (oldDate !== newDate) {
                    // Update oldDate lokaal
                    info.event.setExtendedProp('oldDate', newDate);

                    // Update manualBlockedDates array
                    manualBlockedDates = manualBlockedDates.filter(d => d !== oldDate);
                    manualBlockedDates.push(newDate);

                    // Stuur naar Livewire
                    Livewire.dispatch('moveBlockedDay', { oldDate, newDate });

                    // Update ID zodat FullCalendar het event niet verwijderd bij next re-render
                    info.event.setProp('id', 'block-' + newDate);
                }

                return;
            }

            // --- ORDERS logic blijft hetzelfde ---
            const dateStr = info.event.startStr;
            const movedOrderId = info.event.extendedProps.order_id || info.event.extendedProps.originalId;
            Livewire.dispatch('move-planning', {
                planningIds: info.event.id,
                targetDate: dateStr
            });
        },

        // --- Ontvang externe draggables / drops van buiten kalender ---
        eventReceive: info => {
            const dateStr = info.event.startStr;
            const type =
                info.draggedEl?.dataset?.type ||
                info.event.extendedProps?.type ||
                'order';
            const originalId =
                info.draggedEl?.dataset?.id ||
                info.event.extendedProps?.originalId ||
                info.event.id;
            const eventTitle = info.event.title || 'Geblokkeerd';

            // -----------------------------
            // MANUAL BLOCK
            // -----------------------------
            if (type === 'manual-block') {
                const blockId = 'block-' + dateStr;

                // Check of block al bestaat
                if (!calendar.getEventById(blockId)) {
                    // Voeg event toe in FullCalendar
                    const newEvent = calendar.addEvent({
                        id: blockId,
                        title: eventTitle,
                        start: dateStr,
                        allDay: true,
                        backgroundColor: '#dc3545',
                        borderColor: '#dc3545',
                        textColor: 'white',
                        extendedProps: {
                            type: 'manual-block',
                            originalId: originalId,
                            oldDate: dateStr
                        }
                    });

                    // Update lokale array
                    manualBlockedDates.push(dateStr);

                    // Stuur naar Livewire DB update
                    Livewire.dispatch('addBlockedDay', {
                        date: dateStr,
                        title: eventTitle
                    });
                }

                // Verwijder placeholder uit externe container
                info.event.remove();

                return; // stop verdere order-logica
            }

            // -----------------------------
            // ORDERS
            // -----------------------------
            calendar.addEvent({
                id: originalId,
                title: info.event.title,
                start: dateStr,
                extendedProps: {
                    ...info.event.extendedProps,
                    originalId: originalId,
                    groupId: info.event.extendedProps?.groupId || originalId,
                    planned_m2: info.event.extendedProps?.planned_m2 || 0
                }
            });

            Livewire.dispatch('plan-order', {
                orderId: originalId,
                date: dateStr
            });

            // Verwijder originele DOM placeholder
            if (info.draggedEl && info.draggedEl.parentNode) {
                info.draggedEl.parentNode.removeChild(info.draggedEl);
            }

            info.event.remove();
        },

        // --- Beperk drops binnen kalender ---
        eventAllow: dropInfo => {
            const dateStr = dropInfo.startStr || dropInfo.start.toISOString().split('T')[0];
            const type = dropInfo.event?.extendedProps?.type || dropInfo.draggedEl?.dataset.type || 'order';
            const draggedId = dropInfo.event?.extendedProps?.originalId || dropInfo.draggedEl?.dataset.id;

            // Dagnaam voor weekend check
            const dayName = new Date(dateStr).toLocaleDateString('nl-NL', { weekday: 'long' }).toLowerCase();

            // --- Manual blocks ---
            if (type === 'manual-block') {
                // Kan niet op een dag waar al een block is
                if (manualBlockedDates.includes(dateStr)) return false;
                if (dayHasOrders(calendar, dateStr, draggedId)) return false;
                return true;
            }

            // --- Orders ---
            // Check handmatig geblokkeerde dagen
            if (manualBlockedDates.includes(dateStr)) return false;

            // Check weekdagen die geblokkeerd zijn
            if (blockedWeekDays.includes(dayName)) return false;

            // Check of er al een manual-block event op die dag staat
            const existingBlock = calendar.getEvents().some(ev => ev.extendedProps?.type === 'manual-block' && ev.startStr === dateStr);
            if (existingBlock) return false;

            // ✅ Anders mag droppen
            return true;
        },
        // --- Sleep naar externe container / blocked ---
        eventDragStop: info => {
            const ordersContainer = document.getElementById('external-orders-list');
            const blockedContainer = document.getElementById('blocked-days-list');
            const el = document.elementFromPoint(info.jsEvent.clientX, info.jsEvent.clientY);

            const type = info.event.extendedProps?.type || 'order';
            const originalId = info.event.extendedProps?.order_id || info.event.extendedProps?.originalId || info.event.id;
            const dateStr = info.event.startStr;
            const eventTitle = info.event.title || (type === 'manual-block' ? 'Geblokkeerd' : 'Order');

            // --- Orders terug naar bak
            if (ordersContainer.contains(el) && type !== 'manual-block') {
                // Voeg DOM terug naar bak
                const div = document.createElement('div');
                div.classList.add('fc-event', 'bg-blue-500', 'text-white', 'cursor-move', 'p-2', 'mb-2');
                div.dataset.id = originalId;
                div.dataset.title = eventTitle;
                div.dataset.type = 'order';
                div.innerText = eventTitle;
                ordersContainer.appendChild(div);

                new Draggable(ordersContainer, {
                    itemSelector: '.fc-event',
                    eventData: el => ({
                        id: el.dataset.id,
                        title: el.dataset.title,
                        extendedProps: { originalId: el.dataset.id, type: 'order' }
                    }),
                    removeOnDrop: true
                });

                Livewire.dispatch('unplanOrder', { orderId: originalId });
                info.event.remove();
                return;
            }

            // --- Manual-block terug naar blocked container ---
            if (blockedContainer.contains(el) && type === 'manual-block') {
                info.event.remove();

                const div = document.createElement('div');
                div.classList.add('fc-event', 'bg-red-500', 'text-white', 'cursor-move', 'p-2', 'mb-2');
                div.dataset.id = originalId;
                div.dataset.title = eventTitle;
                div.dataset.type = 'manual-block';
                div.innerText = eventTitle;
                blockedContainer.appendChild(div);

                new Draggable(blockedContainer, {
                    itemSelector: '.fc-event',
                    eventData: el => ({
                        id: 'block-' + el.dataset.id,
                        title: el.dataset.title,
                        extendedProps: { type: 'manual-block', originalId: el.dataset.id, oldDate: dateStr }
                    }),
                    removeOnDrop: false
                });

                // Verwijder oude datum uit manualBlockedDates
                manualBlockedDates = manualBlockedDates.filter(d => d !== dateStr);
                Livewire.dispatch('removeBlockedDay', dateStr);
            }
        },
        // --- Styling van dagcellen ---
        dayCellDidMount: info => {
            const d = info.date;

            // --- Datum in dag-maand formaat ---
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const dateStr = `${day}-${month}`;
            const fullDateISO = `${d.getFullYear()}-${month}-${day}`;

            // --- Bereken load ---
            const load = getDayLoad(calendar, fullDateISO);
            const percent = Math.round((load / maxM2PerDay) * 100);

            // --- Achtergrond rood voor geblokkeerde weekdagen ---
            const dayName = d.toLocaleDateString('nl-NL', { weekday: 'long' }).toLowerCase();
            if (blockedWeekDays.includes(dayName)) {
                info.el.style.backgroundColor = '#dc3545';
            }

            // --- Zorg dat position relative staat zodat overlay kan ---
            info.el.style.position = 'relative';

            // --- Voeg padding-top toe zodat events niet over datum/m2 heen staan ---
            info.el.style.paddingTop = '15px'; // <-- hier de ruimte tussen datum/m² en events

            // --- Datum label overlay ---
            let dateLabel = info.el.querySelector('.day-date-label');
            if (!dateLabel) {
                dateLabel = document.createElement('div');
                dateLabel.classList.add('day-date-label');
                dateLabel.style.position = 'absolute';
                dateLabel.style.top = '2px';
                dateLabel.style.left = '2px';
                dateLabel.style.fontSize = '12px';
                dateLabel.style.fontWeight = '600';
                dateLabel.style.zIndex = '2'; // boven events
                info.el.appendChild(dateLabel);
            }
            dateLabel.innerText = dateStr;

            // --- Load indicator overlay ---
            let indicator = info.el.querySelector('.day-load-indicator');
            if (!indicator) {
                indicator = document.createElement('div');
                indicator.classList.add('day-load-indicator');
                indicator.style.position = 'absolute';
                indicator.style.top = '2px';
                indicator.style.right = '2px';
                indicator.style.fontSize = '11px';
                indicator.style.fontWeight = '600';
                indicator.style.zIndex = '2'; // boven events
                info.el.appendChild(indicator);
            }
            indicator.innerText = `${percent}%`;
            indicator.style.color = getLoadColor(percent);
        },
        // --- Event click (manual-block) ---
        eventClick: info => {
            if (info.event.extendedProps.type === 'manual-block') {
                const date = info.event.startStr;
                Livewire.dispatch('editBlockedDay', date);
            }
        },

        // --- Event styling ---
        eventDidMount: info => {

            // --- Achtergrondkleur ---
            const bgColor = window.getComputedStyle(info.el).backgroundColor;
            const textColor = getContrastColor(bgColor);

            function setEventTextContrast(el, color) {
                el.querySelectorAll('*').forEach(child => {
                    child.style.setProperty('color', color, 'important');
                });
                el.style.setProperty('color', color, 'important');
            }

            setEventTextContrast(info.el, textColor);

            // --- TYPE ---
            const type = info.event.extendedProps?.type || 'order';

            // =========================
            // 🟥 MANUAL BLOCK
            // =========================
            if (type === 'manual-block') {
                info.el.style.cursor = 'pointer';
                info.el.style.border = '2px solid #dc3545'; // zelfde rood
                info.el.style.borderRadius = '4px';
                return; // stop hier
            }

            // =========================
            // 🟦 ORDERS (BORDER LOGIC)
            // =========================
            const plannedM2 = parseFloat(info.event.extendedProps?.planned_m2) || 0;
            const totalM2 = parseFloat(info.event.extendedProps?.total_m2) || plannedM2;

            let borderStyle = 'none';

            if (plannedM2 > 0 && plannedM2 < totalM2) {
                // gedeeltelijk gepland
                borderStyle = '2px dashed rgba(0,0,0,0.6)';
            } else if (plannedM2 >= totalM2) {
                // volledig gepland
                borderStyle = '2px solid rgba(0,0,0,0.6)';
            }

            info.el.style.border = borderStyle;
            info.el.style.borderRadius = '4px';
        },
        eventAdd: () => updateDayIndicators(calendar),
        eventChange: () => updateDayIndicators(calendar),
        eventRemove: () => updateDayIndicators(calendar),
        datesSet: () => updateDayIndicators(calendar),
    });

    calendar.render();
    calendarInitialized = true;

    // --- Externe draggables init ---


    function updateDayIndicators(calendar) {
        document.querySelectorAll('.fc-daygrid-day').forEach(dayEl => {
            const dateStr = dayEl.getAttribute('data-date');
            if (!dateStr) return;

            const load = getDayLoad(calendar, dateStr);
            let percent = (load / maxM2PerDay) * 100;

            percent = Math.round(percent);

            let indicator = dayEl.querySelector('.day-load-indicator');
            if (!indicator) {
                indicator = document.createElement('div');
                indicator.classList.add('day-load-indicator');
                indicator.style.position = 'absolute';
                indicator.style.top = '4px';
                indicator.style.left = '4px';
                indicator.style.fontSize = '11px';
                indicator.style.fontWeight = '600';
                dayEl.style.position = 'relative';
                dayEl.appendChild(indicator);
            }

            indicator.innerText = `${load}/${maxM2PerDay} m²`;

            indicator.style.color = getLoadColor(percent); // dynamische kleur
        });
    }


    function initExternalDraggable() {
        if (draggablesInitialized) return;

        const ordersContainer = document.getElementById('external-orders-list');
        const blockedContainer = document.getElementById('blocked-days-list');

        if (ordersContainer) {
            new Draggable(ordersContainer, {
                itemSelector: '.fc-event',
                eventData: el => ({
                    id: el.dataset.id,
                    title: el.dataset.title,
                    extendedProps: { originalId: el.dataset.id, type: 'order' }
                }),
                removeOnDrop: true // heel belangrijk: verwijder automatisch uit bak
            });
        }

        if (blockedContainer) {
            new Draggable(blockedContainer, {
                itemSelector: '.fc-event',
                eventData: el => ({
                    id: 'block-' + el.dataset.id,
                    title: el.dataset.title,
                    color: el.dataset.color || 'red',
                    extendedProps: { type: 'manual-block', originalId: el.dataset.id }
                }),
                removeOnDrop: false
            });
        }

        draggablesInitialized = true;
    }

    initExternalDraggable();

    // --- Livewire updates orders ---
    Livewire.on('ordersUpdated', (payload) => {
        const currentEvents = calendar.getEvents() || [];

        fetch(calendarEl.dataset.eventsUrl || '/api/orders')
            .then(res => res.json())
            .then(events => {
                // 1️⃣ Verwijder events die niet meer in DB zitten
                currentEvents.forEach(ev => {
                    // behoud blocks
                    if (ev.id.startsWith('block-')) return;

                    // als event niet meer in DB (fetch) zit → remove
                    if (!events.find(e => String(e.id) === String(ev.id))) {
                        ev.remove();
                    }
                });

                // 2️⃣ Voeg nieuwe/ontbrekende events toe
                events.forEach(ev => {
                    const existing = calendar.getEventById(String(ev.id));
                    if (!existing) {
                        calendar.addEvent({
                            ...ev,
                            extendedProps: {
                                ...ev.extendedProps,
                                originalId: String(ev.id),
                                planned_m2: ev.extendedProps?.planned_m2 || 0
                            }
                        });
                    } else {
                        // update eventueel geplande m2 en title
                        existing.setExtendedProp('planned_m2', ev.extendedProps?.planned_m2 || 0);
                        existing.setProp('title', ev.title);
                    }
                });

                // 3️⃣ Update manual blocked dates
                manualBlockedDates = calendar.getEvents()
                    .filter(e => e.extendedProps.type === 'manual-block')
                    .map(b => b.startStr);
            });
    });






    function getLoadColor(percent) {
        if (percent >= 100) return '#dc3545';      // rood
        if (percent >= 75) return '#fd7e14';       // oranje
        return '#28a745';                           // groen
    }


    // --- Modals ---
    Livewire.on('showLimitModal', () => {
        const modalEl = document.getElementById('limitModal');
        modalEl.classList.add('flex');
        modalEl.classList.remove('hidden');
    });

    Livewire.on('showSettingModal', () => {
        const modalEl = document.getElementById('settingModal');
        modalEl.classList.add('flex');
        modalEl.classList.remove('hidden');
    });

    Livewire.on('hideLimitModal', () => {
        const modalEl = document.getElementById('limitModal');
        modalEl.classList.add('hidden');
        modalEl.classList.remove('flex');
    });

    Livewire.on('hideSettingModal', () => {
        const modalEl = document.getElementById('settingModal');
        modalEl.classList.add('hidden');
        modalEl.classList.remove('flex');
    });

    Livewire.on('blockedDatesUpdated', (blockedDates) => {
        if (!calendar) return;

        // forceer array
        blockedDates = Array.isArray(blockedDates) ? blockedDates : [blockedDates];

        // Verwijder oude blocked events


        // Voeg nieuwe blockedDates toe
        blockedDates.forEach(b => {
            const blockId = 'block-' + b.date;

            // check dat event nog niet bestaat
            if (!calendar.getEventById(blockId)) {
                calendar.addEvent({
                    id: blockId,
                    title: b.title,
                    start: b.date,
                    allDay: true,
                    backgroundColor: '#dc3545',
                    borderColor: '#dc3545',
                    extendedProps: { type: 'manual-block', originalId: blockId }
                });
            }
        });

        // update manualBlockedDates voor eventAllow
        manualBlockedDates = blockedDates.map(b => b.date);
    });



    Livewire.on('updateBlockedTitleLive', (payload) => {
        const { date, title } = payload[0];  // eerste element uit de array
        const event = calendar.getEventById('block-' + date);
        if (event) {
            event.setProp('title', title);
        }
    });
});

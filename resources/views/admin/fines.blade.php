@extends('layouts.sec')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/books/index.css') }}">

{{-- FullCalendar v6 global bundle includes styles in JS --}}
@endsection
@section('content')
<div class="container">
    <h3 class="mb-4">Fine Settings</h3>

    <div class="row">

        <!-- LEFT COLUMN : FINE SETTINGS -->
        <div class="col-md-6">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('fines.update') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Fine per Day (₱)</label>
                    <input type="number" step="0.01" name="fine_per_day"
                           class="form-control"
                           value="{{ $settings->fine_per_day ?? '' }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Maximum Fine (₱)</label>
                    <input type="number" step="0.01" name="max_fine"
                           class="form-control"
                           value="{{ $settings->max_fine ?? '' }}">
                </div>
                
                <div class="mb-3">
                    <label>Loan Duration (days)</label>
                    <input type="number" name="loan_duration_days"
                           value="{{ $settings->loan_duration_days }}"
                           class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Grace Period (Days)</label>
                    <input type="number" name="grace_period_days"
                           class="form-control"
                           value="{{ $settings->grace_period_days ?? 0 }}" required>
                </div>

                <button class="btn btn-primary">Save Fine Policy</button>
            </form>

            @if($settings)
                <p class="text-muted mt-3">
                    Effective since: {{ $settings->effective_from }}
                </p>
            @endif

        </div>


        <!-- RIGHT COLUMN : CALENDAR -->
        <div class="col-md-6">

            <h4>Holiday Calendar</h4>
            <div class="d-flex gap-2 mb-3">
                <button id="toggleCalendar" class="btn btn-secondary btn-sm">
                    Show Calendar
                </button>
            
                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#holidayListModal">
                    View Holidays
                </button>
            </div>


            <div id="calendarWrapper" style="display:none;">
                <div id="holiday-calendar"></div>
            </div>

        </div>

    </div>
</div>
<!-- Holiday Modal -->
<div class="modal fade" id="holidayModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Set Holiday</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <p id="holidayMessage"></p>

        <div id="holidayNameField">
            <label class="form-label">Holiday Name</label>
            <input type="text" id="holidayName" class="form-control">
        </div>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button id="confirmHolidayBtn" class="btn btn-primary">Confirm</button>
      </div>

    </div>
  </div>
</div>

<!-- Holiday List Modal -->
<div class="modal fade" id="holidayListModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Holiday List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- Month Filter (Scrollable) -->
                <div class="d-flex overflow-auto mb-3" id="monthTabs">
                    <button class="btn btn-outline-primary btn-sm me-2 month-tab" data-month="all">All</button>
                    <button class="btn btn-outline-primary btn-sm me-2 month-tab" data-month="0">Jan</button>
                    <button class="btn btn-outline-primary btn-sm me-2 month-tab" data-month="1">Feb</button>
                    <button class="btn btn-outline-primary btn-sm me-2 month-tab" data-month="2">Mar</button>
                    <button class="btn btn-outline-primary btn-sm me-2 month-tab" data-month="3">Apr</button>
                    <button class="btn btn-outline-primary btn-sm me-2 month-tab" data-month="4">May</button>
                    <button class="btn btn-outline-primary btn-sm me-2 month-tab" data-month="5">Jun</button>
                    <button class="btn btn-outline-primary btn-sm me-2 month-tab" data-month="6">Jul</button>
                    <button class="btn btn-outline-primary btn-sm me-2 month-tab" data-month="7">Aug</button>
                    <button class="btn btn-outline-primary btn-sm me-2 month-tab" data-month="8">Sep</button>
                    <button class="btn btn-outline-primary btn-sm me-2 month-tab" data-month="9">Oct</button>
                    <button class="btn btn-outline-primary btn-sm me-2 month-tab" data-month="10">Nov</button>
                    <button class="btn btn-outline-primary btn-sm month-tab" data-month="11">Dec</button>
                </div>

                <!-- Holiday List -->
                <ul class="list-group" id="holidayList"></ul>

            </div>

        </div>
    </div>
</div>
@endsection


@section('scripts')
<script src="{{ asset('vendor/fullcalendar/index.global.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const calendarEl = document.getElementById('holiday-calendar');

    let holidays = [];
    let selectedDate = null;
    let isHoliday = false;

    fetch('/holidays/list')
        .then(res => res.json())
        .then(data => {

            holidays = data.map(h => ({
                title: h.name || "Holiday",
                start: h.holiday_date,
                allDay: true,
                color: "red"
            }));

            initCalendar();
        });

    function initCalendar(){

        const calendar = new FullCalendar.Calendar(calendarEl, {

            initialView: 'dayGridMonth',
            height: 450,
            events: holidays,

            dateClick: function(info){

                selectedDate = info.dateStr;

                const event = calendar.getEvents().find(e => e.startStr === selectedDate);

                isHoliday = !!event;

                const modal = new bootstrap.Modal(document.getElementById('holidayModal'));

                if(isHoliday){

                    document.getElementById('holidayMessage').innerText =
                        "This date is already a holiday. Do you want to remove it?";

                    document.getElementById('holidayNameField').style.display = "none";

                }else{

                    document.getElementById('holidayMessage').innerText =
                        "Do you want to set this date as a holiday?";

                    document.getElementById('holidayNameField').style.display = "block";
                    document.getElementById('holidayName').value = "";

                }

                modal.show();

            }

        });

        calendar.render();
        
        const wrapper = document.getElementById('calendarWrapper');
        const toggleBtn = document.getElementById('toggleCalendar');
        
        toggleBtn.addEventListener('click', function () {
        
            if(wrapper.style.display === "none"){
                wrapper.style.display = "block";
                toggleBtn.textContent = "Hide Calendar";
            } else {
                wrapper.style.display = "none";
                toggleBtn.textContent = "Show Calendar";
            }
        
        });


        document.getElementById('confirmHolidayBtn').addEventListener('click', function(){

            const name = document.getElementById('holidayName').value;

            fetch('/holidays/toggle', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    date: selectedDate,
                    name: name
                })
            })
            .then(res => res.json())
            .then(data => {

                if(data.status === "added"){

                    calendar.addEvent({
                        title: name || "Holiday",
                        start: selectedDate,
                        color: "red"
                    });

                }

                if(data.status === "removed"){

                    calendar.getEvents().forEach(event => {
                        if(event.startStr === selectedDate){
                            event.remove();
                        }
                    });

                }

                bootstrap.Modal.getInstance(document.getElementById('holidayModal')).hide();

            });

        });

    }

});


/* -----------------------------
HOLIDAY LIST MODAL WITH MONTH FILTER
------------------------------*/

let allHolidays = [];
let currentMonth = "all";

const holidayListModal = document.getElementById('holidayListModal');

holidayListModal.addEventListener('show.bs.modal', function () {

    fetch('/holidays/all')
    .then(res => res.json())
    .then(data => {

        allHolidays = data;
        renderHolidayTable();

    });

});


function renderHolidayTable(){

    const list = document.getElementById('holidayList');
    list.innerHTML = "";

    let filtered = allHolidays;

    if(currentMonth !== "all"){
        filtered = allHolidays.filter(h => {
            let d = new Date(h.holiday_date);
            return d.getMonth() == currentMonth;
        });
    }

    if(filtered.length === 0){
        list.innerHTML = `
            <li class="list-group-item text-center text-muted">
                No holidays
            </li>
        `;
        return;
    }

    filtered.forEach(h => {

        let d = new Date(h.holiday_date);

        let formatted = d.toLocaleDateString('en-US',{
            month:'long',
            day:'numeric'
        });

        list.innerHTML += `
            <li class="list-group-item d-flex justify-content-between">
                <span>${formatted}</span>
                <span class="text-muted">${h.name ?? 'Holiday'}</span>
            </li>
        `;

    });

}

/* Month tab filter */

document.querySelectorAll(".month-tab").forEach(btn => {

    btn.addEventListener("click",function(){

        currentMonth = this.dataset.month;

        if(currentMonth !== "all"){
            currentMonth = parseInt(currentMonth);
        }

        renderHolidayTable();

    });

});

</script>
@endsection
@php
    Theme::layout('full-width');
    Theme::set('pageTitle', 'Booking');
@endphp

<section class="tp-booking-area pt-50 pb-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-9">
                <div class="booking-wrap" x-data="bookingWidget()" x-init="init()">

                    <div class="booking-head">
                        <button class="btn prev" @click="prevWeek()">Previous week</button>
                        <div class="week-label"><strong x-text="weekLabel"></strong></div>
                        <button class="btn next" @click="nextWeek()">Next week</button>
                    </div>

                    <div class="booking-days">
                        <template x-for="(d,idx) in days" :key="d.date">
                            <div class="booking-day" :class="{'active': idx===activeDay}" @click="activeDay = idx">
                                <div x-text="new Date(d.date).toLocaleDateString('en-US',{weekday:'short'})"></div>
                                <div x-text="new Date(d.date).toLocaleDateString('en-US')"></div>
                            </div>
                        </template>
                    </div>

                    <template x-if="slotsOfActiveDay.length && slotsOfActiveDay[0].closed && slotsOfActiveDay.length===1">
                        <div class="slot closed">Closed all day</div>
                    </template>

                    <div class="slots" x-show="!(slotsOfActiveDay.length && slotsOfActiveDay[0].closed && slotsOfActiveDay.length===1)">
                        <template x-for="s in slotsOfActiveDay" :key="s.start+s.end">
                            <div class="slot"
                                :class="{
                                    'unavailable': !s.available,
                                    'selected': isSelected(s),
                                    'closed': s.closed
                                }"
                                @click="selectSlot(s)">
                                <span x-text="s.closed ? 'Closed' : (s.start + ' - ' + s.end)"></span>
                            </div>
                        </template>
                    </div>

                    <div class="form" :class="{'show': selected}">
                        <h4>Booking Form</h4>
                        <div>Date: <span x-text="selectedDate"></span> | Time: <span x-text="selectedTime"></span></div>
                        <input type="text" placeholder="Name" x-model="form.name" required>
                        <input type="email" placeholder="Email" x-model="form.email">
                        <input type="text" placeholder="Phone" x-model="form.phone">
                        <button class="btn submit" @click="submit()">Submit booking</button>
                        <div x-text="message" style="margin-top:8px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .booking-wrap {max-width: 870px;margin: 20px auto;font-family: inherit;padding: 0 10px;}


    .booking-head {
        display:flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        flex-wrap: wrap;
        gap: 10px;
    }
    .booking-head .week-label {flex:1;text-align:center}

/
    @media(max-width: 600px) {
        .booking-head {
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        .booking-head .week-label {order:1;margin-bottom:6px}
        .booking-head .prev, .booking-head .next {
            order:2;
            width: 45%;
        }
        .booking-head {flex-wrap: nowrap;}
    }


    .booking-days {
        display:flex;
        justify-content:center;
        gap:12px;
        margin-bottom:15px;
        flex-wrap: wrap;
    }

    .booking-day {
        flex: 0 0 100px;
        padding:10px;
        border:1px solid #ddd;
        border-radius:10px;
        text-align:center;
        cursor:pointer;
        background:#fafafa;
    }
    .booking-day.active {background:#e8f7ff;border-color:#8bd1ff}


    .slots {
        display:grid;
        grid-template-columns:repeat(auto-fill,minmax(130px,1fr));
        gap:8px
    }
    .slot {
        padding:10px;
        border:1px solid #ddd;
        border-radius:10px;
        text-align:center;
        cursor:pointer;
        background:#f6fff6
    }
    .slot.unavailable {background:#f9f9f9;color:#bbb;cursor:not-allowed}
    .slot.selected {background:#ffe7e7;border-color:#ff8b8b}
    .slot.closed {background:#fbeaea;color:#d9534f;border-color:#d9534f;cursor:not-allowed}

    /* فرم */
    .form {
        margin-top:15px;
        border-top:1px dashed #ddd;
        padding-top:12px;
        display:none;
        direction:ltr;     
        text-align:left;   
    }
    .form.show {display:block}
    .form input {
        width:100%;
        padding:8px;
        border:1px solid #ddd;
        border-radius:8px;
        margin-bottom:8px;
        direction:ltr;
        text-align:left;
    }
    .form h4, .form div {text-align:left;direction:ltr;}


    .btn {padding:8px 12px;border:1px solid #ccc;border-radius:8px;background:#fff;cursor:pointer;white-space:nowrap}
    .submit {background:#bd844c;color:#fff;border-color:#bd844c}

    @media(max-width:600px){
        .slots {grid-template-columns:repeat(auto-fill,minmax(110px,1fr));}
        .booking-day {flex: 0 0 80px;padding:8px;font-size: 14px;}
    }
</style>

<script src="//unpkg.com/alpinejs" defer></script>

<script>
    function bookingWidget() {
        return {
            start: null,
            days: [],
            activeDay: 0,
            selected: null,
            form: {name:'',email:'',phone:''},
            message: '',
            get weekLabel() {
                if (!this.days.length) return '';
                const d1 = new Date(this.days[0].date);
                const d7 = new Date(this.days[6].date);
                return d1.toLocaleDateString('en-US') + ' - ' + d7.toLocaleDateString('en-US');
            },
            get slotsOfActiveDay() {
                return this.days[this.activeDay]?.slots ?? [];
            },
            get selectedDate(){ return this.days[this.activeDay]?.date ?? '' },
            get selectedTime(){ return this.selected ? (this.selected.start+' - '+this.selected.end) : '' },
            isSelected(s){ return this.selected && this.selected.start===s.start && this.selected.end===s.end },
            selectSlot(s){
                if (!s.available || s.closed) return;
                this.selected = s;
                this.message = '';
            },
            fetchSlots(){
                const url = new URL('{{ route('booking.slots') }}', window.location.origin);
                if (this.start) url.searchParams.set('start', this.start);
                fetch(url).then(r=>r.json()).then(j=>{
                    this.days = j.days;
                    this.activeDay = 0;
                    this.selected = null;
                });
            },
            nextWeek(){
                const base = this.start ? new Date(this.start) : new Date();
                base.setDate(base.getDate() + 7);
                this.start = base.toISOString().slice(0,10);
                this.fetchSlots();
            },
            prevWeek(){
                const base = this.start ? new Date(this.start) : new Date();
                base.setDate(base.getDate() - 7);
                this.start = base.toISOString().slice(0,10);
                this.fetchSlots();
            },
            submit(){
                if (!this.selected) { this.message='Please select a time slot.'; return; }
                const body = new FormData();
                body.append('name', this.form.name);
                body.append('email', this.form.email);
                body.append('phone', this.form.phone);
                body.append('date', this.selectedDate);
                body.append('start_time', this.selected.start);
                body.append('end_time', this.selected.end);

                fetch('{{ route('booking.reserve') }}', {
                    method:'POST',
                    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},
                    body
                })
                .then(async r=>({ok:r.ok, data: await r.json()}))
                .then(res=>{
                    if (!res.ok) { this.message = res.data.message || 'Error'; return; }
                    this.message = 'Booking request submitted. (Pending confirmation)';
                    this.fetchSlots();
                })
                .catch(()=> this.message='Connection error');
            },
            init(){ this.fetchSlots(); }
        }
    }
</script>

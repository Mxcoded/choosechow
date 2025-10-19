<div class="dashboard-card mb-4">
    <div class="card-header">
        <h5 class="card-title">Operating Hours 🕒</h5>
    </div>
    <div class="card-body">
        <p class="small text-muted mb-3">Define when your kitchen accepts orders.</p>

        @php
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            $currentHours = old('operating_hours', $profile->operating_hours ?? []);
        @endphp

        @foreach ($days as $day)
            @php
                $dayData = $currentHours[$day] ?? [];
                $isOpen = $dayData['is_open'] ?? false;
                $openTime = $dayData['open_time'] ?? '09:00';
                $closeTime = $dayData['close_time'] ?? '17:00';
            @endphp

            <div class="row mb-3 align-items-center border-bottom pb-2">
                <div class="col-4">
                    <div class="form-check">
                        <input class="form-check-input hours-toggle" type="checkbox" id="{{ $day }}_open"
                            name="operating_hours[{{ $day }}][is_open]" value="1"
                            {{ $isOpen ? 'checked' : '' }}>
                        <label class="form-check-label" for="{{ $day }}_open">
                            {{ ucfirst($day) }}
                        </label>
                    </div>
                </div>
                <div class="col-8">
                    <div class="row g-1 time-inputs">
                        <div class="col-5">
                            <input type="time"
                                class="form-control form-control-sm @error("operating_hours.$day.open_time") is-invalid @enderror"
                                name="operating_hours[{{ $day }}][open_time]" value="{{ $openTime }}"
                                {{ $isOpen ? '' : 'disabled' }}>
                        </div>
                        <div class="col-2 text-center">-</div>
                        <div class="col-5">
                            <input type="time"
                                class="form-control form-control-sm @error("operating_hours.$day.close_time") is-invalid @enderror"
                                name="operating_hours[{{ $day }}][close_time]" value="{{ $closeTime }}"
                                {{ $isOpen ? '' : 'disabled' }}>
                        </div>
                    </div>
                    @error("operating_hours.$day.close_time")
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        @endforeach

        <div class="mt-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="accepts_orders" name="accepts_orders" value="1"
                    {{ old('accepts_orders', $profile->accepts_orders ?? true) ? 'checked' : '' }}>
                <label class="form-check-label fw-bold" for="accepts_orders">
                    Currently accepting new orders
                </label>
            </div>
            <div class="form-text">Turn this off if you need a break or are fully booked.</div>
        </div>
    </div>
</div>

@section('scripts')
    @parent
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logic for enabling/disabling time inputs based on checkbox state
            document.querySelectorAll('.hours-toggle').forEach(toggle => {
                const timeInputs = toggle.closest('.row').querySelectorAll('input[type="time"]');

                // Initial state setup
                timeInputs.forEach(input => input.disabled = !toggle.checked);

                toggle.addEventListener('change', function() {
                    timeInputs.forEach(input => {
                        input.disabled = !this.checked;
                        // Clear values if disabled to avoid sending null/invalid times
                        if (input.disabled) {
                            input.value = '';
                        }
                    });
                });
            });
        });
    </script>
@endsection

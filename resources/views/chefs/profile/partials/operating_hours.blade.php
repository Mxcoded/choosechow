<div class="alert alert-info border-0 bg-info bg-opacity-10">
    <i class="fas fa-info-circle me-2"></i>
    Uncheck the box to mark a day as "Closed".
</div>

<div class="table-responsive">
    <table class="table align-middle">
        <thead class="bg-light">
            <tr>
                <th style="width: 150px;">Day</th>
                <th>Status</th>
                <th>Opening Time</th>
                <th>Closing Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                @php
                    $hours = $profile->operating_hours[$day] ?? ['open' => '09:00', 'close' => '20:00', 'closed' => false];
                    $isClosed = isset($hours['closed']) && filter_var($hours['closed'], FILTER_VALIDATE_BOOLEAN);
                @endphp
                <tr>
                    <td class="fw-bold text-capitalize">{{ $day }}</td>
                    <td>
                        <div class="form-check form-switch">
                            {{-- Hidden input to ensure 'closed' is sent as 'true' if checkbox is unchecked --}}
                            <input type="hidden" name="operating_hours[{{ $day }}][closed]" value="1">
                            <input class="form-check-input" type="checkbox" name="operating_hours[{{ $day }}][closed]" value="0"
                                   id="switch_{{ $day }}" 
                                   {{ !$isClosed ? 'checked' : '' }}
                                   onchange="toggleDay('{{ $day }}')">
                            <label class="form-check-label text-success" for="switch_{{ $day }}" id="label_{{ $day }}">
                                {{ !$isClosed ? 'Open' : 'Closed' }}
                            </label>
                        </div>
                    </td>
                    <td>
                        <input type="time" name="operating_hours[{{ $day }}][open]" class="form-control time-input-{{ $day }}" 
                               value="{{ $hours['open'] ?? '09:00' }}" {{ $isClosed ? 'disabled' : '' }}>
                    </td>
                    <td>
                        <input type="time" name="operating_hours[{{ $day }}][close]" class="form-control time-input-{{ $day }}" 
                               value="{{ $hours['close'] ?? '20:00' }}" {{ $isClosed ? 'disabled' : '' }}>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
function toggleDay(day) {
    const checkbox = document.getElementById('switch_' + day);
    const label = document.getElementById('label_' + day);
    const inputs = document.querySelectorAll('.time-input-' + day);
    
    if (checkbox.checked) {
        label.innerText = "Open";
        label.classList.remove('text-muted');
        label.classList.add('text-success');
        inputs.forEach(input => input.disabled = false);
    } else {
        label.innerText = "Closed";
        label.classList.remove('text-success');
        label.classList.add('text-muted');
        inputs.forEach(input => input.disabled = true);
    }
}
</script>
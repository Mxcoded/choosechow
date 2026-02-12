<div class="alert alert-info border-0 bg-info bg-opacity-10 mb-3">
    <i class="fas fa-clock me-2"></i>
    Set your store's opening hours. Toggle the switch to Open/Close a day.
</div>

<div class="table-responsive">
    <table class="table align-middle table-hover">
        <thead class="table-light">
            <tr>
                <th style="width: 120px;">Day</th>
                <th style="width: 100px;">Status</th>
                <th>Opens At</th>
                <th>Closes At</th>
            </tr>
        </thead>
        <tbody>
            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                @php
                    $hours = $profile->operating_hours[$day] ?? ['open' => '09:00', 'close' => '20:00', 'closed' => false];
                    // "Closed" is true if the key exists and is true
                    $isClosed = isset($hours['closed']) && filter_var($hours['closed'], FILTER_VALIDATE_BOOLEAN);
                @endphp
                <tr>
                    <td class="fw-bold text-capitalize text-muted">{{ $day }}</td>
                    <td>
                        <div class="form-check form-switch">
                            {{-- Default value for unchecked checkbox --}}
                            <input type="hidden" name="operating_hours[{{ $day }}][closed]" value="1">
                            
                            <input class="form-check-input" type="checkbox" name="operating_hours[{{ $day }}][closed]" value="0"
                                   id="switch_{{ $day }}" 
                                   {{ !$isClosed ? 'checked' : '' }}
                                   onchange="toggleDay('{{ $day }}')">
                        </div>
                    </td>
                    <td>
                        <input type="time" name="operating_hours[{{ $day }}][open]" 
                               class="form-control time-input-{{ $day }}" 
                               value="{{ $hours['open'] ?? '09:00' }}" 
                               {{ $isClosed ? 'disabled' : '' }}>
                    </td>
                    <td>
                        <input type="time" name="operating_hours[{{ $day }}][close]" 
                               class="form-control time-input-{{ $day }}" 
                               value="{{ $hours['close'] ?? '20:00' }}" 
                               {{ $isClosed ? 'disabled' : '' }}>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    function toggleDay(day) {
        const checkbox = document.getElementById('switch_' + day);
        const inputs = document.querySelectorAll('.time-input-' + day);
        
        // If checked (value 0) -> It is OPEN -> Enable inputs
        // If unchecked (value 1) -> It is CLOSED -> Disable inputs
        inputs.forEach(input => input.disabled = !checkbox.checked);
    }
</script>
<!-- Order Status Tracker Component -->
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-6">Order Status Tracker</h3>
    
    @php
        $statuses = [
            'approved'  => 'Approved', 
            'preparing' => 'Preparing', 
            'ready'     => 'Ready for Pickup', 
            'completed' => 'Completed'
        ];
        
        $currentStatus = strtolower($ticket->status ?? 'pending');
        
        // Normalize status variations to standard step levels
        $statusMap = [
            'pending staff review' => 0,
            'pending'              => 0,
            'under review'         => 0,
            'approved'             => 1,
            'approved for payment' => 1,
            'in production'        => 2,
            'preparing'            => 2,
            'ready for pickup'     => 3,
            'ready'                => 3,
            'completed'            => 4,
        ];
        
        $currentLevel = $statusMap[$currentStatus] ?? 0;
        
        $statusOrder = [
            'approved'  => 1,
            'preparing' => 2,
            'ready'     => 3,
            'completed' => 4,
        ];
    @endphp

    <div class="flex items-center justify-between relative px-2">
        <!-- Progress Line Background -->
        <div class="absolute left-6 right-6 top-1/2 transform -translate-y-1/2 h-1 bg-gray-200 z-0"></div>
        
        <!-- Progress Line Active Fill -->
        <div class="absolute left-6 top-1/2 transform -translate-y-1/2 h-1 bg-emerald-500 transition-all duration-500 z-0"
             style="width: calc({{ min(100, max(0, ($currentLevel - 1) * 33.33)) }}% - 12px);"></div>

        @foreach($statuses as $key => $label)
            @php $level = $statusOrder[$key]; @endphp
            <div class="flex flex-col items-center relative z-10">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300
                    {{ $currentLevel >= $level ? 'bg-emerald-600 text-white shadow-md ring-4 ring-emerald-50' : 'bg-gray-200 text-gray-500' }}">
                    @if($currentLevel > $level)
                        &#10003;
                    @else
                        {{ $loop->iteration }}
                    @endif
                </div>
                <span class="text-xs font-semibold mt-2.5 {{ $currentLevel >= $level ? 'text-emerald-700 font-bold' : 'text-gray-400' }}">
                    {{ $label }}
                </span>
            </div>
        @endforeach
    </div>
</div>
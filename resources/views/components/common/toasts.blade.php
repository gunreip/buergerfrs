{{-- Shared toast host. Rendering and actions remain owned by Flux. --}}
@persist('toast')
    <flux:toast.group position="top end" expanded>
        <flux:toast />
    </flux:toast.group>
@endpersist

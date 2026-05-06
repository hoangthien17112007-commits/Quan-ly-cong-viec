<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>

    <flux:toast position="top end" class="pt-24" />
    <script>
        window.addEventListener('play-success', () => {
            const audio = new Audio('/sounds/success.mp3');
            audio.play();
        });
        document.addEventListener('livewire:init', () => {
            Livewire.on('play-sound', (sound) => {
                const audio = new Audio(`/sounds/${sound}.mp3`);
                audio.play();
            });
        });
    </script>
</x-layouts::app.sidebar>

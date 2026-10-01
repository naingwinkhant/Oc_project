{{--
    Applied before first paint, so a night-mode visitor never sees a white flash.

    Kept as one component because it is needed by every layout: forgetting it in
    one of them is what left the sign-in pages stuck in day mode.
--}}
<script>
    (() => {
        // Unescaped on purpose: this is a JavaScript literal, not HTML, and
        // escaping it would turn "dark" into &quot;dark&quot;.
        const saved = {!! \App\Support\Theme::forCurrentUser() === \App\Support\Theme::SYSTEM ? 'null' : json_encode(\App\Support\Theme::forCurrentUser()) !!}
            || localStorage.getItem(@json(\App\Support\Theme::STORAGE_KEY));

        const dark = saved === 'dark'
            || ((!saved || saved === 'system') && window.matchMedia('(prefers-color-scheme: dark)').matches);

        document.documentElement.classList.toggle('dark', dark);
    })();
</script>
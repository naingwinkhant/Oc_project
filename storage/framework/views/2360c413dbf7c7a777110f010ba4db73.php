
<script>
    (() => {
        // Unescaped on purpose: this is a JavaScript literal, not HTML, and
        // escaping it would turn "dark" into &quot;dark&quot;.
        const saved = <?php echo \App\Support\Theme::forCurrentUser() === \App\Support\Theme::SYSTEM ? 'null' : json_encode(\App\Support\Theme::forCurrentUser()); ?>

            || localStorage.getItem(<?php echo json_encode(\App\Support\Theme::STORAGE_KEY, 15, 512) ?>);

        const dark = saved === 'dark'
            || ((!saved || saved === 'system') && window.matchMedia('(prefers-color-scheme: dark)').matches);

        document.documentElement.classList.toggle('dark', dark);
    })();
</script><?php /**PATH C:\Users\User\OneDrive\Desktop\Oc_project\resources\views/components/theme-script.blade.php ENDPATH**/ ?>